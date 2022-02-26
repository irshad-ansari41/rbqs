<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Country_List_Table extends WP_List_Table {

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
            case 'name':
            case 'updated_at':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID'),
            'name' => __('Name'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array('name', true),
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

        $id = !empty($cid) ? " id like '%{$cid}%' " : 1;
        $name = !empty($name) ? " name like '%{$name}%' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$name}  AND {$id}";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_item_category $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['name'] = $value->name;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit' title=Edit class=btn-edit>"
                        . "<img alt='' src='{$asset_url}edit.png'></a> | "
                        . (has_role_super_and_admin() ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' title=Delete class=btn-delete><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_item_category $where"); //count($rows);
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

function admin_ctm_item_category_list() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $name = !empty($postdata['name']) ? trim($postdata['name']) : (!empty($getdata['name']) ? $getdata['name'] : '');
    $id = !empty($postdata['id']) ? $postdata['id'] : (!empty($getdata['id']) ? $getdata['id'] : '');
    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');

    if ($action == 'Add') {
        $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_item_category SET name='$name', created_by='{$current_user->ID}', updated_by='{$current_user->ID}',created_at='{$date}',  updated_at='{$date}'");
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'Update') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_item_category SET name='$name', updated_by='{$current_user->ID}', updated_at='{$date}' WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_item_category WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        
         $cid = !empty($getdata['cid']) ? $getdata['cid'] : '';
             
        //Create an instance of our package class...
        $testListTable = new CTM_Country_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        if ($id && $action == 'edit') {
            $category = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_item_category WHERE id={$id}");
        }
        ?>
        <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        </style>
        <div class="wrap">
            <h1 class="wp-heading-inline">Item Category List </h1>
            <span href="#" id="add-new-category" class="page-title-action">Add New</span><br/><br/>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-category-form" class="<?= $id ? '' : 'hide' ?>" method="post">
                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <?php if ($id) { ?>
                                            <input type="hidden" name="id" value="<?= $id ?>" />
                                        <?php } ?>
                                        <table class="form-table">
                                            <tr>
                                                <td><label>Category Name:<span class="text-red">*</span></label><br/>
                                                    <input type="text" name="name" class="search-input" placeholder="Category Name"   value="<?= !empty($category) ? $category->name : $name ?>" required>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <br/><input type="submit"  name="action" value="<?= $id ? 'Update' : 'Add' ?>" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                                            </tr>
                                        </table>
                                        <br/>
                                    </form>
                                    <?php if (!$id) { ?>
                                        <div id="page-inner-content">
                                            <form id="filter-form1" method="get">
                                                <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                                <table class="form-table">
                                                    <tr>
                                                        <td><input type="text" name="cid" class="search-input" value="<?=$cid?>" placeholder="Search by ID"  ></td>
                                                        <td><input type="text" name="name" class="search-input" value="<?=$name?>" placeholder="Search by name"  ></td>
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
                                    <?php } ?>
                                </div>
                            </div>
                        </div>	
                    </div>
                </div>
            </div><!-- dashboard-widgets-wrap -->
        </div>

        <script>
            jQuery('#add-new-category').click(() => {
                jQuery('#add-new-category-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });
        </script>
        <?php
    }
}
