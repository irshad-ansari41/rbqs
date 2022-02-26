<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Project_Job_Order_List_Table extends WP_List_Table {

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
            case 'pjo_no':
            case 'qtn':
            case 'qtn_type':
            case 'type':
            case 'client_name':
            case 'start_date':
            case 'status':
            case 'updated_by':
            case 'updated_at':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'pjo_no' => __('PJO No'),
            'qtn' => __('QTN'),
            'qtn_type' => __('QTN Type'),
            'type' => __('Type'),
            'client_name' => __('Client Name'),
            'start_date' => __('PJO Date'),
            'status' => __('Status'),
            'updated_by' => __('Created By'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'pjo_no' => array('pjo_no', true),
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

        $pjo_id = !empty($pjo_id) ? "t1.id = '{$pjo_id}' " : 1;
        $qid = !empty($qid) ? "t1.quotation_id like '%{$qid}%' " : 1;
        $qtn_type = !empty($qtn_type) ? "t1.qtn_type like '%{$qtn_type}%' " : 1;
        $client_id = !empty($client_id) ? " t1.client_id like '%{$client_id}%' " : 1;
        $start_date = !empty($start_date) ? " t2.start_date like '%{$start_date}%' " : 1;

        $where = "WHERE {$pjo_id} AND {$qid} AND $qtn_type AND {$client_id} AND {$start_date}";
        $results = $wpdb->get_results("SELECT t1.*,t2.start_date FROM {$wpdb->prefix}ctm_project_job_order "
                . "t1 LEFT JOIN {$wpdb->prefix}ctm_project_job_order_meta t2 "
                . "ON t1.id=t2.pjo_id "
                . "$where ORDER BY id DESC LIMIT $start, 10");
        $rs = [];
        foreach ($results as $v) {
            $rs[$v->id . '-' . $v->start_date] = $v;
        }
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $tax_invoice = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice WHERE pjo_id='$value->id'");
                $item_id = $wpdb->get_var("SELECT item_id FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id='{$value->id}'");
                $qtn_meta_ids = $wpdb->get_var("SELECT group_concat(qtn_meta_id) FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id='{$value->id}' AND qtn_meta_id!=0");
                $listArr[$i]['pjo_no'] = $value->id;
                $listArr[$i]['qtn'] = $value->quotation_id;
                $listArr[$i]['qtn_type'] = $value->qtn_type;
                $listArr[$i]['type'] = get_item($item_id, 'collection_name');
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['start_date'] = rb_date($value->start_date);
                $listArr[$i]['status'] = show_pjo_status($value);
                $listArr[$i]['updated_by'] = $value->requested_by;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $view = ($value->status != 'Draft' ? "<a href='" . admin_url() . "admin.php?page=$page-view&pjo_id={$value->id}' class='btn-view' title='View' ><img alt='' src='{$asset_url}view.png'></a>&nbsp;&nbsp;&nbsp;" : '');
                $edit = "<a href='" . admin_url() . "admin.php?page=$page-edit&pjo_id={$value->id}'' class='btn-view' title='Edt' ><img alt='' src='{$asset_url}edit.png'></a>&nbsp;&nbsp;&nbsp;";
                $delete = (has_role_super_and_admin() ? "<a href='" . admin_url() . "admin.php?page=$page&pjo_id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class='btn-view' title='Delete' ><img alt='' src='{$asset_url}delete.png'></a>&nbsp;&nbsp;&nbsp;" : '');
                $create_tax_invoice = $value->qtn_type == 'Project' && empty($tax_invoice) && !empty($qtn_meta_ids) ? "<a href='" . admin_url() . "admin.php?page=tax-invoice&pjo_id={$value->id}&action=create' class=' ' title='View' >Create Tax Invoice</a>&nbsp;&nbsp;&nbsp;" : '';
                $view_tax_invoice = $value->qtn_type == 'Project' && !empty($tax_invoice) ? "<a href='" . admin_url() . "admin.php?page=tax-invoice-view&id={$tax_invoice}' style='color:darkgreen;' title='View' >View Tax Invoice</a>&nbsp;&nbsp;&nbsp;" : '';
                $listArr[$i]['action'] = $view . $edit . $delete . $create_tax_invoice . $view_tax_invoice;
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(concat(t1.id,'-',t2.start_date)) FROM {$wpdb->prefix}ctm_project_job_order "
                . "t1 LEFT JOIN {$wpdb->prefix}ctm_project_job_order_meta t2 "
                . "ON t1.id=t2.pjo_id "
                . "$where"); //count($rows);
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

function admin_ctm_project_job_order_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $pjo_id = !empty($getdata['pjo_id']) ? $getdata['pjo_id'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $qtn_type = !empty($getdata['qtn_type']) ? $getdata['qtn_type'] : '';
    $start_date = !empty($getdata['start_date']) ? $getdata['start_date'] : '';

    $action = !empty($getdata['action']) ? $getdata['action'] : '';
    if ($action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_project_job_order WHERE id={$pjo_id}");
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id={$pjo_id}");
        wp_redirect("?page={$getdata['page']}&msg=delete");
        exit();
    }

    //Create an instance of our package class...
    $testListTable = new CTM_Project_Job_Order_List_Table();
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
        <h1 class="wp-heading-inline">PROJECT JOB ORDER REGISTRY</h1>
        <a href="<?= admin_url("admin.php?page={$page}-add") ?>" id="add-new-item" class="page-title-action">Add New</a><br/><br/>
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
                                                    <input type=text name="pjo_id" value="<?= $pjo_id ?>" class="search-input" placeholder="Search by PJO No" >
                                                </td>
                                                <td>
                                                    <input type=text name="qid" value="<?= $qid ?>" class="search-input" placeholder="Search by QTN No" >
                                                </td>
                                                <td>
                                                    <input type="date" name="start_date" value="<?= $start_date ?>"  class="search-input" placeholder="Search by PJO Date"  >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="qtn_type" onchange="this.form.submit()">
                                                        <option value="">Select Type</option>
                                                        <option value="Stock" <?= $qtn_type == 'Stock' ? 'selected' : '' ?>>Stock</option>
                                                        <option value="Order" <?= $qtn_type == 'Order' ? 'selected' : '' ?>>Order</option>
                                                        <option value="Project" <?= $qtn_type == 'Project' ? 'selected' : '' ?>>Project</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                </td>
                                                <td colspan="2"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;
                                                    <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
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
?>
