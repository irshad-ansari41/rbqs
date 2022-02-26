<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Quotation_List_Table extends WP_List_Table {

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
            case 'revised_no':
            case 'client_name':
            case 'type':
            case 'scope':
            case 'vat':
            case 'city':
            case 'country':
            case 'status':
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
            'id' => __('Quotation ID'),
            'revised_no' => __('Revised No'),
            'client_name' => __('Client Name'),
            'type' => __('Quotation<br/>Type'),
            'scope' => __('Scope'),
            'vat' => __('Vat<br/>Status'),
            'city' => __('City'),
            'country' => __('Country'),
            'status' => __('Status'),
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


        $keyword = !empty($keyword) ? " (id like  '%" . $keyword . "%' OR  revised_no like  '%" . $keyword . "%') " : 1;
        $qtn_scope = !empty($qtn_scope) ? " scope ='{$qtn_scope}'" : 1;
        $qtn_type = !empty($qtn_type) ? " type ='{$qtn_type}'" : 1;
        $client_id = !empty($client_id) ? " client_id ={$client_id}" : 1;
        $city_id = !empty($city_id) ? " city_id ={$city_id}" : 1;
        $country_id = !empty($country_id) ? " country_id ={$country_id}" : 1;
        $from_date = !empty($from_date) ? "created_at>='{$from_date} 00:00:01'" : 1;
        $to_date = !empty($to_date) ? "created_at<='{$to_date} 23:59:59'" : 1;
        $status = !empty($status) ? " status ='{$status}'" : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE trash=0 AND is_showroom=0 AND $keyword AND $qtn_scope AND $qtn_type AND $client_id AND $country_id AND $city_id AND {$from_date} AND {$to_date} AND $status";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotations $where ORDER BY {$orderby} {$order}  LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $sales_person = get_sales_person($value->sales_person);

                $id = hide_edit($value) ? "<a href='" . admin_url() . "admin.php?page={$page}-edit&id={$value->id}' class='btn-edit' title='Edit'>{$value->id}</a>" : $value->id;

                $listArr[$i]['id'] = $id;
                $listArr[$i]['revised_no'] = $value->revised_no;
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['type'] = $value->type;
                $listArr[$i]['scope'] = $value->scope;
                $listArr[$i]['vat'] = $value->vat == 'wovat' ? 'Zero VAT' : '';
                $listArr[$i]['city'] = get_location($value->city_id, 'city');
                $listArr[$i]['country'] = get_country($value->country_id, 'name');
                $listArr[$i]['status'] = show_qtn_status($value);
                $listArr[$i]['updated_by'] = 'SP ' . $sales_person->sp_id . ' / ' . $sales_person->display_name;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $view = $edit = $delete = $invoice = '';
                if ($value->status != 'Draft') {
                    $view = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}' class='btn-view' title='View' ><img alt='' src='{$asset_url}view.png'></a>";
                }
                if (hide_edit($value)) {
                    $edit = " <a href='" . admin_url() . "admin.php?page={$page}-edit&id={$value->id}' class='btn-edit' title='Edit'><img alt='' src='{$asset_url}edit.png'></a>";
                }
                if (in_array('admin', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                    $delete = "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class='btn-delete' title='Delete'><img alt='' src='{$asset_url}delete.png'></a>&nbsp;&nbsp;";
                }
                $listArr[$i]['action'] = $view . $edit . $delete . $invoice;
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
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations $where"); //count($rows);
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

function admin_ctm_quotation_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    $step = !empty($getdata['step']) ? $getdata['step'] : 1;
    $type = !empty($getdata['type']) ? $getdata['type'] : '';
    $scope = !empty($getdata['scope']) ? $getdata['scope'] : '';
    $vat = !empty($getdata['vat']) ? $getdata['vat'] : '';
    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : '';
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : '';
    $city_id = !empty($getdata['city_id']) ? $getdata['city_id'] : '';
    $country_id = !empty($getdata['country_id']) ? $getdata['country_id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';
    $status = !empty($getdata['status']) ? $getdata['status'] : '';
    if ($action && $id && $getdata['action'] == 'delete') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET trash=1 WHERE trash=0 AND id={$id}");
        wp_redirect("?page={$getdata['page']}&msg=delete");
        exit();
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
        .wp-list-table th {text-align: center;}
    </style>
    <div class="wrap">
        <?php if ($step == 1 && !$scope && !$vat && !$id) { ?>
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
            <h1 class="wp-heading-inline">Quotation Registry</h1>
            <span  id="add-new-quotation" class="page-title-action <?= $action == 'create' ? 'hide' : '' ?>">Add New</span>
            <br/><br/>
        <?php } ?>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php if ($step == 1) { ?>
                            <div id="welcome-to-aquila" class="postbox <?= $action != 'create' ? 'hide' : '' ?> ">
                                <h2 class="hndle ui-sortable-handle text-center"><span>Please select quotation type</span></h2>
                                <div class="inside text-center">
                                    <a href="admin.php?page=<?= $page ?>&step=2&type=Stock"><div class="new-quotation">Stock Quotation</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=2&type=Order"><div class="new-quotation">Order Quotation</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=2&type=Project"><div class="new-quotation">Project Quotation</div></a>
                                    <a href="<?= gen_quote_back_url($page, $step, $type, $scope) ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                                </div>
                            </div>
                        <?php } ?>


                        <?php if ($step == 2 && $type == 'Stock') { ?>
                            <div id="welcome-to-aquila" class="postbox">
                                <h2 class="hndle ui-sortable-handle text-center"><span><?= $type ?> Quotation</span></h2>
                                <div class="inside text-center">
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Stock&scope=Local"><div class="new-quotation">Local (UAE)</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Stock&scope=Export"><div class="new-quotation">Export</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Stock&scope=Promotion"><div class="new-quotation">Promotion</div></a>
                                    <a href="<?= gen_quote_back_url($page, $step, $type, $scope) ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                                </div>
                            </div>
                        <?php } ?>


                        <?php if ($step == 2 && $type == 'Order') { ?>
                            <div id="welcome-to-aquila" class="postbox">
                                <h2 class="hndle ui-sortable-handle text-center"><span><?= $type ?> Quotation</span></h2>
                                <div class="inside text-center">
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Order&scope=Local"><div class="new-quotation">Local (UAE)</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Order&scope=Export"><div class="new-quotation">Export</div></a>
                                    <a href="admin.php?page=<?= $page ?>&step=3&type=Order&scope=Promotion"><div class="new-quotation">Promotion</div></a>
                                    <a href="<?= gen_quote_back_url($page, $step, $type, $scope) ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                                </div>
                            </div>
                        <?php } ?>


                        <?php
                        if ($step == 3 && ($type == 'Stock' || $type == 'Order') && $scope == 'Local') {
                            wp_redirect(admin_url("admin.php?page=quotation-create&step={$step}&type={$type}&scope={$scope}"));
                            exit();
                        }
                        ?>


                        <?php if ($step == 3 && ($type == 'Stock' || $type == 'Order') && $scope == 'Export') { ?>
                            <div id="welcome-to-aquila" class="postbox">
                                <h2 class="hndle ui-sortable-handle text-center"><span><?= $type ?> Quotation</span></h2>
                                <div class="inside text-center">
                                    <?= "{$type} -> {$scope}" ?><br/>
                                    <!--<a href="<?= "admin.php?page={$page}&step=4&type={$type}&scope={$scope}&vat=wvat" ?>"><div class="new-quotation">Export w/ VAT</div></a>-->
                                    <a href="<?= "admin.php?page={$page}&step=4&type={$type}&scope={$scope}&vat=wovat" ?>"><div class="new-quotation">Export Zero VAT</div></a>
                                    <a href="<?= gen_quote_back_url($page, $step, $type, $scope) ?>" class="btn btn-secondary text-white">&lt; - Back</a>
                                </div>
                            </div>
                        <?php } ?>




                        <?php
                        if ($step == 4 && ($type == 'Order' || $type == 'Stock') && $scope == 'Export' && ($vat == 'wvat' || $vat == 'wovat')) {
                            wp_redirect(admin_url("admin.php?page=quotation-create&step={$step}&type={$type}&scope={$scope}&vat={$vat}"));
                            exit();
                        }
                        ?>

                        <?php
                        if ($step == 3 && ($type == 'Order' || $type == 'Stock') && $scope == 'Promotion') {
                            wp_redirect(admin_url("admin.php?page=quotation-create&step={$step}&type={$type}&scope={$scope}"));
                            exit();
                        }
                        ?>

                        <?php
                        if ($step == 2 && $type == 'Project') {
                            wp_redirect(admin_url("admin.php?page=quotation-create&step={$step}&type={$type}"));
                            exit();
                        }
                        ?>



                        <?php
                        if ($step == 1 && !$scope && !$vat && !$id && $action != 'create') {
                            $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country WHERE id IN (SELECT group_concat(country_id) FROM {$wpdb->prefix}ctm_locations group by country_id) ORDER BY name ASC");
                            $locations = $wpdb->get_results("SELECT id,city as name FROM {$wpdb->prefix}ctm_locations WHERE country_id='{$country_id}'");
                            ?>
                            <div id="page-inner-content" class="postbox">
                                <br/>
                                <div class="inside">
                                    <div id="page-inner-content1">
                                        <form id="filter-form1" method="get">
                                            <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                            <table class="form-table">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="keyword" class="search-input" placeholder="Search by ID."  
                                                               value="<?= !empty($getdata['keyword']) ? $getdata['keyword'] : '' ?>" >
                                                    </td>
                                                    <td>
                                                        <select name="qtn_type" onchange="this.form.submit()">
                                                            <option value="">Select Type</option>
                                                            <option value="Stock" <?= !empty($getdata['qtn_type']) && $getdata['qtn_type'] == 'Stock' ? 'selected' : '' ?>>Stock</option>
                                                            <option value="Order" <?= !empty($getdata['qtn_type']) && $getdata['qtn_type'] == 'Order' ? 'selected' : '' ?>>Order</option>
                                                            <option value="Project" <?= !empty($getdata['qtn_type']) && $getdata['qtn_type'] == 'Project' ? 'selected' : '' ?>>Project</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="qtn_scope" onchange="this.form.submit()">
                                                            <option value="">Select Scope</option>
                                                            <option value="Local" <?= !empty($getdata['qtn_scope']) && $getdata['qtn_scope'] == 'Local' ? 'selected' : '' ?> >Local</option>
                                                            <option value="Export" <?= !empty($getdata['qtn_scope']) && $getdata['qtn_scope'] == 'Export' ? 'selected' : '' ?> >Export</option>
                                                            <option value="Promotion" <?= !empty($getdata['qtn_scope']) && $getdata['qtn_scope'] == 'Promotion' ? 'selected' : '' ?> >Promotion</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                            <option value="">Loading...</option>
                                                        </select>
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
                                                        <select name="city_id" class="chosen-select" onchange="this.form.submit()">
                                                            <option value="">Select Location</option> 
                                                            <?php foreach ($locations as $value) { ?>
                                                                <option value="<?= $value->id ?>" <?= $city_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    
                                                    <td>
                                                        <input type="<?= $from_date ? 'date' : 'text' ?>" name="from_date" class="search-input" value="<?= $from_date ?>" placeholder="From Created Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td>
                                                        <input type="<?= $to_date ? 'date' : 'text' ?>" name="to_date" class="search-input" value="<?= $to_date ?>" placeholder="To Created Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td>
                                                        <select name="status" onchange="this.form.submit()">
                                                            <option value="">Select Status</option> 
                                                            <?php foreach (QTN_STATUS as $value) { ?>
                                                                <option value="<?= $value ?>" <?= $status == $value ? 'selected' : '' ?>><?= $value ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
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
                                            <?php
                                            //Create an instance of our package class...
                                            $testListTable = new CTM_Quotation_List_Table();
                                            //Fetch, prepare, sort, and filter our data...
                                            $testListTable->prepare_items();
                                            $testListTable->display();
                                            ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>


                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#add-new-quotation').click(() => {
                jQuery('#welcome-to-aquila').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('.chosen-select').chosen();
                }
            });

        });
    </script>
    <?php
}
