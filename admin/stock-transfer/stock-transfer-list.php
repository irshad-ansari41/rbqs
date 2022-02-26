<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Stock_Transfer_List_Table extends WP_List_Table {

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
            case 'from_location':
            case 'to_location':
            case 'st_date':
            case 'status':
            case 'created_by':
            case 'updated_at':
            case 'packaging_label':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID'),
            'from_location' => __('From Location'),
            'to_location' => __('To Location'),
            'st_date' => __('Stock Tranfer Date'),
            'status' => __('Status'),
            'created_by' => __('Created By'),
            'updated_at' => __('Updated At'),
            'packaging_label' => __('Packaging Label'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', true),
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

        $st_date = !empty($st_date) ? " st_date = '{$st_date}' " : 1;
        $from_location = !empty($from_location) ? " from_location = '{$from_location}' " : 1;
        $to_location = !empty($to_location) ? " to_location = '{$to_location}' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$st_date} AND {$from_location} AND {$to_location}";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_stock_transfer $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {

                $stf_status = $wpdb->get_var("SELECT status FROM {$wpdb->prefix}ctm_stock_transfer_meta WHERE status='pending' AND stf_id='{$value->id}'");

                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['from_location'] = $value->from_location;
                $listArr[$i]['to_location'] = $value->to_location;
                $listArr[$i]['st_date'] = rb_date($value->st_date);
                $listArr[$i]['status'] = !empty($stf_status) && $stf_status == 'Pending' ? "<span class='badge badge-warning'>Pending</span>" : "<span class='badge badge-success'>Approved</span>";
                $listArr[$i]['created_by'] = get_user($value->created_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['packaging_label'] = "<a href='" . admin_url() . "admin.php?page=stf-packaging-label&stf_id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>";
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}&action=view' title=View class=btn-edit>"
                        . "<img alt='' src='{$asset_url}view.png'></a>&nbsp;&nbsp;"
                        . (has_role_super_and_admin() && $stf_status == 'Pending' ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' title=Delete class=btn-delete><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_stock_transfer $where"); //count($rows);
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

function admin_ctm_stock_transfer_list_page() {
    global$wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? trim($getdata['id']) : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    $st_date = !empty($getdata['st_date']) ? trim($getdata['st_date']) : '';
    $from_location = !empty($getdata['from_location']) ? trim($getdata['from_location']) : '';
    $to_location = !empty($getdata['to_location']) ? trim($getdata['to_location']) : '';


    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_stock_transfer_meta WHERE stf_id={$id} AND status='Pending'");
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_stock_transfer WHERE id={$id}");
        $msg=1;
    }

    //Create an instance of our package class...
    $testListTable = new CTM_Stock_Transfer_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Stock Transfer List</h1>
        <br/><br/>
         <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Stock Transfer has been deleted successfully.
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
                                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <input type="text" name="st_date" value="<?= $st_date ?>" placeholder="Stock Transfer Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"/>
                                                </td>
                                                <td>
                                                    <select class='location' name='from_location' style='width:75px' onchange='this.form.submit()'>"
                                                        <option value=''>From Location</option>
                                                        <?php
                                                        foreach (STOCK_LOCATION as $value) {
                                                            $selected = $from_location == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class='location' name='to_location' style='width:75px' onchange='this.form.submit()'>"
                                                        <option value=''>To Location</option>
                                                        <?php
                                                        foreach (STOCK_LOCATION as $value) {
                                                            $selected = $to_location == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected>$value</option>";
                                                        }
                                                        ?>
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
                                        <?php $testListTable->display(); ?>
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
