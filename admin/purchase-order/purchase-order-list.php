<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Quotation_Uurchase_Order_List_Table extends WP_List_Table {

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
            case 'client_name':
            case 'qtn':
            case 'type':
            case 'supplier':
            case 'status':
            case 'updated_at':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('PO ID'),
            'client_name' => __('Client Name'),
            'qtn' => __('QTN ID'),
            'type' => __('QTN Type'),
            'supplier' => __('Supplier'),
            'status' => __('Status'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
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
        $qid = !empty($qid) ? " (t1.quotation_id like  '%" . $qid . "%' OR t1.revised_no like  '%" . $qid . "%') " : 1;
        $client_id = !empty($client_id) ? " t2.client_id ={$client_id}" : 1;
        $po_id = !empty($po_id) ? " t1.id ={$po_id}" : 1;
        $po_date = !empty($po_date) ? " t1.created_at like '%{$po_date}%'" : 1;
        $from_date = !empty($from_date) ? "t1.created_at>='{$from_date} 00:00:01'" : 1;
        $to_date = !empty($to_date) ? "t1.created_at<='{$to_date} 23:59:59'" : 1;
        $qtn_scope = !empty($qtn_scope) ? " t2.scope ='{$qtn_scope}'" : 1;
        $qtn_type = !empty($qtn_type) ? " t2.type ='{$qtn_type}'" : 1;
        $status = !empty($status) ? " t1.status ='{$status}'" : " t1.status !='Hide'";


        $where = "WHERE $po_id AND $qid AND $client_id AND $po_date AND $from_date AND $to_date AND $qtn_scope AND $qtn_type AND $status";
        $rs = $wpdb->get_results("SELECT t1.id,t1.quotation_id,t1.revised_no,t1.client_id,t1.sup_code,t1.status,t1.updated_at,t2.type,t2.vat,t2.scope FROM {$wpdb->prefix}ctm_quotation_po t1 LEFT JOIN {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id=t2.id  $where ORDER BY t1.id DESC LIMIT $start, 10");
        $listArr = array();
        $i = 0;

        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $qtn_type = get_quotation($value->quotation_id, 'type');
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['qtn'] = !empty($value->revised_no) ? $value->revised_no : $value->quotation_id;
                $listArr[$i]['type'] = $qtn_type;
                $listArr[$i]['supplier'] = $value->sup_code;
                $listArr[$i]['status'] = show_purchase_order_status($value->status);
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $type = $qtn_type == 'Project' ? '&type=Project' : '';
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}-items&id={$value->id}{$type}' class='btn-view'  title='View'><img alt='' src='{$asset_url}view.png'></a><br/>";
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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quotation_po t1 LEFT JOIN {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id=t2.id $where");
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

function admin_ctm_purchase_order_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $po_id = !empty($getdata['po_id']) ? $getdata['po_id'] : '';
    $po_date = !empty($getdata['po_date']) ? $getdata['po_date'] : '';
    $qtn_type = !empty($getdata['qtn_type']) ? $getdata['qtn_type'] : '';
    $qtn_scope = !empty($getdata['qtn_scope']) ? $getdata['qtn_scope'] : '';
    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : '';
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : '';
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th,.wp-list-table tr td{white-space: nowrap;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Purchase Order</h1>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <div id="page-inner-content" class="postbox">
                            <br/>
                            <div class="inside">
                                <div id="page-inner-content1">
                                    <form id="filter-form1" method="get">
                                        <input type="hidden" name="page" value="<?= $page ?>" />
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <input type="text" name="po_id" class="search-input" value="<?= $po_id ?>" placeholder="Search by PO ID."  >
                                                </td>
                                                <td>
                                                    <input type="text" name="qid" class="search-input" value="<?= $qid ?>" placeholder="Search by QTN ID."  >
                                                </td>
                                                <td>
                                                    <select name="qtn_type" onchange="this.form.submit()">
                                                        <option value="">Select Type</option>
                                                        <option value="Order" <?= $qtn_type == 'Order' ? 'selected' : '' ?>>Order</option>
                                                        <option value="Project" <?= $qtn_type == 'Project' ? 'selected' : '' ?>>Project</option>
                                                    </select>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="qtn_scope" onchange="this.form.submit()">
                                                        <option value="">Select Scope</option>
                                                        <option value="Local" <?= $qtn_scope == 'Local' ? 'selected' : '' ?> >Local</option>
                                                        <option value="Export" <?= $qtn_scope == 'Export' ? 'selected' : '' ?> >Export</option>
                                                        <option value="Promotion" <?= $qtn_scope == 'Promotion' ? 'selected' : '' ?> >Promotion</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="<?= $po_date ? 'date' : 'text' ?>" name="po_date" class="search-input" value="<?= $po_date ?>" placeholder="Search By PO Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')">
                                                </td>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="<?= $from_date ? 'date' : 'text' ?>" name="from_date" class="search-input" value="<?= $from_date ?>" placeholder="From Created Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td>
                                                    <input type="<?= $to_date ? 'date' : 'text' ?>" name="to_date" class="search-input" value="<?= $to_date ?>" placeholder="To Created Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td>
                                                    <select  name="status" class="search-input" onchange="this.form.submit()">
                                                        <option value="">Select Status</option>
                                                        <?php
                                                        foreach (PO_STATUS as $value) {
                                                            echo "<option value='$value' " . ($status == $value ? 'selected' : '') . " >$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                </tr>
                                            <tr>
                                                <td colspan="3"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;
                                                    <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form id="deletes-filter" method="get">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $page ?>" />
                                        <!-- Now we can render the completed list table -->
                                        <?php
                                        //Create an instance of our package class...
                                        $testListTable = new CTM_Quotation_Uurchase_Order_List_Table();
                                        //Fetch, prepare, sort, and filter our data...
                                        $testListTable->prepare_items();
                                        $testListTable->display();
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
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

        });
    </script>
    <?php
}
