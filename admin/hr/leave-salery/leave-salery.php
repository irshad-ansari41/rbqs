<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Leave_Salary_List_Table extends WP_List_Table {

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
            case 'joining_date':
            case 'last_ls_date':
            case 'ls_paid_period':
            case 'amount':
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
            'name' => __('Name'),
            'joining_date' => __('Joining Date'),
            'last_ls_date' => __('Date of Last Leave Salary'),
            'ls_paid_period' => __('Leave Salary Paid for Period'),
            'amount' => __('Amount'),
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


        $id = !empty($id) ? " id like  '%" . $id . "%'" : 1;
        $emp_id = !empty($emp_id) ? " emp_id ='{$emp_id}'" : 1;


        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE $id AND $emp_id";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_hr_leave_salaries $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $employee = get_employee($value->emp_id);
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['name'] = $employee->name;
                $listArr[$i]['joining_date'] = rb_date($employee->joining_date);
                $listArr[$i]['amount'] = $value->amount;
                $listArr[$i]['last_ls_date'] = rb_date($value->last_ls_date);
                $listArr[$i]['ls_paid_period'] = $value->ls_paid_period;
                $listArr[$i]['updated_by'] = get_user($value->updated_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $view = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}' class='btn-view' title='View' ><img alt='' src='{$asset_url}view.png'></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                $edit = "<a href='admin.php?page={$page}&id={$value->id}&action=edit'  class=btn-edit><img alt='' src='{$asset_url}edit.png' title='Edit'></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                $delete = "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete><img alt='' src='{$asset_url}delete.png'></a>";
                $listArr[$i]['action'] = $view . $edit . $delete;

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
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_hr_leave_salaries $where"); //count($rows);
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

function admin_ctm_leave_salary_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');
    $emp_id = !empty($getdata['emp_id']) ? $getdata['emp_id'] : '';

    $data = [
        'emp_id' => !empty($postdata['emp_id']) ? $postdata['emp_id'] : '',
        'last_ls_date' => !empty($postdata['last_ls_date']) ? $postdata['last_ls_date'] : '',
        'ls_paid_period' => !empty($postdata['ls_paid_period']) ? $postdata['ls_paid_period'] : '',
        'amount' => !empty($postdata['amount']) ? $postdata['amount'] : '',
        'created_by' => $current_user->ID,
        'updated_by' => $current_user->ID,
        'ls_date' => $date,
        'created_at' => $date,
        'updated_at' => $date,
    ];

    if ($action == 'create') {
        $wpdb->insert("{$wpdb->prefix}ctm_hr_leave_salaries", array_map('trim', $data), wpdb_data_format($data));
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($action == 'update') {
        unset($data['ls_date'], $data['created_at'], $data['created_at']);
        $wpdb->update("{$wpdb->prefix}ctm_hr_leave_salaries", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_hr_leave_salaries WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {

        if ($id && $action == 'edit') {
            $leave_salary = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_leave_salaries WHERE id={$id}");
        }

        $employees = get_active_employees();
        ?>
        <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
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
                <h1 class="wp-heading-inline">Leave Salary</h1>
                <span id="add-new-leave-salary" class="page-title-action">Add New</span> 
            <?php } else { ?>
                <h1 class="wp-heading-inline">Edit Leave Salary</h1>
            <?php } ?>
            <br/><br/>   
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-leave-salary-form" class="<?= $id ? '' : 'hide' ?>" method="post">
                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <?php if ($id) { ?>
                                            <input type="hidden" name="id" value="<?= $id ?>" />
                                        <?php } ?>
                                        <table class="form-table" style="width:100%">
                                            <tr>
                                                <td><label>Employee:<span class="text-red">*</span></label><br/>
                                                    <select name="emp_id" required>
                                                        <option>Select Employee</option>
                                                        <?php foreach ($employees as $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= !empty($leave_salary) && $leave_salary->emp_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Date of Last Leave Salary:</label><br/>
                                                    <input type="date" name="last_ls_date" value="<?= !empty($leave_salary) ? $leave_salary->last_ls_date : '' ?>" required >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Leave Salary Paid for Period:<span class="text-red">*</span></label><br/>
                                                    <input type="text" name="ls_paid_period"  placeholder="Leave Salary Paid for Period" value="<?= !empty($leave_salary) ? $leave_salary->ls_paid_period : '' ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Amount:<span class="text-red">*</span></label><br/>
                                                    <input type="number" name="amount" placeholder="Amount" value="<?= !empty($leave_salary) ? $leave_salary->amount : '' ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><br/><br/>
                                                    <button type="submit"  name="action" value="<?= $id ? 'update' : 'create' ?>" class="button-primary"><?= $id ? 'Update' : 'Create' ?></button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                                            </tr>
                                        </table>
                                        <br/>
                                    </form>
                                    <?php if (!$id) { ?>
                                        <div id="page-inner-content">
                                            <form id="filter-form1" method="get">
                                                <input type="hidden" name="page" value="<?= $page ?>" />
                                                <table class="form-table">
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="id" class="search-input" placeholder="ID" value="<?= $id ?>" >
                                                        </td>
                                                        <td>
                                                            <select name="emp_id" required>
                                                                <option>Select Employee</option>
                                                                <?php foreach ($employees as $value) { ?>
                                                                    <option value="<?= $value->id ?>" <?= $emp_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                                <?php } ?>
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
                                                <input type="hidden" name="page" value="<?= $page ?>" />
                                                <!-- Now we can render the completed list table -->
                                                <?php
                                                //Create an instance of our package class...
                                                $testListTable = new CTM_Leave_Salary_List_Table();
                                                //Fetch, prepare, sort, and filter our data...
                                                $testListTable->prepare_items();
                                                $testListTable->display();
                                                ?>
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
            jQuery(document).ready(() => {

                jQuery('#add-new-leave-salary').click(() => {
                    jQuery('#add-new-leave-salary-form').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
                jQuery('#add-new-quotation').click(() => {
                    jQuery('#welcome-to-aquila').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
                jQuery('#open-close-menu').click(() => {
                    jQuery('#collapse-button').trigger('click');
                });

            });
        </script>
        <?php
    }
}
