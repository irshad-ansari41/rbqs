<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Client_Order_Status_List_Table extends WP_List_Table {

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
            case 'image':
            case 'entry':
            case 'description':
            case 'qty':
            case 'customer_name':
            case 'item_status':
            case 'dispatch_date':
            case 'arrival_date':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'image' => __('Image'),
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'qty' => __('QTY'),
            'customer_name' => __('Customer Name'),
            'item_status' => __('Item Status'),
            'dispatch_date' => __('Dispatch Date'),
            'arrival_date' => __('Arrival Date'),
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
        extract($_GET);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $current_page = $this->get_pagenum();
        $start = ($current_page == 1) ? 0 : ($current_page - 1) * 10;

        $po_meta_id = !empty($po_meta_id) ? " t1.id like '%" . $po_meta_id . "%' " : 1;
        $qid = !empty($qid) ? " (t1.quotation_id like  '%" . $qid . "%' or  t1.revised_no like  '%" . $qid . "%') " : 1;
        $client_id = !empty($client_id) ? " t1.client_id ='{$client_id}'" : 1;
        $po_date = !empty($from_date) && !empty($to_date) ? " po_date>='$from_date' AND po_date<='$to_date' " : 1;

        $where = "WHERE t1.order_registry='ARRIVED' AND $po_meta_id AND $qid AND $client_id AND $po_date ";
        $sql = "SELECT t1.id,t1.quotation_id,t1.client_id,t1.item_id,t1.entry,t1.item_desc,t1.quantity,t1.order_registry,t1.arrival_date,t1.dispatch_date,t1.entry, t1.cl_pkgs,t1.stk_inv_location,t1.stk_inv_status,t1.stk_inv_comment,t1.revised_no, t1.receipt_no FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ORDER BY CAST(t1.entry AS UNSIGNED INTEGER)  DESC LIMIT $start, 10";

        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $client = get_client($value->client_id);
                $client_name = !empty($client->name) ? $client->name : '';
                $item = get_item($value->item_id);

                $listArr[$i]['image'] = "<a href='" . get_image_src($item->image) . "' target='_image'><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></a>";
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['qty'] = $value->quantity;
                $listArr[$i]['customer_name'] = $client_name;
                $listArr[$i]['item_status'] = show_order_tracker_status($value->order_registry);
                $listArr[$i]['dispatch_date'] = rb_date($value->dispatch_date, 'd-M-Y');
                $listArr[$i]['arrival_date'] = rb_date($value->arrival_date, 'd-M-Y');

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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quotation_po_meta t1  LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ");

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

function admin_ctm_client_order_status_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : '';
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : '';
    $client_id = !empty($getdata['client_id']) ? $getdata['client_id'] : '';

    $quotations = $wpdb->get_results("SELECT id, revised_no from {$wpdb->prefix}ctm_quotations where client_id='{$client_id}' AND con_res_date IS NOT NULL");
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th{white-space: nowrap;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table.wp-list-table th {text-align: center;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Client Order Status</h1>
        <a  href='<?= admin_url("admin.php?page=stock-inventory") ?>' class="page-title-action" >Back</a>
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
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Search By Client</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="qid" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Search By Quotation</option>
                                                        <?php
                                                        foreach ($quotations as $value) {
                                                            $selected = $value->id == $qid ? 'selected' : '';
                                                            echo "<option value='{$value->id}' $selected>" . (!empty($value->revised_no) ? $value->revised_no : $value->id) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td style="width:170px">
                                                    <input type="<?= $from_date ? 'date' : 'text' ?>" name="from_date" class="search-input" value="<?= $from_date ?>" placeholder="From PO Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td style="width:170px">
                                                    <input type="<?= $to_date ? 'date' : 'text' ?>" name="to_date" class="search-input" value="<?= $to_date ?>" placeholder="To PO Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>


                                    <?php
                                    //Create an instance of our package class...
                                    $testListTable = new CTM_Client_Order_Status_List_Table();
                                    //Fetch, prepare, sort, and filter our data...
                                    $testListTable->prepare_items();
                                    $testListTable->display();
                                    ?>

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

            jQuery('.chosen-select').chosen();
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Search By Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });

        });
    </script>
    <?php
}
