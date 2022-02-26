<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Delivery_Note_List_Table extends WP_List_Table {

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
            case 'qtn':
            case 'type':
            //case 'total_amount':
            //case 'paid_amount':
            //case 'balance_amount':
            case 'client_name':
            case 'delivery_date':
            case 'status':
            case 'updated_by':
            case 'updated_at':
            case 'packaging_label':
            case 'packaging_list':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('DN No'),
            'qtn' => __('QTN'),
            'type' => __('Type'),
            //'total_amount' => __('Total Amount'),
            //'paid_amount' => __('Paid Amount'),
            //'balance_amount' => __('Balance Amount'),
            'client_name' => __('Client Name'),
            'delivery_date' => __('Delivery Date'),
            'status' => __('Status'),
            'updated_by' => __('Updated By'),
            'updated_at' => __('Updated At'),
            'packaging_label' => __('Packaging Label'),
            'packaging_list' => __('Packaging List'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'dn_no' => array('dn_no', true),
            'updated_at' => array('updated_at', true),
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

        $dn = !empty($dn) ? "id = '{$dn}' " : 1;
        $qid = !empty($qid) ? "(quotation_id like '%{$qid}%' OR revised_no like '%{$qid}%') " : 1;
        $client_id = !empty($client_id) ? " client_id like '%{$client_id}%' " : 1;
        $qtn_type = !empty($qtn_type) ? " qtn_type like '%{$qtn_type}%' " : 1;
        $delivery_date = !empty($delivery_date) ? " delivery_date like '%{$delivery_date}%' " : 1;
        $status = !empty($status) ? " status = '{$status}' " : 1;

        $where = "WHERE {$dn} AND {$qid} AND {$client_id} AND {$qtn_type} AND {$delivery_date} AND $status";
        $rs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn $where ORDER BY id DESC LIMIT $start, 10");
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $has_export = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_dn_meta WHERE dn_id='{$value->id}' AND export_packing=1");
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['qtn'] = $value->revised_no ? $value->revised_no : $value->quotation_id;
                $listArr[$i]['type'] = $value->qtn_type;
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['delivery_date'] = !empty($value->delivery_date) ? rb_date($value->delivery_date) : "<span class='text-red'>NOT SCHEDULED</span>";
                $listArr[$i]['status'] = show_dn_status($value->status);
                $listArr[$i]['updated_by'] = !empty($value->delivery_date) ? get_user($value->updated_by, 'display_name') : '';
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['packaging_label'] = "<a href='" . admin_url() . "admin.php?page=packaging-label&dn_id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>";
                $listArr[$i]['packaging_list'] = "<a href='" . admin_url() . "admin.php?page=packaging-list&dn_id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>";
                $type = $value->qtn_type == 'Stock' ? 'stock-delivery-note' : ($value->qtn_type == 'Project' ? 'project-delivery-note' : 'order-delivery-note');
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page=$type&dn_id={$value->id}' class='btn-view' title='View' >"
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotation_dn $where"); //count($rows);
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

function admin_ctm_sp_delivery_note_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $dn = !empty($getdata['dn']) ? $getdata['dn'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $delivery_date = !empty($getdata['delivery_date']) ? $getdata['delivery_date'] : '';
    $qtn_type = !empty($getdata['qtn_type']) ? $getdata['qtn_type'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : '';

    //Create an instance of our package class...
    $testListTable = new CTM_Delivery_Note_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();

    //if (!get_option('update_tax_invoice', 1)) {
//    $dns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn");
//    foreach ($dns as $v) {
//        if (!empty($v->quotation_id) && $v->qtn_type == 'Order') {
//            create_tax_invoice_order($v->quotation_id, $v->id);
//        }
//        create_tax_invoice_stock($v->quotation_id, $v->id);
//        create_tax_invoice_project($v->quotation_id, $v->id);
//    }
//    update_option('update_tax_invoice', 0);
    //}
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
        <h1 class="wp-heading-inline">DELIVERY NOTE REGISTRY</h1>
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
                                                    <input type=text name="dn" value="<?= $dn ?>" class="search-input" placeholder="Search by DN No" >
                                                </td>
                                                <td>
                                                    <input type=text name="qid" value="<?= $qid ?>" class="search-input" placeholder="Search by QTN No" >
                                                </td>
                                                <td>
                                                    <input type="<?= $delivery_date ? 'date' : 'text' ?>" name="delivery_date" value="<?= $delivery_date ?>"  class="search-input" placeholder="Search By Delivery Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>

                                            </tr>
                                            <tr>
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
                                                <td>
                                                    <select  name="status" class="search-input" onchange="this.form.submit()">
                                                        <option value="">Select Status</option>
                                                        <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="DELIVERED" <?= $status == 'DELIVERED' ? 'selected' : '' ?>>DELIVERED</option>
                                                        <option value="CANCELLED" <?= $status == 'CANCELLED' ? 'selected' : '' ?>>CANCELLED</option>
                                                        <option value="RETURNED" <?= $status == 'RETURNED' ? 'selected' : '' ?>>RETURNED</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
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
