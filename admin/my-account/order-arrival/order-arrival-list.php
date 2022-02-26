<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Order_Arrival_List_Table extends WP_List_Table {

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
            case 'qtn':
            case 'type':
            case 'client_name':
            case 'status':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'qtn' => __('QTN'),
            'type' => __('Type'),
            'client_name' => __('Client Name'),
            'status' => __('Status'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'qtn' => array('qtn', true),
        );
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

        $qid = !empty($qid) ? "(quotation_id like '%{$qid}%' OR revised_no like '%{$qid}%') " : 1;
        $client_id = !empty($client_id) ? " client_id like '%{$client_id}%' " : 1;
        $qtn_type = !empty($qtn_type) ? " qtn_type like '%{$qtn_type}%' " : 1;
        $status = !empty($status) ? " status = '{$status}' " : 1;

        $where = "WHERE {$qid} AND {$client_id} AND {$qtn_type} AND $status";
        $rs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_order_arrival $where  ORDER BY quotation_id DESC LIMIT $start, 10");
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['qtn'] = $value->revised_no ? $value->revised_no : $value->quotation_id;
                $listArr[$i]['type'] = get_qtn_type($value->quotation_id);
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['status'] = show_order_arrival_status($value->status);

                $type = $value->status == 'Complete' ? 'confirm-order-arrival' : 'partial-order-arrival';
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page=$type&id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>";

                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'collection_name';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotation_order_arrival $where"); //count($rows);
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

function admin_order_arival_list_page() {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $getdata = filter_input_array(INPUT_GET);

    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $qtn_type = !empty($getdata['qtn_type']) ? $getdata['qtn_type'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : '';

    $quotation_id = $wpdb->get_var("SELECT quotation_id FROM {$wpdb->prefix}ctm_quotation_order_arrival ORDER BY quotation_id DESC LIMIT 1");
    $rs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE quotation_id>" . ($quotation_id ? $quotation_id : 1) . " GROUP BY quotation_id ORDER BY quotation_id ASC LIMIT 0, 100");
    foreach ($rs as $value) {
        $data = ['quotation_id' => $value->quotation_id, 'revised_no' => get_revised_no($value->quotation_id), 'qtn_type' => get_qtn_type($value->quotation_id),
            'client_id' => $value->client_id, 'status' => check_quotation_confirm_partial_status($value->quotation_id), 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID,
            'created_at' => $date, 'updated_at' => $date,];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_order_arrival", $data, wpdb_data_format($data));
    }


    //Create an instance of our package class...
    $testListTable = new CTM_Order_Arrival_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], 
        #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], 
        #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], 
        #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th,.wp-list-table tr td{white-space: nowrap;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">CONFIRM / PARTIAL ORDER ARRIVAL REGISTRY</h1>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <div id="page-inner-content">
                                    <form id="filter-form1" method="get">
                                        <input type=hidden name="page" value="<?= $getdata['page'] ?>" />
                                        <table class="form-table">
                                            <tr>


                                                <td>
                                                    <input type=text name="qid" value="<?= $qid ?>" class="search-input" placeholder="Search by QTN No" >
                                                </td>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="qtn_type" onchange="this.form.submit()">
                                                        <option value="">Select Type</option>
                                                        <option value="Stock" <?= $qtn_type == 'Stock' ? 'selected' : '' ?>>Stock</option>
                                                        <option value="Order" <?= $qtn_type == 'Order' ? 'selected' : '' ?>>Order</option>
                                                        <option value="Project" <?= $qtn_type == 'Project' ? 'selected' : '' ?>>Project</option>
                                                    </select>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <select  name="status" class="search-input" onchange="this.form.submit()">
                                                        <option value="">Select Status</option>
                                                        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                        <option value="Partial" <?= $status == 'Partial' ? 'selected' : '' ?>>Partial</option>
                                                    </select>
                                                </td>
                                                <td colspan="2"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    <!-- Now we can render the completed list table -->
                                    <?php $testListTable->display(); ?>
                                </div>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(function () {
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
?>
