<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Location_List_Table extends WP_List_Table {

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
            case 'city':
            case 'country':
            case 'local_freight_charge':
            case 'export_freight_charge':
            case 'local_discount':
            case 'export_discount':
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
            'city' => __('City'),
            'country' => __('Country'),
            'local_freight_charge' => __('Local Freight Charge (%)'),
            'export_freight_charge' => __('Export Freight Charge (%)'),
            'local_discount' => __('Local Discount (%)'),
            'export_discount' => __('Export Discount (%)'),
            'status' => __('Status'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'city' => array('city', true),
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

        $city = !empty($city) ? " city like '%{$city}%' " : 1;
        $country_id = !empty($country_id) ? " country_id = '{$country_id}' " : 1;
        $loc_status = !empty($loc_status) ? " status = '{$loc_status}' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$city} AND {$country_id} AND $loc_status";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_locations $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $asset_url = get_template_directory_uri() . '/assets/images/';
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['city'] = $value->city;
                $listArr[$i]['country'] = get_country($value->country_id, 'name');
                $listArr[$i]['local_freight_charge'] = $value->local_freight_charge;
                $listArr[$i]['export_freight_charge'] = $value->export_freight_charge;
                $listArr[$i]['local_discount'] = $value->local_discount;
                $listArr[$i]['export_discount'] = $value->export_discount;
                $listArr[$i]['status'] = $value->status == 'Active' ? "<span style='color:green'>Active</span>" : "<span style='color:red'>Inactive</span>";
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&action=edit' title=Edit class=btn-edit><img alt='' src='{$asset_url}edit.png'></a> | "
                        . (has_role_super_and_admin() ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' title=Delete class=btn-delete ><img alt='' src='{$asset_url}delete.png'></a><br/>" : "");
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'city';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_locations $where"); //count($rows);
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

function admin_ctm_locations_list() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');

    $country_id = !empty($postdata['country_id']) ? $postdata['country_id'] : (!empty($getdata['country_id']) ? $getdata['country_id'] : '');
    $city = !empty($postdata['city']) ? trim($postdata['city']) : (!empty($getdata['city']) ? trim($getdata['city']) : '');
    $local_freight_charge = !empty($postdata['local_freight_charge']) ? trim($postdata['local_freight_charge']) : '';
    $export_freight_charge = !empty($postdata['export_freight_charge']) ? trim($postdata['export_freight_charge']) : '';
    $local_discount = !empty($postdata['local_discount']) ? trim($postdata['local_discount']) : '';
    $export_discount = !empty($postdata['export_discount']) ? trim($postdata['export_discount']) : '';
    $status = !empty($postdata['status']) ? trim($postdata['status']) : (!empty($getdata['status']) ? $getdata['status'] : 'Inactive');

    $data = ['city' => $city, 'local_freight_charge' => $local_freight_charge, 'export_freight_charge' => $export_freight_charge, 'local_discount' => $local_discount, 'export_discount' => $export_discount, 'country_id' => $country_id, 'status' => $status, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

    if ($action == 'Add') {
        $wpdb->insert("{$wpdb->prefix}ctm_locations", $data, wpdb_data_format($data));
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'Update') {
        unset($data['created_by'], $data['created_at']);
        $wpdb->update("{$wpdb->prefix}ctm_locations", $data, ['id' => $id], wpdb_data_format($data), ['%d']);
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_locations WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_Location_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country ORDER BY name ASC");
        $loc_status = !empty($getdata['loc_status']) ? $getdata['loc_status'] : '';
        if ($id && $action == 'edit') {
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_locations WHERE id={$id}");
        }
        ?>
        <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
            #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
            #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
            #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
            .chosen-container{width: 100%!important; min-width: 300px;}
            table.wp-list-table {table-layout: initial!important;}
            .wp-list-table tr th,.wp-list-table tr td{white-space: nowrap;}
        </style>
        <div class="wrap">
            <h1 class="wp-heading-inline">Quotation Structure</h1>
            <span href="#" id="add-new-location" class="page-title-action">Add New</span><br/><br/>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <form id="add-new-location-form" class="<?= $id ? '' : 'hide' ?>" method="post">

                                        <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                        <?php if ($id) { ?>
                                            <input type="hidden" name="id" value="<?= $id ?>" />
                                        <?php } ?>
                                        <table class="form-table" style="width:100%">
                                            <tr>
                                                <td><label>City:<span class="text-red">*</span></label>
                                                    <input type="text" name="city" placeholder="City Name"  
                                                           value="<?= !empty($row) ? $row->city : $city; ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Country:<span class="text-red">*</span></label>
                                                    <select name="country_id" class="chosen-select" required>
                                                        <option value="">Select Country</option> 
                                                        <?php foreach ($countries as $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= !empty($row) && $row->country_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><label>Local Freight Charge (%):<span class="text-red">*</span></label>
                                                    <input type="number" name="local_freight_charge" placeholder="Freight Charge" step="0.01"  
                                                           value="<?= !empty($row) ? $row->local_freight_charge : $local_freight_charge ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Export Freight Charge (%):<span class="text-red">*</span></label>
                                                    <input type="number" name="export_freight_charge" placeholder="Freight Charge" step="0.01"  
                                                           value="<?= !empty($row) ? $row->export_freight_charge : $export_freight_charge ?>" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Local Discount (%):<span class="text-red">*</span></label>
                                                    <input type="number" name="local_discount" placeholder="Discount" maxlength="20" step="0.01"   
                                                           value="<?= !empty($row) ? $row->local_discount : $local_discount ?>" required>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><label>Export Discount (%):<span class="text-red">*</span></label>
                                                    <input type="number" name="export_discount" placeholder="Discount" maxlength="20" step="0.01"   
                                                           value="<?= !empty($row) ? $row->export_discount : $export_discount ?>" required>
                                                </td>
                                            </tr>
                                            <?php
                                            if (has_this_role()) {
                                                ?>
                                                <tr>
                                                    <td colspan="2"><label>Status:</label>
                                                        <select  name="status" class="search-input">
                                                            <option value="">Status</option>
                                                            <option value="Active" <?= !empty($row) && $row->status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                            <option value="Inactive" <?= !empty($row) && $row->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>
                                                    <br/><input type="submit"  name="action" value="<?= $id ? 'Update' : 'Add' ?>" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                                            </tr>
                                        </table>
                                        <br/>
                                    </form>
                                    <?php
                                    if (!$id) {
                                        $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country WHERE id IN (SELECT group_concat(country_id) FROM {$wpdb->prefix}ctm_locations group by country_id) ORDER BY name ASC");
                                        ?>
                                        <div id="page-inner-content">
                                            <form id="filter-form1" method="get">
                                                <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                                <table class="form-table">
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="city"  value="<?= $city ?>" class="search-input" placeholder="Search by City" maxlength="20" size="15" >
                                                        </td>
                                                        <td>
                                                            <select name="country_id" class="chosen-select" onchange="this.form.submit()">
                                                                <option value="">Select Country</option> 
                                                                <?php foreach ($countries as $value) { ?>
                                                                    <option value="<?= $value->id ?>" <?= $country_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select  name="loc_status" class="search-input" onchange="this.form.submit()">
                                                                <option value="">Status</option>
                                                                <option value="Active" <?= $loc_status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                                <option value="Inactive" <?= $loc_status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
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
                jQuery('.chosen-select').chosen();
                jQuery('#add-new-location').click(() => {
                    jQuery('#add-new-location-form').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
            });
        </script>
        <?php
    }
}
