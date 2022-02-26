<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_ShowroomOrder_List_Table extends WP_List_Table {

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
            case 'status':
            case 'created_by':
            case 'updated_at':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('Order ID'),
            'status' => __('Status'),
            'created_by' => __('Created By'),
            //'created_at' => __('Created At'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', true),
            'status' => array('status', true),
            'created_by' => array('created_by', true),
            'updated_at' => array('updated_at', true),
        );
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb, $current_user;
        $per_page = 10;
        $this->current_action();
        extract($_REQUEST);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $current_page = $this->get_pagenum();
        $start = ($current_page == 1) ? 0 : ($current_page - 1) * 10;

        $qid = !empty($qid) ? " id like  '%{$qid}%' " : 1;
        $status = !empty($status) ? " status='{$status}'" : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE is_showroom=1 AND trash=0 AND  $qid AND $status ORDER BY {$orderby} {$order}  LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['status'] = showroom_qtn_status($value);
                $listArr[$i]['created_by'] = get_user($value->created_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page=view-{$page}&id={$value->id}' class='btn-view' title='View' ><img alt='' src='{$asset_url}view.png'></a>" .
                        ( hide_edit($value) ? " <a href='" . admin_url() . "admin.php?page=edit-{$page}&id={$value->id}' class='btn-edit' title='Edit'><img alt='' src='{$asset_url}edit.png'></a>" : '')
                        .
                        (in_array('admin', $current_user->roles) || in_array('administrator', $current_user->roles) ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class='btn-delete' title='Delete'><img alt='' src='{$asset_url}delete.png'></a><br/>" : '');
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        //$rows = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_clients");
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations WHERE is_showroom=1 AND  $qid AND $status"); //count($rows);
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

function admin_ctm_showroom_order_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    $action = !empty($getdata['action']) ? $getdata['action'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : '';
    if ($action && $id && $getdata['action'] == 'delete') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET trash=1 WHERE id={$id}");
        wp_redirect("?page={$getdata['page']}&msg=delete");
        exit();
    }
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
        <h1 class="wp-heading-inline">Showroom Order Registry</h1>
        <a  href="<?= admin_url("admin.php?page=add-{$page}") ?>" class="page-title-action">Add New</a>
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
                                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <input type="text" name="qid" class="search-input" placeholder="Search by ORDER ID."  value="<?= $qid ?>" >
                                                </td>
                                                <td>
                                                    <select name="status" onchange="this.form.submit()">
                                                        <option value="">Select Type</option>
                                                        <option value="Pending" <?= $status == 'pending' ? 'selected' : '' ?>>Under Review</option>
                                                        <option value="CONFIRMED" <?= $status == 'CONFIRMED' ? 'selected' : '' ?>>CONFIRMED</option>
                                                        <option value="PURCHASED" <?= $status == 'PURCHASED' ? 'selected' : '' ?>>PURCHASED</option>
                                                        <option value="DELIVERED" <?= $status == 'DELIVERED' ? 'selected' : '' ?>>DELIVERED</option>
                                                    </select>
                                                </td>
                                                <td><button type="submit"  class="button-primary" value="Filter" >Filter</button></td>
                                                <td><a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form id="deletes-filter" method="get">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                        <!-- Now we can render the completed list table -->
                                        <?php
                                        //Create an instance of our package class...
                                        $testListTable = new CTM_ShowroomOrder_List_Table();
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
        });
    </script>
    <?php
}
