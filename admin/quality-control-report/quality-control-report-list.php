<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Quality_control_report_List_Table extends WP_List_Table {

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
            case 'collection':
            case 'category':
            case 'entry':
            case 'supplier':
            case 'client':
            case 'qtn':
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
            'id' => __('QCR No'),
            'collection' => __('Collection Name'),
            'category' => __('Category'),
            'entry' => __('Entry#'),
            'supplier' => __('Supplier Name'),
            'client' => __('Client Name'),
            'qtn' => __('QTN#'),
            'status' => __('Status'),
            'updated_at' => __('Date Reported'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
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
        $client_id = !empty($client_id) ? " t1.client_id like '%{$client_id}%' " : 1;
        $qcr_date = !empty($qcr_date) ? " t1.qcr_date like '%{$qcr_date}%' " : 1;
        $entry = !empty($entry) ? " t1.entry like '%{$entry}%' " : 1;
        $cque = !empty($cque) ? " t1.cque like '%{$cque}%' " : 1;

        $where = "WHERE {$pjo_id} AND {$cque} AND {$client_id} AND {$qcr_date} AND $entry";
        $rs = $wpdb->get_results("SELECT t1.id,t1.client_id,t1.item_id,t1.po_id,t1.entry,t1.notice_date,t1.cque,t1.status,t2.collection_name,t2.category,t2.sup_code FROM {$wpdb->prefix}ctm_quality_control_report t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ORDER BY t1.id DESC LIMIT $start, 10");


        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['collection'] = $value->collection_name;
                $listArr[$i]['category'] = get_item_category($value->category, 'name');
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['supplier'] = get_supplier($value->sup_code, 'name');
                $listArr[$i]['client'] = get_client($value->client_id, 'name');
                $listArr[$i]['qtn'] = $value->cque;
                $listArr[$i]['status'] = show_qcr_status($value);
                $listArr[$i]['updated_at'] = rb_date($value->notice_date);
                $listArr[$i]['action'] = ($value->status != 'Draft' ? "<a href='" . admin_url() . "admin.php?page=$page-view&id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>" : '')
                        . "<a href='" . admin_url() . "admin.php?page=$page-edit&id={$value->id}'' class='btn-view' title='Edt' >"
                        . "<img alt='' src='{$asset_url}edit.png'></a>"
                        . (has_role_super_and_admin() ? "<a href='" . admin_url() . "admin.php?page=$page&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class='btn-view' title='Delete' ><img alt='' src='{$asset_url}delete.png'></a>" : '')
                        ;
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 't1.id';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quality_control_report t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where"); //count($rows);
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

function admin_ctm_quality_control_report_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    $qcr_id = !empty($getdata['qcr_id']) ? $getdata['qcr_id'] : '';
    $cque = !empty($getdata['cque']) ? $getdata['cque'] : '';
    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $notice_date = !empty($getdata['notice_date']) ? $getdata['notice_date'] : '';

    $action = !empty($getdata['action']) ? $getdata['action'] : '';
    if ($action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quality_control_report WHERE id={$id}");
        $msg = 1;
    }

    //Create an instance of our package class...
    $testListTable = new CTM_Quality_control_report_List_Table();
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
        table tr td.item_desc{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Quality Control Report Registry</h1>
        <a href="<?= admin_url("admin.php?page={$page}-add") ?>" id="add-new-item" class="page-title-action">Add New</a><br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Your Quality Control Report has been deleted successfully.
            </div>
        <?php } ?>
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
                                                    <input type=text name="qcr_id" value="<?= $qcr_id ?>" class="search-input" placeholder="Search by QCR No" >
                                                </td>
                                                <td>
                                                    <input type=text name="cque" value="<?= $cque ?>" class="search-input" placeholder="Search by QTN No" >
                                                </td>
                                                <td>
                                                    <input type=text name="entry" value="<?= $entry ?>" class="search-input" placeholder="Search by Entry #" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="notice_date" value="<?= $notice_date ?>"  class="search-input" placeholder="Search by QCR Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"  >
                                                </td>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                </td>
                                                <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>&nbsp;&nbsp;
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
        var msg = '<?= !empty($msg) ? $msg : 0 ?>';
        if (parseInt(msg) === 1) {
            window.history.pushState({}, null, '<?= admin_url("/admin.php?page={$page}") ?>');
        }
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
