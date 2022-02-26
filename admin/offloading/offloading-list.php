<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Off_Loading_Order_List_Table extends WP_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'delete', //singular name of the listed records
            'plural' => 'deletes', //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'sup_name':
            case 'entry':
            case 'description':
            case 'qty':
            case 'pkgs':
            case 'cque':
            case 'contact_no':
            case 'container_name':
            case 'status':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID'),
            'sup_name' => __('Supplier Name'),
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'qty' => __('QTY'),
            'pkgs' => __('PKG'),
            'cque' => __('CQUE'),
            'contact_no' => __('Contact Number'),
            'container_name' => __('Container Name'),
            'status' => __('Action (<small>QTN Status, Order Status & Note</small>)'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array();
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb;
        $per_page = 10;
        $this->current_action();
        extract($_REQUEST);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $current_page = $this->get_pagenum();
        $start = ($current_page == 1) ? 0 : ($current_page - 1) * 10;

        $containers_name = !empty($containers_name) ? "container_name IN ('" . implode("', '", array_filter($containers_name)) . "')" : 1;
        $qid = !empty($qid) ? " (quotation_id like  '%{$qid}%' OR revised_no like  '%{$qid}%')" : 1;
        $entry = !empty($entry) ? " (entry like '%{$entry}%') " : 1;
        $client_id = !empty($client_id) ? " client_id ='{$client_id}'" : 1;
        $arrival_date = !empty($arrival_date) ? " arrival_date ='{$arrival_date}'" : 1;

        $where = "WHERE cl_status=1 AND $qid AND $entry AND $client_id AND $arrival_date AND $containers_name AND order_registry='TRANSIT'";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta $where LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;

        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $client = get_client($value->client_id);
                $supplier_name = get_supplier($value->sup_code, 'name');
                $tr_selected = $value->order_registry == 'TRANSIT' ? 'selected' : '';
                $ar_selected = $value->order_registry == 'ARRIVED' ? 'selected' : '';
                $olc_selected = $value->ol_status == 'Complete' ? 'selected' : '';
                $olic_selected = $value->ol_status == 'Incomplete' ? 'selected' : '';
                $disabled = $value->ol_list_status ? 'disabled' : '';
                $required = $value->order_registry == 'TRANSIT' ? 'required' : '';

                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['sup_name'] = $supplier_name;
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['qty'] = $value->quantity;
                $listArr[$i]['pkgs'] = $value->cl_pkgs;
                $listArr[$i]['cque'] = (!empty($client->name) ? $client->name : '') . ' ' . (!empty($value->revised_no) ? $value->revised_no : $value->quotation_id);
                $listArr[$i]['contact_no'] = (!empty($client->phone) ? $client->phone : '') . (!empty($client->phone2) ? ', ' . $client->phone2 : '');
                $listArr[$i]['container_name'] = "<a href='admin.php?page=offloading-preview&container_name={$value->container_name}' target='_blank'>$value->container_name</a>";

                $checked = $value->ol_add_list ? 'checked="checked"' : '';

                $form = "<form method=post><br/><table class='tbl-offloading'>"
                        . "<tr><td>QTN Status: <select name=ol_status required style='width:100px' $disabled>"
                        . "<option value=''>QTN Status</option><option value='Complete' $olc_selected>Complete</option><option value='Incomplete' $olic_selected>Incomplete</option></select></td>"
                        . "<td><br/><label class='button-secondary'>"
                        . "<input type=checkbox $checked $disabled class=ol_add_list name='ol[{$value->id}][po_id]' value='{$value->id}' /> Add</label></td></tr>"
                        . "<tr><td colspan=2><input type=text name=ol_note value='$value->ol_note' style='width: 100%;' placeholder=Note $disabled /></td></tr>"
                        . "<tr><td>Departure Date: <input type=date name='departure_date' value='$value->departure_date' required /></td>"
                        . "<td>Arrival Date: <input type=date name='arrival_date' value='$value->arrival_date' $required /></tr>"
                        . "<tr><td>Order Status: <select name=status required style='width:100%'>"
                        . "<option value=''>Order Status</option><option value='TRANSIT' $tr_selected>TRANSIT</option><option value='ARRIVED' $ar_selected>ARRIVED</option></select></td>"
                        . "<td><br/><input type='submit' name=order_registry value=Update  class='button-primary btn-small' ></td></tr>"
                        . "</table>"
                        . "<input type=hidden name=po_id value=$value->id />"
                        . "<input type=hidden name=client_id value=$value->client_id />"
                        . "<input type=hidden name=is_showroom value=$value->is_showroom />"
                        . "<br/></form>";
                $listArr[$i]['status'] = $form;
                //ol_list_status


                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'company_name';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotation_po_meta $where");
        //$count = $num_rows; //count($rows);
        //usort($data, 'usort_reorder');
        $total_items = $count; //count($data);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}

function admin_ctm_offloading_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    if (!empty($postdata['order_registry'])) {
        $po_id = !empty($postdata['po_id']) ? $postdata['po_id'] : 0;
        $status = !empty($postdata['status']) ? $postdata['status'] : '';
        $arrival_date = !empty($postdata['arrival_date']) ? $postdata['arrival_date'] : '';
        $departure_date = !empty($postdata['departure_date']) ? $postdata['departure_date'] : '';
        $ol_status = !empty($postdata['ol_status']) ? $postdata['ol_status'] : '';
        $ol_note = !empty($postdata['ol_note']) ? $postdata['ol_note'] : '';
        $stk_inv_status = !empty($postdata['client_id']) && $postdata['client_id'] == FLAGSHIP_ID ? 'AVAILABLE' : 'RESERVED';
        if ($status == 'ARRIVED') {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='$status', arrival_date='{$arrival_date}', stk_inv_status='$stk_inv_status', stk_inv_location='WH' where id='$po_id'");
        } else {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='$status',arrival_date='{$arrival_date}', departure_date='{$departure_date}', ol_status='{$ol_status}', ol_note='{$ol_note}' where  id='$po_id'");
        }
    }
    if (!empty($postdata['bulk_update'])) {
        if ($postdata['order_registry'] == 'ARRIVED') {
            $flagship = FLAGSHIP_ID;
            //STOCK STATUS RESERVED
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET arrival_date='{$postdata['arrival_date']}', departure_date='{$postdata['departure_date']}', order_registry='{$postdata['order_registry']}', stk_inv_status='RESERVED', stk_inv_location='WH' where  container_name='{$postdata['container_name']}' AND client_id != $flagship");
            
             //STOCK STATUS AVAILABLE
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET arrival_date='{$postdata['arrival_date']}', departure_date='{$postdata['departure_date']}', order_registry='{$postdata['order_registry']}',  stk_inv_status='AVAILABLE', stk_inv_location='WH' where container_name='{$postdata['container_name']}' AND client_id = $flagship");
        } else {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET arrival_date='{$postdata['arrival_date']}', departure_date='{$postdata['departure_date']}', order_registry='{$postdata['order_registry']}' where container_name='{$postdata['container_name']}'");
        }
    }
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $arrival_date = !empty($getdata['arrival_date']) ? $getdata['arrival_date'] : '';
    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $containers_name = !empty($getdata['containers_name']) ? array_filter($getdata['containers_name']) : [];

    $containers = $wpdb->get_results("SELECT container_name FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name!='' GROUP BY container_name");
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th{white-space: nowrap;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        #the-list .tbl-offloading tr td{padding:3px 5px!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Offloading List</h1>
        <a href="admin.php?page=offloading-preview" class="page-title-action" target="_blank">Preview Offloading List</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div></div>
                        <div class="postbox" style="background:#f1f1f1">
                            <form id="filter-form1" method="post">
                                <input type="hidden" name="page" value="<?= $page ?>" />
                                <table class="form-table">
                                    <tr>
                                        <td colspan="3"><strong>Bulk Update</strong></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="container_name" required>
                                                <option value="">Select Container</option>
                                                <?php
                                                foreach ($containers as $value) {
                                                    echo "<option value='{$value->container_name}'>{$value->container_name}</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="departure_date" class="search-input" placeholder="Departure Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" required />
                                        </td>
                                        <td>
                                            <input type="text" name="arrival_date" class="search-input" placeholder="Arrival Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name=order_registry required>
                                                <option value=''>Order Status</option>
                                                <option value='TRANSIT'>TRANSIT</option>
                                                <option value='ARRIVED'>ARRIVED</option>
                                            </select>
                                        </td>
                                        <td colspan="2">
                                            <button type="submit" class="button-primary" name="bulk_update" value="bulk_update" 
                                                    onclick='return confirm(`are you sure you want to update?`)' >Update</button>
                                            &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                        </td>
                                    </tr>
                                </table>
                            </form><br/>
                        </div>
                        <div class="postbox">
                            <div class="inside">
                                <form id="filter-form1" method="get">
                                    <input type="hidden" name="page" value="<?= $page ?>" />
                                    <table class="form-table">
                                        <tr>
                                            <td colspan="3"><strong>Filters</strong></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select name="containers_name[]" class="chosen-select" multiple="true">
                                                    <option value="">Select Container</option>
                                                    <?php
                                                    foreach ($containers as $value) {
                                                        $selected = in_array($value->container_name, $containers_name) ? 'selected' : '';
                                                        echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="qid" class="search-input" value="<?= $qid ?>" placeholder="Search by QTN"  >
                                            </td>
                                            <td>
                                                <input type="text" name="entry" class="search-input" value="<?= $entry ?>" placeholder="Search By Entry#"  >
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td>
                                                <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                    <option value="">Loading...</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="<?= $arrival_date ? 'date' : 'text' ?>" name="arrival_date" class="search-input" value="<?= $arrival_date ?>" placeholder="Search By Arrival Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"  >
                                            </td>
                                            <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>&nbsp;&nbsp;
                                                <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                <?php
                                //Create an instance of our package class...
                                $testListTable = new CTM_Off_Loading_Order_List_Table();
                                //Fetch, prepare, sort, and filter our data...
                                $testListTable->prepare_items();
                                $testListTable->display();
                                ?>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>

    <?php
    $roles = rb_get_current_user_roles();
    if (in_array('accounts', $roles) || in_array('logistics', $roles)) {
        ?>
        <script>
            jQuery(document).ready(() => {
                jQuery(".wp-list-table input,.wp-list-table select,.wp-list-table textarea").prop("disabled", true);
            });
        </script>
    <?php } ?>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('.chosen-select').chosen();
                }
            });

            jQuery('.ol_add_list').click(function () {
                var po_id = jQuery(this).val();
                var status = 0;
                if (jQuery(this).is(':checked')) {
                    status = 1;
                } else {
                    status = 0;
                }
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/offloading-list.php",
                    type: "post",
                    dataType: "json",
                    data: {po_id: po_id, status: status},
                    success: function (response) {
                        if (response.status) {
                            alert(`Item ${response.status} to offloading list successfully`);
                        }
                    }
                });

            });
        });
    </script>
    <?php
}
