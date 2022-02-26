<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Employee_List_Table extends WP_List_Table {

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
            case 'image':
            case 'nationality':
            case 'designation':
            case 'department':
            case 'joining_date':
            case 'contract_type':
            case 'visa':
            case 'eid':
            case 'passport':
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
            'id' => __('ID'),
            'name' => __('Name'),
            'image' => __('Image'),
            'nationality' => __('Nationality'),
            'designation' => __('Designation'),
            'department' => __('Department'),
            'joining_date' => __('Joining Date'),
            'contract_type' => __('Contract Type'),
            'visa' => __('Visa'),
            'eid' => __('Emirates ID'),
            'passport' => __('Passport'),
            'status' => __('Status'),
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

        $id = !empty($id) ? " id like '%{$id}%' " : 1;
        $name = !empty($name) ? "name like '%{$name}%' " : 1;
        $department = !empty($department) ? "department='{$department}' " : 1;
        $status = !empty($status) ? "status='{$status}' " : "status='active'";

        $where = "WHERE {$id} AND {$name} AND {$department} AND {$status}";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_hr_employees  $where ORDER BY id DESC LIMIT $start, 10 ";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = "<a href='" . admin_url() . "admin.php?page=employee-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >$value->id</a>";
                $listArr[$i]['name'] = $value->name;
                $listArr[$i]['image'] = "<img src='" . get_image_src($value->profile_image) . "' width=75 />";
                $listArr[$i]['nationality'] = get_country($value->nationality, 'name');
                $listArr[$i]['designation'] = $value->designation;
                $listArr[$i]['department'] = $value->department;
                $listArr[$i]['joining_date'] = rb_date($value->joining_date);
                $listArr[$i]['contract_type'] = $value->contract_type;
                $listArr[$i]['visa'] = $value->visa;
                $listArr[$i]['eid'] = $value->eid_number;
                $listArr[$i]['passport'] = $value->passport_no;
                $listArr[$i]['status'] = $value->status == 'Active' ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Inactive</span>";
                $listArr[$i]['updated_at'] = rb_date($value->updated_at) . '<br/>' . rb_time($value->updated_at);

                $view = "<a href='" . admin_url() . "admin.php?page=employee-view&id={$value->id}&action=edit' "
                        . "title=View class=btn-edit ><img alt='' src='{$asset_url}view.png'></a>&nbsp;&nbsp;";
                $edit = "<a href='" . admin_url() . "admin.php?page=employee-edit&id={$value->id}&action=edit' "
                        . "title=Edit class=btn-edit ><img alt='' src='{$asset_url}edit.png'></a>&nbsp;&nbsp;";
                $delete = has_this_role('accounts') ? "<a href='" . admin_url() . "admin.php?page=employee&id={$value->id}&action=delete' "
                        . "title=Delete class=btn-delete onclick='return confirm(`are you sure you want to delete?`)' ><img alt='' src='{$asset_url}delete.png'></a>" : '';

                $listArr[$i]['action'] = $view . $edit . $delete;
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_hr_employees $where"); //count($rows);
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

function admin_ctm_employee_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $name = !empty($getdata['id']) ? $getdata['name'] : '';
    $department = !empty($getdata['department']) ? $getdata['department'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : 'active';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_hr_employees WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
//Create an instance of our package class...
        $testListTable = new CTM_Employee_List_Table();
//Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], 
            #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], 
            #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], 
            #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
            table.wp-list-table {table-layout: initial!important;}
            .wp-list-table tr th{white-space: nowrap;}
            table tr td.collection_name{width:400px}
            table tr td.description{width:200px}
            #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;}
            th#received_from{width: 150px}
        </style>
        <div class="wrap">
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
            <h1 class="wp-heading-inline">Employee Master</h1>
            <a  id="add-new-client" href="<?= 'admin.php?page=employee-add' ?>" class="page-title-action btn-primary" target="_blank">Add Employee </a>
            <a  id="add-new-client" href="<?= 'admin.php?page=employee-master' ?>" class="page-title-action btn-primary">Employee Master </a>
            <br/> <br/>
            <?php if (!empty($getdata['msg'])) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Employee has been added successfully.
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
                                        <form method="get">
                                            <input type=hidden name="page" value="<?= $getdata['page'] ?>" />
                                            <table class="form-table">
                                                <tr>
                                                    <td>
                                                        <input type=text name="id" value="<?= $id ?>" class="search-input" placeholder="Employee ID"  >
                                                    </td>
                                                    <td>
                                                        <input type=text name="name" value="<?= $name ?>" class="search-input" placeholder="Employee Name"  >
                                                    </td>
                                                    <td>
                                                        <select name='department' onchange="this.form.submit()">
                                                            <option value="">Select Department</option>
                                                            <?php
                                                            foreach (DEPARTMENTS as $value) {
                                                                $selected = $department == $value ? 'selected' : '';
                                                                echo "<option value='$value' $selected>$value</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name='status' onchange="this.form.submit()">
                                                            <option value="">Select Status</option>
                                                            <?php
                                                            foreach (['active' , 'inactive'] as $value) {
                                                                $selected = $status == $value ? 'selected' : '';
                                                                echo "<option value='$value' $selected>". ucfirst($value)."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>

                                                    <td><button type="submit"  class="button-primary" value="Filter" >Filter</button></td>
                                                    <td><a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
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
                        var received_from = '<?= !empty($getdata['received_from']) ? $getdata['received_from'] : 0; ?>';
                        jQuery('#client-name').html('');
                        var html = '<option value="">Select Client</option>';
                        jQuery.each(data, function (i, client) {
                            var selected = received_from === client.name ? 'selected' : '';
                            html += `<option value="${client.name}" ${selected}>${client.name}</option>`;
                        });
                        jQuery('#client-name').html(html);
                        jQuery('.chosen-select').chosen();
                    }
                });

            });
        </script>
        <?php
    }
}
