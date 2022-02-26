<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Client_List_Table extends WP_List_Table {

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
            case 'email':
            case 'phone':
            case 'address':
            case 'trn':
            case 'city':
            case 'country':
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
            'name' => __('Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'address' => __('Address'),
            'trn' => __('TRN #'),
            'city' => __('City'),
            'country' => __('Country'),
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

        $id = !empty($uid) ? " id like '%{$uid}%' " : 1;
        $name = !empty($name) ? " name like '%{$name}%' " : 1;
        $email = !empty($email) ? " email like '%{$email}%' " : 1;
        $phone = !empty($phone) ? " phone like '%{$phone}%' " : 1;
        $address = !empty($address) ? " address like '%{$address}%' " : 1;
        $trn = !empty($trn) ? " trn like '%{$trn}%' " : 1;
        $city = !empty($city) ? " city like '%{$city}%' " : 1;
        $country = !empty($country) ? " country like '%{$country}%' " : 1;
        $client_status = !empty($client_status) ? " status = '{$client_status}' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$id} AND {$name} AND {$email} AND {$phone} AND {$address} AND {$trn} AND {$city} AND {$country} AND $client_status";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_clients $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['name'] = $value->name;
                $listArr[$i]['email'] = $value->email . '<br/>' . $value->email2;
                $listArr[$i]['phone'] = $value->phone . '<br/>' . $value->phone2;
                $listArr[$i]['address'] = $value->address;
                $listArr[$i]['trn'] = $value->trn;
                $listArr[$i]['city'] = $value->city;
                $listArr[$i]['country'] = $value->country;
                $listArr[$i]['status'] = $value->status == 'Active' ? "<span style='color:green'>Active</span>" : "<span style='color:red'>Inactive</span>";
                $listArr[$i]['created_by'] = get_user($value->created_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href=" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit title=Edit class=btn-edit><img alt='' src='{$asset_url}edit.png'></a> | "
                        . (has_role_super_and_admin() ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' title=Delete class=btn-delete><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_clients $where"); //count($rows);
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

function admin_ctm_client_list() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');

    $name = !empty($postdata['name']) ? trim($postdata['name']) : (!empty($getdata['name']) ? trim($getdata['name']) : '');
    $email = !empty($postdata['email']) ? trim($postdata['email']) : (!empty($getdata['email']) ? trim($getdata['email']) : '');
    $email2 = !empty($postdata['email2']) ? trim($postdata['email2']) : (!empty($getdata['email2']) ? trim($getdata['email2']) : '');
    $phone = !empty($postdata['phone']) ? trim($postdata['phone']) : (!empty($getdata['phone']) ? trim($getdata['phone']) : '');
    $phone2 = !empty($postdata['phone2']) ? trim($postdata['phone2']) : (!empty($getdata['phone2']) ? trim($getdata['phone2']) : '');
    $address = !empty($postdata['address']) ? trim($postdata['address']) : (!empty($getdata['address']) ? trim($getdata['address']) : '');
    $trn = !empty($postdata['trn']) ? trim($postdata['trn']) : (!empty($getdata['trn']) ? trim($getdata['trn']) : '');
    $city = !empty($postdata['city']) ? trim($postdata['city']) : (!empty($getdata['city']) ? trim($getdata['city']) : '');
    $country = !empty($postdata['country']) ? trim($postdata['country']) : (!empty($getdata['country']) ? trim($getdata['country']) : '');
    $status = !empty($postdata['status']) ? trim($postdata['status']) : (!empty($getdata['status']) ? $getdata['status'] : 'Inactive');

    $data = ['name' => $name, 'email' => $email, 'email2' => $email2, 'phone' => $phone,
        'phone2' => $phone2, 'address' => $address, 'trn' => $trn, 'city' => $city, 'country' => $country, 'status' => $status, 'created_by' => $current_user->ID,
        'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

    if ($action == 'Add') {
        $wpdb->insert("{$wpdb->prefix}ctm_clients", $data, wpdb_data_format($data));
        wp_redirect("?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'Update') {
        unset($data['created_by']);
        unset($data['created_at']);
        $wpdb->update("{$wpdb->prefix}ctm_clients", $data, ['id' => $id], wpdb_data_format($data), ['%d']);
        wp_redirect("?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_clients WHERE id={$id}");
        wp_redirect("?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        $uid = !empty($getdata['uid']) ? $getdata['uid'] : '';
        $client_status = !empty($getdata['client_status']) ? $getdata['client_status'] : '';
        //Create an instance of our package class...
        $testListTable = new CTM_Client_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        if ($action && $id && $action == 'edit') {
            $client = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_clients WHERE id={$id}");
        }
        $countries = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}ctm_country WHERE id IN (select distinct concat(country_id) from {$wpdb->prefix}ctm_locations) ORDER BY name asc");
        ?>
        <style>
            #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
            .wp-list-table{table-layout: auto!important;}
        </style>
        <div class="wrap">
            <h1 class="wp-heading-inline">Client Master </h1>
            <?php if (empty($id)) { ?>
                <span  id="add-new-client" class="page-title-action">Add New</span> 
                <a  href='<?= get_template_directory_uri() ?>/export/export-clients.php' class="page-title-action">Export</a>  
            <?php } ?>

            <br/><br/>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-client-form" class="<?= $id ? '' : 'hide' ?>" method="post">
                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <?php if ($id) { ?>
                                            <input type="hidden" name="id" value="<?= $id ?>" />
                                        <?php } ?>
                                        <table class="form-table" style="width:100%">
                                            <tr>
                                                <td><label>Name:<span class="text-red">*</span></label>
                                                    <input type="text" name="name" id="name"  placeholder="Name" value="<?= !empty($client->name) ? $client->name : $name ?>" required >
                                                    <span id="res-name" class="text-red"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Primary Email:<span class="text-red">*</span></label>
                                                    <input type="email" name="email" id="email"  placeholder="Email"  value="<?= !empty($client->email) ? $client->email : $email ?>" required >
                                                    <span id="res-email" class="text-red"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Secondary Email:</label>
                                                    <input type="email" name="email2"  placeholder="Email"  value="<?= !empty($client->email2) ? $client->email2 : $email2 ?>" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Primary Phone:<span class="text-red">*</span></label>
                                                    <input type="text" name="phone"  placeholder="Primary Phone" value="<?= !empty($client->phone) ? $client->phone : $phone ?>" required maxlength="20">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Secondary Phone:</label>
                                                    <input type="text" name="phone2"  placeholder="Secondary Phone" value="<?= !empty($client->phone2) ? $client->phone2 : $phone2 ?>" maxlength="20">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Delivery Address:<span class="text-red">*</span></label>
                                                    <textarea  name="address"  placeholder="Delivery Address" rows="3" required ><?= !empty($client->address) ? $client->address : $address ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>TRN #:</label>
                                                    <input type="text" name="trn"  placeholder="TRN #" value="<?= !empty($client->trn) ? $client->trn : $trn ?>"  maxlength="20">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Country:</label>

                                                    <select name="country" id='country' class="chosen-select" onchange="change_country(this.id)">
                                                        <option value="">Select Country</option>
                                                        <?php
                                                        foreach ($countries as $value) {
                                                            $cities = $wpdb->get_results("SELECT id,city FROM {$wpdb->prefix}ctm_locations where country_id='{$value->id}'")
                                                            ?>
                                                            <option value="<?= $value->name ?>"  <?= !empty($client->country) && $client->country == $value->name ? 'selected' : '' ?> data-cities='<?= json_encode($cities) ?>'><?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>City:</label>
                                                    <select name="city" class="chosen-select" id='city'>
                                                        <option value="">Select City</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><label>Status:</label>
                                                    <select  name="status" class="search-input">
                                                        <option value="">Status</option>
                                                        <option value="Active" <?= !empty($client) && $client->status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="Inactive" <?= !empty($client) && $client->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <br/>
                                                    <input type="submit"  name="action" value="<?= $id ? 'Update' : 'Add' ?>" class="button-primary"  >
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
                                                        <td>
                                                            <input type="text" name="uid" value="<?= $uid ?>" class="search-input" placeholder="Search by ID"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="name" value="<?= $name ?>" class="search-input" placeholder="Search by Name"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="email" value="<?= $email ?>"  class="search-input" placeholder="Search by Email"  >
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="phone" value="<?= $phone ?>" class="search-input" placeholder="Search by Phone"  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="address"  value="<?= $address ?>" class="search-input" placeholder="Search by Address."  >
                                                        </td>
                                                        <td>
                                                            <input type="text" name="trn" value="<?= $trn ?>" class="search-input" placeholder="Search by TRN"  >
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <select  name="client_status" class="search-input" onchange="this.form.submit()">
                                                                <option value="">Status</option>
                                                                <option value="Active" <?= $client_status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                                <option value="Inactive" <?= $client_status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </td>

                                                        <td colspan="2"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
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
            jQuery(document).ready(() => {

                jQuery('#add-new-client').click(() => {
                    jQuery('#add-new-client-form').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');

                });

                jQuery('#name,#email').on('blur', function () {
                    var name = jQuery('#name').val();
                    var email = jQuery('#email').val();
                    jQuery.ajax({
                        url: "<?= get_template_directory_uri() ?>/ajax/check-client.php",
                        dataType: 'json',
                        data: {name: name, email: email},
                        success: function (data) {
                            if (data.name === true && name !== '') {
                                jQuery('#res-name').text("Client Name already exists.");
                            } else {
                                jQuery('#res-name').text("");
                            }

                            if (data.email === true && email !== '') {
                                jQuery('#res-email').text("Client Email already exists.");
                            } else {
                                jQuery('#res-email').text("");
                            }
                        }
                    });
                });

                change_country('country');

            });
            function change_country(id) {
                jQuery('#city').empty();
                var city = '<?= !empty($client->city) ? $client->city : '' ?>';
                var options = ' <option value="">Select City</option>';
                var cities = jQuery(`#${id}`).find(':selected').data('cities');
                jQuery.each(cities, (i, item) => {
                    var selected = (city == item.city ? 'selected' : '');
                    options += `<option value='${item.city}' ${selected}>${item.city}</option>`;
                });
                jQuery('#city').append(options);
                jQuery('#city').trigger("chosen:updated");

            }
        </script>
        <?php
    }
}
