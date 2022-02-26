<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Item_List_Table extends WP_List_Table {

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
            case 'collection_name':
            case 'image':
            case 'description':
            case 'sup_code':
            case 'category':
            case 'entry':
            case 'hs_code':
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
            'id' => __('ID'),
            'collection_name' => __('Collection Name'),
            'image' => __('Image'),
            'description' => __('Description'),
            'sup_code' => __('Supplier Code'),
            'category' => __('Category'),
            'entry' => __('Entry #'),
            'hs_code' => __('HS Code'),
            'status' => __('Status'),
            'created_by' => __('Created By'),
            'updated_at' => __('Updated At'),
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

        $id = !empty($pid) ? " id like '%{$pid}%' " : 1;
        $collection_name = !empty($collection_name) ? " collection_name like '%{$collection_name}%' " : 1;
        $description = !empty($description) ? " description like '%{$description}%' " : 1;
        $sup_code = !empty($sup_code) ? " sup_code like '%{$sup_code}%' " : 1;
        $entry = !empty($entry) ? " entry like '%{$entry}%' " : 1;
        $category = !empty($category) ? " category ='$category' " : 1;
        $hs_code = !empty($hs_code) ? " hs_code like '%{$hs_code}%' " : 1;
        $item_status = !empty($item_status) ? " status = '{$item_status}' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$id} AND {$collection_name} AND {$description} AND {$sup_code} AND {$entry} AND {$category} AND {$hs_code} AND $item_status";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_items $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['collection_name'] = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit' title=Edit class=btn-edit>".$value->collection_name."</a>";
                $listArr[$i]['description'] = $value->description;
                $listArr[$i]['image'] = "<a href='" . get_image_src($value->image) . "' target='_image'><img src='" . get_image_src($value->image) . "' width=100  style='margin: auto;width: 100px; '></a>";
                $listArr[$i]['sup_code'] = $value->sup_code;
                $listArr[$i]['category'] = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}ctm_item_category WHERE id='{$value->category}'");
                $listArr[$i]['entry'] = $value->entry;
                $listArr[$i]['hs_code'] = $value->hs_code;
                $listArr[$i]['status'] = $value->status == 'Active' ? "<span style='color:green'>Active</span>" : "<span style='color:red'>Inactive</span>";
                $listArr[$i]['created_by'] = get_user($value->created_by,'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit' title=Edit class=btn-edit>"
                        . "<img alt='' src='{$asset_url}edit.png'></a> | " .
                        (has_role_super_and_admin() ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_items $where"); //count($rows);
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

function admin_ctm_item_list() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $collection_name = !empty($postdata['collection_name']) ? trim($postdata['collection_name']) : (!empty($getdata['collection_name']) ? $getdata['collection_name'] : '');
    $description = !empty($postdata['description']) ? trim($postdata['description']) : (!empty($getdata['description']) ? $getdata['description'] : '');
    $sup_code = !empty($postdata['sup_code']) ? trim($postdata['sup_code']) : (!empty($getdata['sup_code']) ? $getdata['sup_code'] : '');
    $entry = !empty($postdata['entry']) ? trim($postdata['entry']) : (!empty($getdata['entry']) ? $getdata['entry'] : '');
    $category = !empty($postdata['category']) ? trim($postdata['category']) : (!empty($getdata['category']) ? $getdata['category'] : '');
    $hs_code = !empty($postdata['hs_code']) ? trim($postdata['hs_code']) : (!empty($getdata['hs_code']) ? $getdata['hs_code'] : '');
    $image = !empty($postdata['image']) ? trim($postdata['image']) : site_url();
    $status = !empty($postdata['status']) ? trim($postdata['status']) : (!empty($getdata['status']) ? $getdata['status'] : 'Inactive');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');


    $data = ['collection_name' => $collection_name, 'description' => $description, 'sup_code' => $sup_code, 'entry' => $entry, 
        'category' => $category, 'hs_code' => $hs_code, 'image' => $image, 'status' => $status, 'created_by' => $current_user->ID, 
        'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

    if ($action == 'Add') {
        $wpdb->insert("{$wpdb->prefix}ctm_items", $data, wpdb_data_format($data));
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'Update') {
        unset($data['created_by']);
        unset($data['created_at']);
        $wpdb->update("{$wpdb->prefix}ctm_items", $data, ['id' => $id], wpdb_data_format($data), ['%d']);
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_items WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {

        $pid = !empty($getdata['pid']) ? $getdata['pid'] : '';
        $item_status = !empty($getdata['item_status']) ? $getdata['item_status'] : '';

        //Create an instance of our package class...
        $testListTable = new CTM_Item_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();
        $categories = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_item_category ORDER BY name ASC");
        $suppliers = $wpdb->get_results("SELECT sup_code FROM {$wpdb->prefix}ctm_suppliers ORDER BY sup_code ASC");
        if ($id && $action == 'edit') {
            $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items WHERE id={$id}");
        }
        ?>
        <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
            .wp-list-table{table-layout: auto!important;}
            table tr td.description{width:200px}
        </style>
        <div class="wrap">
            <h1 class="wp-heading-inline">Product Master</h1>
            <span href="#" id="add-new-item" class="page-title-action">Add New</span>
            <a  href='<?= get_template_directory_uri() ?>/export/export-items.php' class="page-title-action">Export</a>  
            <!--<a href='' class="page-title-action">Import</a>-->
            <br/><br/>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-item-form" class="<?= $id ? '' : 'hide' ?>" method="post">
                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <?php if ($id) { ?>
                                            <input type="hidden" name="id" value="<?= $id ?>" />
                                        <?php } ?>
                                        <table class="form-table" style="width:100%">
                                            <tr>
                                            <tr>
                                                <td colspan="2"><label>Collection Name:<span class="text-red">*</span></label><br/>
                                                    <input type="text" name="collection_name"  placeholder="Collection Name"  value="<?= !empty($item) ? $item->collection_name : $collection_name ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><label>Description:<span class="text-red">*</span></label><br/>
                                                    <textarea style="width:100%" type="text" name="description"  placeholder="Description" rows="15" required><?= !empty($item) ? $item->description : $description ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Supplier Code:<span class="text-red">*</span></label><br/>
                                                    <select name="sup_code"   required>
                                                        <option value="">Select supplier</option> 
                                                        <?php foreach ($suppliers as $supplier) { ?>
                                                            <option value="<?= $supplier->sup_code ?>" <?= (!empty($item) ? $item->sup_code : $sup_code) == $supplier->sup_code ? 'selected' : '' ?>><?= $supplier->sup_code ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td><label>Entry:</label><br/>
                                                    <input type="text" name="entry"  placeholder="Entry #"  value="<?= !empty($item) ? $item->entry : $entry ?>">
                                                </td>
                                            </tr><tr>
                                                <td><label>Category:<span class="text-red">*</span></label><br/>
                                                    <select name="category" required>
                                                        <option value="">Select Category</option> 
                                                        <?php foreach ($categories as $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= (!empty($item) ? $item->category : $category) == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>

                                                <td><label>HS Code:<span class="text-red">*</span></label><br/>
                                                    <input type="text" name="hs_code"  placeholder="HS Code"  value="<?= !empty($item) ? $item->hs_code : $hs_code ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><label>Image:<span class="text-red">*</span></label><br/>
                                                    <input id="item_image" class="button-primary" type="button" value="Add Item Image" /><br/>
                                                    <output id="item-image"><?= !empty($item->image) ? "<input type='hidden' name='image' value='$item->image'/>" : "" ?></output>
                                                    <?= wp_get_attachment_image(!empty($item) ? $item->image : $image, 'large') ?>
                                                </td>
                                            </tr>
                                            <?php
                                            if (has_this_role()) {
                                                ?>
                                                <tr>
                                                    <td colspan="2"><label>Status:</label><br/>
                                                        <select  name="status" class="search-input">
                                                            <option value="">Status</option>
                                                            <option value="Active" <?= !empty($item) && $item->status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                            <option value="Inactive" <?= !empty($item) && $item->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>
                                                    <br/><input type="submit"  name="action" value="<?= $id ? 'Update' : 'Add' ?>" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                                            </tr>
                                        </table>
                                        <br/><br/><br/>
                                    </form>
                                    <?php if (!$id) { ?>
                                        <div id="page-inner-content">
                                            <form id="filter-form1">
                                                <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                                <table class="form-table">
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="pid" value="<?= $pid ?>" class="search-input" placeholder="Search by ID"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="collection_name" value="<?= $collection_name ?>" class="search-input" placeholder="Search by name"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="description" value="<?= $description ?>"  class="search-input" placeholder="Search by description"  >
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="sup_code" value="<?= $sup_code ?>"  class="search-input" placeholder="Search by Supplier code"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="entry" value="<?= $entry ?>"  class="search-input" placeholder="Search by entry"  >
                                                        </td>
                                                        <td>
                                                            <select name="category" onchange="this.form.submit()">
                                                                <option value="">Select Category</option> 
                                                                <?php foreach ($categories as $value) { ?>
                                                                    <option value="<?= $value->id ?>" <?= $category == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="hs_code" value="<?= $hs_code ?>"  class="search-input" placeholder="Search by hs code"  >
                                                        </td>
                                                        <td>
                                                            <select  name="item_status" class="search-input" onchange="this.form.submit()">
                                                                <option value="">Status</option>
                                                                <option value="Active" <?= $item_status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                                <option value="Inactive" <?= $item_status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </td>

                                                        <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                            &nbsp;&nbsp;
                                                            <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                        </td>
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
            jQuery('#add-new-item').click(() => {
                jQuery('#add-new-item-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });

            jQuery('#item_image').click(function (e) {
                file_uploader(e, 'image', 'item-image', false);
            });

            function file_uploader(e, input, output, multiple) {
                var custom_uploader;
                e.preventDefault();
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }

                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: multiple
                });
                custom_uploader.on('select', function () {
                    var selection = custom_uploader.state().get('selection');
                    var attachment_ids = selection.map(function (attachment) {
                        attachment = attachment.toJSON();
                        if (multiple == false) {
                            jQuery('#' + output).html('');
                        }
                        jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:250px'>");
                    }).join();
                });
                custom_uploader.open();
            }
        </script>
        <?php
    }
}
