<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Supplier_List_Table extends WP_List_Table {

    function __construct() {
        global $status, $page;
        //Set parent defaults
        parent::__construct(array(
            'singular' => 'delete', //singular name of the listed records
            'plural' => 'deletes', //plural name of the listed records
            'ajax' => false  //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'sup_code':
            case 'address':
            case 'email':
            case 'phone':
            case 'contact_person':
            case 'credit_terms':
            case 'iban':
            case 'swift_code':
            case 'trn':
            case 'type':
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
            'name' => __('Supplier<br/>Name'),
            'sup_code' => __('Supplier<br/>Code'),
            'address' => __('Address'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'contact_person' => __('Contact<br/>Person'),
            'credit_terms' => __('Credit<br/>terms'),
            'iban' => __('IBAN'),
            'swift_code' => __('Swift<br/>Code'),
            'trn' => __('TRN'),
            'type' => __('Type'),
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

        $id = !empty($sid) ? " id like '%{$sid}%' " : 1;
        $name = !empty($name) ? " name like '%{$name}%' " : 1;
        $sup_code = !empty($sup_code) ? " sup_code like '%{$sup_code}%' " : 1;
        $address = !empty($address) ? " address like '%{$address}%' " : 1;
        $email = !empty($email) ? " email like '%{$email}%' " : 1;
        $phone = !empty($phone) ? " phone like '%{$phone}%' " : 1;
        $contact_person = !empty($contact_person) ? " contact_person like '%{$contact_person}%' " : 1;
        $credit_terms = !empty($credit_terms) ? " credit_terms like '%{$credit_terms}%' " : 1;
        $iban = !empty($iban) ? " iban like '%{$iban}%' " : 1;
        $swift_code = !empty($swift_code) ? " swift_code like '%{$swift_code}%' " : 1;
        $sup_type = !empty($sup_type) ? " sup_type = '{$sup_type}' " : 1;
        $trn = !empty($trn) ? " trn like '%{$trn}%' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$id} AND {$name} AND {$sup_code} AND {$address} AND {$email} AND {$phone} AND {$contact_person} AND {$credit_terms}  AND {$iban}  AND {$swift_code} AND {$sup_type} AND {$trn}";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_suppliers $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $asset_url = get_template_directory_uri() . '/assets/images/';
        $i = 0;
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['name'] = $value->name;
                $listArr[$i]['sup_code'] = $value->sup_code;
                $listArr[$i]['address'] = $value->address;
                $listArr[$i]['email'] = $value->email;
                $listArr[$i]['phone'] = $value->phone;
                $listArr[$i]['contact_person'] = $value->contact_person;
                $listArr[$i]['credit_terms'] = $value->credit_terms;
                $listArr[$i]['iban'] = $value->iban;
                $listArr[$i]['swift_code'] = $value->swift_code;
                $listArr[$i]['trn'] = $value->trn;
                $listArr[$i]['type'] = $value->sup_type == 'Local' ? "<span class='badge badge-dark'>Local</span>" : "<span class='badge badge-success'>International</span>";
                 $listArr[$i]['status'] = $value->status == 'Active' ? "<span style='color:green'>Active</span>" : "<span style='color:red'>Inactive</span>";
                $listArr[$i]['updated_at'] = rb_date($value->updated_at) . '<br/>' . rb_time($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}-edit&id={$value->id}' title=Edit class=btn-edit><img alt='' src='{$asset_url}edit.png'></a> | " .
                        (has_role_super_and_admin() ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' title=Delete class=btn-delete ><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'name';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_suppliers $where"); //count($rows);
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

function admin_ctm_supplier_list() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';


    $name = !empty($getdata['name']) ? trim($getdata['name']) : '';
    $sup_code = !empty($getdata['sup_code']) ? trim($getdata['sup_code']) : '';
    $address = !empty($getdata['address']) ? trim($getdata['address']) : '';
    $email = !empty($getdata['email']) ? trim($getdata['email']) : '';
    $phone = !empty($getdata['phone']) ? trim($getdata['phone']) : '';
    $contact_person = !empty($getdata['contact_person']) ? trim($getdata['contact_person']) : '';
    $credit_terms = !empty($getdata['credit_terms']) ? trim($getdata['credit_terms']) : '';
    $iban = !empty($getdata['iban']) ? trim($getdata['iban']) : '';
    $swift_code = !empty($getdata['swift_code']) ? trim($getdata['swift_code']) : '';
    $trn = !empty($getdata['trn']) ? trim($getdata['trn']) : '';
    $sup_type = !empty($getdata['sup_type']) ? trim($getdata['sup_type']) : '';
    $status = !empty($getdata['status']) ? trim($getdata['status']) : '';


    if ($action && $id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_suppliers WHERE id={$id}");
        wp_redirect("admin.php?page={$page}&msg=delete");
        $msg = 'deleted';
    }
    $sid = !empty($getdata['sid']) ? $getdata['sid'] : '';
    //Create an instance of our package class...
    $testListTable = new CTM_Supplier_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Supplier Master</h1>
        <a href="<?= admin_url('admin.php?page=rw-suppliers-add') ?>" id="add-new-item" class="page-title-action">Add New</a><br/><br/>
        <?php if (!empty($getdata['msg']) && $getdata['msg'] == 'Added') { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Supplier has been added successfully.
            </div>
            <?php
        }
        if (!empty($msg)) {
            ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Supplier has been deleted successfully.
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
                                        <input type="hidden" name="page" value="<?= $page ?>" />
                                        <table class="form-table">
                                            <tr>
                                                <td><label>Supplier Type:<span class="text-red">*</span></label><br/>
                                                    <label class="font-weight-normal m-0">
                                                        <input type="radio" name="sup_type" class="type" value="Local"   onclick="this.form.submit()" 
                                                               <?= $sup_type == 'Local' ? 'checked' : '' ?>  required>Local</label>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <label class="font-weight-normal m-0">
                                                        <input type="radio" name="sup_type"  class="type" value="International"  onclick="this.form.submit()"
                                                               <?= $sup_type == 'International' ? 'checked' : (empty($sup_type) ? 'checked' : '') ?> required>International</label>
                                                </td>
                                                <td>
                                                    <input type="text" name="sid" value="<?= $sid ?>"  placeholder="Search by ID" >
                                                </td>
                                                <td>
                                                    <input type="text" name="name" value="<?= $name ?>"  placeholder="Search by name" >
                                                </td>
                                            </tr><tr>
                                                <td>
                                                    <input type="text" name="sup_code"  value="<?= $sup_code ?>"   placeholder="Search by Supplier code" >
                                                </td>
                                                <td>
                                                    <input type="text" name="address"  value="<?= $address ?>"   placeholder="Search by address" >
                                                </td>
                                                <td>
                                                    <input type="text" name="email" value="<?= $email ?>"  placeholder="Search by email" >
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="phone" value="<?= $phone ?>"  placeholder="Search by phone" >
                                                </td>
                                                <td>
                                                    <input type="text" name="contact_person" value="<?= $contact_person ?>" placeholder="Search by contact person" >
                                                </td>
                                                <td>
                                                    <input type="text" name="credit_terms" value="<?= $credit_terms ?>"  placeholder="Search by credit terms" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="iban" value="<?= $iban ?>" placeholder="Search by iban" >
                                                </td>
                                                <td>
                                                    <input type="text" name="swift_code" value="<?= $swift_code ?>" placeholder="Search by swift code" >
                                                </td>
                                                <td>
                                                    <select  name="status" class="search-input">
                                                        <option value="">Status</option>
                                                        <option value="Active" <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="Inactive" <?= $status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
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

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form id="deletes-filter" method="get">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $page ?>" />
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
        var trn = '<?= !empty($trn) ? $trn : '' ?>';
        jQuery(document).ready(() => {

            if (trn === '') {
                jQuery('#trn-div').hide();
            } else {
                jQuery('input.local,textarea.local').val('').removeAttr('required');
                jQuery('span.local').hide();
            }
            jQuery('.sup_type').click(function () {
                type = jQuery('input[name="sup_type"]:checked').val();
                if (type === 'Local') {
                    jQuery('#trn-div').show();
                    jQuery('#trn').val(trn).prop('required', true);
                    jQuery('input.local,textarea.local').val('').removeAttr('required');
                    jQuery('span.local').hide();
                }
                if (type === 'International') {
                    jQuery('#trn-div').hide();
                    jQuery('#trn').val('').removeAttr('required');

                    jQuery('input.local,textarea.local').prop('required', true);
                    jQuery('span.local').show();

                }
            });
            jQuery('.chosen-select').chosen();
        });
    </script>
    <?php
}
