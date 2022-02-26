<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Import_Declaration_List_Table extends WP_List_Table {

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
            case 'containers_name':
            case 'from_location':
            case 'declaration_no':
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
            'id' => __('ID'),
            'containers_name' => __('Containers'),
            'from_location' => __('From Location'),
            'declaration_no' => __('Declaration No'),
            'updated_by' => __('Updated By'),
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


        $containers_name = !empty($containers_name) ? " containers_name IN ('" . implode("', '", $containers_name) . "') " : 1;
        $from_location = !empty($from_location) ? " from_location ='{$from_location}'" : 1;
        $declaration_no = !empty($declaration_no) ? " declaration_no ='{$declaration_no}'" : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE $containers_name AND $from_location AND $declaration_no";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_import_declarations $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['containers_name'] = $value->containers_name;
                $listArr[$i]['from_location'] = $value->from_location;
                $listArr[$i]['declaration_no'] = $value->declaration_no;
                $listArr[$i]['updated_by'] = get_user($value->updated_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);


                $cost_sheet = "<a href='admin.php?page=cost-sheet&id={$value->id}&action=cost-sheet-registry' target='_blank' class=btn-edit>Cost Sheet Registry</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                $internal = "<a href='admin.php?page={$page}-internal-view&id={$value->id}&action=cost-sheet-registry' target='_blank' class=btn-edit>Internal Imp. Decl.</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                $view = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}' class='btn-view' title='View' target='_blank' >Imp. Decl.</a>&nbsp;&nbsp;&nbsp;";
                $edit = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit' class='btn-edit' title='Edit' ><img alt='' src='{$asset_url}edit.png'></a></a>&nbsp;&nbsp;&nbsp;";
                $delete = "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete><img alt='' src='{$asset_url}delete.png'></a>";
                $listArr[$i]['action'] = $cost_sheet . $internal . $view . $edit . (has_this_role('accounts') ? $delete : '');

                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? 't1.' . filter_input(INPUT_GET, 'orderby') : 't1.id';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_import_declarations $where"); //count($rows);
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

function admin_ctm_import_declaration_page() {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $id = !empty($postdata['id']) ? $postdata['id'] : (!empty($getdata['id']) ? $getdata['id'] : '');
    $action = !empty($postdata['containers_name']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');

    if ($action == 'create') {
        $containers_name = !empty($postdata['containers_name']) ? $postdata['containers_name'] : '';
        $from_location = !empty($postdata['from_location']) ? $postdata['from_location'] : '';
        $declaration_no = !empty($postdata['declaration_no']) ? $postdata['declaration_no'] : '';
        $data = ['containers_name' => implode(', ', $containers_name), 'from_location' => $from_location, 'declaration_no' => $declaration_no,
            'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_import_declarations", $data, wpdb_data_format($data));
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'update') {
        $containers_name = !empty($postdata['containers_name']) ? $postdata['containers_name'] : '';
        $from_location = !empty($postdata['from_location']) ? $postdata['from_location'] : '';
        $declaration_no = !empty($postdata['declaration_no']) ? $postdata['declaration_no'] : '';
        $data = ['containers_name' => implode(', ', $containers_name), 'from_location' => $from_location, 'declaration_no' => $declaration_no];
        $wpdb->update("{$wpdb->prefix}ctm_import_declarations", $data, ['id' => $id], wpdb_data_format($data), ['%d']);
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_import_declarations WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        $containers_name = !empty($getdata['containers_name']) ? array_filter($getdata['containers_name']) : [];
        $from_location = !empty($getdata['from_location']) ? $getdata['from_location'] : '';
        $declaration_no = !empty($getdata['declaration_no']) ? $getdata['declaration_no'] : '';
        $containers = $wpdb->get_results("SELECT container_name FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name!='' GROUP BY container_name");

        if ($action == 'edit') {
            $import_declarations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_import_declarations WHERE id='{$id}'");
            $containers_name = !empty($import_declarations) ? array_filter(explode(', ', $import_declarations->containers_name)) : [];
            $from_location = $import_declarations->from_location;
            $declaration_no = $import_declarations->declaration_no;
        }
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
            table.wp-list-table {table-layout: initial!important;}
            .wp-list-table tr th,.wp-list-table tr td{white-space: nowrap;}
            .wp-list-table tr th#client_name,.wp-list-table tr td.client_name{white-space:normal;}
            .postbox-container{margin: auto;float: unset;}
            .hide,.toggle-indicator{display: none}
            .text-center{text-align:center!important;}
            .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        </style>
        <div class="wrap">
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
            <?php if (empty($id)) { ?>
                <h1 class="wp-heading-inline">Import Declaration List</h1>
                <span href="#" id="add-new-category" class="page-title-action">Create New</span><br/><br/> 
            <?php } else { ?>
                <h1 class="wp-heading-inline">Edit Import Declaration</h1>
            <?php } ?>

            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-category-form" class="<?= $id ? '' : 'hide' ?>" method="post">
                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <table class="form-table">
                                            <tr>
                                                <td style="vertical-align: top;text-align: left">
                                                    <label>Select Containers</label>
                                                    <select name="containers_name[]" required  class="chosen-select" multiple='true'>
                                                        <option value="">Select Containers</option>
                                                        <?php
                                                        foreach ($containers as $value) {
                                                            $selected = in_array($value->container_name, $containers_name) ? 'selected' : '';
                                                            echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Container From Location:</label><br/>
                                                    <input type="text" name="from_location" value="<?= $from_location ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Import Declaration #</label><br/>
                                                    <input name='declaration_no' type='text' value='<?= $declaration_no ?>'>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><br/><br/><button type="submit"  name="action" value="<?= $id ? 'update' : 'create' ?>" class="button-primary"><?= $id ? 'Update' : 'Create' ?></button>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                                            </tr>
                                        </table>
                                        <br/>
                                    </form>

                                    <div id="page-inner-content" class="<?= $id ? 'hide' : '' ?>">
                                        <form id="filter-form1" method="get">
                                            <input type="hidden" name="page" value="<?= $page ?>" />
                                            <table class="form-table">
                                                <tr>
                                                    <td style="vertical-align: top;text-align: left">
                                                        <label>Select Containers</label>
                                                        <select name="containers_name[]"  class="chosen-select" multiple='true'>
                                                            <option value="">Select Containers</option>
                                                            <?php
                                                            foreach ($containers as $value) {
                                                                $selected = in_array($value->container_name, $containers_name) ? 'selected' : '';
                                                                echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td style="vertical-align: top;text-align: left">
                                                        <label>Container From Location:</label>
                                                        <input type="text" name="from_location" value="<?= $from_location ?>" />
                                                    </td>
                                                    <td style="vertical-align: top;text-align: left">
                                                        <label>Import Declaration #</label>
                                                        <input name='declaration_no' type='text' value='<?= $declaration_no ?>'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" style="vertical-align: top;text-align: left"><br/><br/>
                                                        <button type="submit"  class="button-primary" value="Filter" >Filter</button>
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
                                            $testListTable = new CTM_Import_Declaration_List_Table();
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
                jQuery('#add-new-category').click(() => {
                    jQuery('#add-new-category-form').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
                jQuery('#add-new-quotation').click(() => {
                    jQuery('#welcome-to-aquila').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
                jQuery('#open-close-menu').click(() => {
                    jQuery('#collapse-button').trigger('click');
                });
                jQuery('.chosen-select').chosen();
            });
        </script>
        <?php
    }
}
