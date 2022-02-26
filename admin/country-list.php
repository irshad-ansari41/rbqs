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
            case 'city':
            case 'country':
            case 'freight_charge':
            case 'discount':
            //case 'created_at':
            case 'updated_at':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'city' => __('City'),
            'country' => __('Country'),
            'freight_charge' => __('Freight Charge'),
            'discount' => __('Discount'),
            //'created_at' => __('Created At'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array();
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
        $keyword = !empty($keyword) ? " city like  '%" . $keyword . "%' " : 1;
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_country WHERE $keyword LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $listArr[$i]['country'] = $value->country;
                $listArr[$i]['city'] = $value->city;
                $listArr[$i]['freight_charge'] = $value->freight_charge;
                $listArr[$i]['discount'] = $value->discount;
                //$listArr[$i]['created_at'] = rb_datetime($value->created_at);
                //$listArr[$i]['created_at'] = rb_datetime($value->created_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}&id={$value->id}&city={$value->city}&freight_charge={$value->freight_charge}&discount={$value->discount}&country={$value->country}'>Edit</a> | <a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class='text-red' >Delete</a><br/>";
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

        //$rows = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_country");
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_country"); //count($rows);
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

function admin_ctm_country_list() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $date = current_time('mysql');
    $country = !empty($getdata['country']) ? $getdata['country'] : '';
    $city = !empty($getdata['city']) ? $getdata['city'] : '';
    $freight_charge = !empty($getdata['freight_charge']) ? $getdata['freight_charge'] : '';
    $discount = !empty($getdata['discount']) ? $getdata['discount'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';
    $fields = "city='$city',freight_charge='{$freight_charge}',discount='{$discount}',country='{$country}'";

    if (!empty($getdata['action']) && $getdata['action'] == 'Add') {
        $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_country SET {$fields},created_at='{$date}'");
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($action && $id && $getdata['action'] == 'Update') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_country SET $fields WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=updated");
        exit();
    } else if ($action && $id && $getdata['action'] == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_country WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_Country_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country ORDER BY name ASC");
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }table th#id{width:30px}
            table th#user_id{width:50px}
            .text-red{color: red!important}
            .hide{display:none!important}
            .form-control{width:100%;}
            #filter-form{ margin-bottom: -40px;margin-top: 20px;}
        </style>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h1 class="wp-heading-inline">Country List </h1>
            <span href="#" id="add-new-country" class="page-title-action">Add New</span>

            <form id="add-new-country-form" class="<?= $id ? '' : 'hide' ?>" method="get">
                <br/>
                <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                <?php if ($id) { ?>
                    <input type="hidden" name="id" value="<?= $id ?>" />
                <?php } ?>
                <table class="form-table">
                    <tr>
                        <td>Country:<br/>
                            <select name="country" class="form-control"  required>
                                <option value="">Select Country</option> 
                                <?php foreach ($countries as $value) { ?>
                                    <option value="<?= $value->id ?>" <?= $country == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>City:<br/>
                            <input type="text" name="city" class="form-control" placeholder="City Name"  value="<?= $city ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Freight Charge(%):<br/>
                            <input type="number" name="freight_charge" class="form-control" placeholder="Freight Charge"  value="<?= $freight_charge ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Discount(%):<br/>
                            <input type="number" name="discount" class="form-control" placeholder="Discount"  value="<?= $discount ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br/><input type="submit"  name="action" value="<?= $id ? 'Update' : 'Add' ?>" class="button-primary"  >
                            &nbsp;&nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Cancel</a></td>
                    </tr>
                </table>
                <br/><br/><br/>
            </form>
            <?php if (!$id) { ?>
                <div id="page-inner-content">
                    <form id="filter-form1" method="get">
                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                        <table class="form-table">
                            <tr>
                                <td><input type="text" name="keyword" id="keyword" class="search-input" placeholder="Search by keyword..."  ></td>
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
        <script>
            jQuery('#add-new-country').click(() => {
                jQuery('#add-new-country-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });
        </script>
        <?php
    }
}
