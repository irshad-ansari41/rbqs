<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Sold_Items_List_Table extends WP_List_Table {

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
            case 'category':
            case 'sup_code':
            case 'client':
            case 'qtn':
            case 'entry':
            case 'description':
            case 'qty':
            case 'item_arraived_date':
            case 'item_sold_date':
            case 'item_cost_price_euro':
            case 'item_cost_price_aed':
            case 'selling_price':
            case 'difference':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID'),
            'collection_name' => __('Collection<br/>Name'),
            'image' => __('Image'),
            'category' => __('Category'),
            'sup_code' => __('Supplier<br/>Code'),
            'client' => __('Customer<br/>Name'),
            'qtn' => __('QTN<br/>No'),
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'qty' => __('QTY'),
            'item_arraived_date' => __('Item Arrived Date'),
            'item_sold_date' => __('Item Sold Date'),
            'item_cost_price_euro' => __('Item Cost Price €'),
            'item_cost_price_aed' => __('Item Cost Price AED'),
            'selling_price' => __('Selling Price'),
            'difference' => __('Difference'),
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
        extract($_GET);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $current_page = $this->get_pagenum();
        $start = ($current_page == 1) ? 0 : ($current_page - 1) * 10;

        $item_desc = !empty($item_desc) ? "t1.item_desc like '%{$item_desc}%'" : 1;
        $category_id = !empty($category_id) ? "t2.category ='{$category_id}'" : 1;
        $sup_code = !empty($sup_code) ? "t1.sup_code ='{$sup_code}'" : 1;
        $con_res_date_from = !empty($from_date) ? "t3.con_res_date >='{$from_date}'" : 1;
        $con_res_date_to = !empty($to_date) ? "t3.con_res_date <='{$to_date}'" : 1;


        $where = "WHERE t1.stk_inv_status='DELIVERED' AND t3.con_res_date IS NOT NULL AND $item_desc AND $category_id AND $sup_code AND $con_res_date_from AND $con_res_date_to ";
        $sql = "SELECT t1.id,t1.quotation_id,t1.revised_no,t1.client_id,t1.item_id,t1.entry,t1.item_desc,t1.quantity,t1.cl_value,t1.arrival_date,t1.currency,t1.container_name,t3.con_res_date "
                . "FROM {$wpdb->prefix}ctm_quotation_po_meta t1 "
                . "LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id "
                . "LEFT JOIN {$wpdb->prefix}ctm_quotations t3 ON t1.quotation_id=t3.id "
                . "$where ORDER BY t3.con_res_date DESC LIMIT $start, 10";

        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {

                $net_price = $wpdb->get_var("SELECT net_price from {$wpdb->prefix}ctm_quotations_meta WHERE entry='{$value->entry}' AND net_price IS NOT NULL");
                $item = get_item($value->item_id);
                $category = get_item_category($item->category, 'name');

                $imp_dec_id = $wpdb->get_var("SELECT id from {$wpdb->prefix}ctm_import_declarations WHERE containers_name like '%{$value->container_name}%'");
                $cs_rate = get_option("cs_coefficients_rate_{$imp_dec_id}");

                $inc_custom_vat = 0;
                if (!empty($cs_rate)) {
                    $item_cost_price = $value->cl_value;
                    $total_price = $value->quantity * $item_cost_price;
                    $landed_cost = $total_price * ($value->currency == 'EURO' ? $cs_rate['euro'] : $cs_rate['usd']);
                    $custom_duty = $landed_cost * $cs_rate['custom'];
                    $inc_custom = $landed_cost + $custom_duty;
                    $input_vat = get_vat_amount($inc_custom);
                    $inc_custom_vat = $inc_custom + $input_vat;
                }

                $listArr[$i]['id'] = "<a href='admin.php?page=stock-inventory-edit&id={$value->id}&action=edit&imp_dec_id={$imp_dec_id}' title=Edit class=btn-edit >$value->id</a>";
                $listArr[$i]['collection_name'] = $item->collection_name;
                $listArr[$i]['image'] = "<a href='" . get_image_src($item->image) . "' target='_image'><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></a>";
                $listArr[$i]['category'] = $category;
                $listArr[$i]['sup_code'] = $item->sup_code;
                $listArr[$i]['client'] = get_client($value->client_id, 'name');
                $listArr[$i]['qtn'] = $value->revised_no??$value->quotation_id    ;
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['qty'] = $value->quantity;

                $listArr[$i]['item_arraived_date'] = rb_date($value->arrival_date);
                $listArr[$i]['item_sold_date'] = !empty($value->con_res_date) ? rb_date($value->con_res_date) : '';
                $listArr[$i]['item_cost_price_euro'] = $value->cl_value;
                $listArr[$i]['item_cost_price_aed'] = number_format($inc_custom_vat, 2);
                $listArr[$i]['selling_price'] = !empty($net_price) ? rb_float($net_price, 2) : 0.00;
                $listArr[$i]['difference'] = number_format($net_price-$inc_custom_vat,2);
                $listArr[$i]['action'] = "<label class='button-secondary'><input type='checkbox'>Reorder</label>";
                $reorder = "<form method=post>"
                        . "<input type='hidden' name='page' value='{$page}' />"
                        . "<input type='hidden'  name='reorder_id' value='{$value->id}' />"
                        . "<input type='hidden'  name='quantity' value='{$value->quantity}' />"
                        . "<button type='submit' name='reorder_form' value=1 style='border: 1px solid #333;'><span class='dashicons dashicons-update-alt'></span></button>"
                        . "</form>";
                $listArr[$i]['action'] = $reorder;

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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quotation_po_meta t1  LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id LEFT JOIN {$wpdb->prefix}ctm_quotations t3 ON t1.quotation_id=t3.id $where ");

        //$count = $num_rows; //count($rows);
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

function admin_ctm_sold_items_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $sup_code = !empty($getdata['sup_code']) ? $getdata['sup_code'] : '';
    $item_desc = !empty($getdata['item_desc']) ? $getdata['item_desc'] : '';
    $category_id = !empty($getdata['category_id']) ? $getdata['category_id'] : '';
    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : '';
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : '';

    $suppliers = get_cache_results("SELECT name,sup_code FROM {$wpdb->prefix}ctm_suppliers ORDER BY sup_code ASC", ['day' => true]);
    $categories = get_cache_results("SELECT id,name FROM {$wpdb->prefix}ctm_item_category ORDER BY name ASC", ['day' => true]);

    if (!empty($postdata['reorder_form'])) {
        make_model_reorder($postdata);
    }

    if (!empty($postdata['make_reorder'])) {
        create_showroom_order($postdata['order_id'], $postdata['reorder_id'], $postdata['quantity'], $postdata['order_type']);
        $msg = 'Item has been added to showroom order successfully.';
    }
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th{white-space: nowrap;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table.wp-list-table th {text-align: center;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Sold Item List</h1>
        <a  href='<?= admin_url("admin.php?page=stock-inventory") ?>' class="page-title-action">Back</a>
        <a  href='<?= admin_url("admin.php?page=sold-items-statistics") ?>' class="page-title-action">Best Seller Statistics</a>
        <br/><br/>

        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> <?= $msg ?>.
            </div>
        <?php } ?>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <div id="page-inner-content" class="postbox">
                            <br/>
                            <div class="inside">
                                <div id="page-inner-content1">
                                    <form id="filter-form1" method="get">
                                        <input type="hidden" name="page" value="<?= $page ?>" />                                       
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <select name="category_id" id="category-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Category</option>
                                                        <?php foreach ($categories as $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= $value->id == $category_id ? 'selected' : '' ?>>
                                                                <?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="item_desc" class="search-input" value="<?= $item_desc ?>" placeholder="Search By Description Keyword"  >
                                                </td>
                                                <td>
                                                    <select name="sup_code" id="supplier-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Supplier</option>
                                                        <?php foreach ($suppliers as $value) { ?>
                                                            <option value="<?= $value->sup_code ?>" <?= !empty($sup_code) && $sup_code == $value->sup_code ? 'selected' : '' ?>>
                                                                <?= $value->sup_code ?> | <?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td style="width:170px">
                                                    <input type="<?= $from_date ? 'date' : 'text' ?>" name="from_date" class="search-input" value="<?= $from_date ?>" placeholder="From Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td style="width:170px">
                                                    <input type="<?= $to_date ? 'date' : 'text' ?>" name="to_date" class="search-input" value="<?= $to_date ?>" placeholder="To Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>

                                            </tr>


                                            <tr>

                                                <td colspan="4">
                                                    <button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>


                                    <?php
                                    //Create an instance of our package class...
                                    $testListTable = new CTM_Sold_Items_List_Table();
                                    //Fetch, prepare, sort, and filter our data...
                                    $testListTable->prepare_items();
                                    $testListTable->display();
                                    ?>

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

            jQuery('.chosen-select').chosen();
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Search By Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-items.php",
                dataType: 'json',
                success: function (data) {
                    var item_id = '<?= !empty($getdata['item_id']) ? $getdata['item_id'] : 0; ?>';
                    jQuery('#item-name').html('');
                    var html = '<option value="">Search By Collection Name</option>';
                    jQuery.each(data, function (i, item) {
                        var selected = item_id === item.id ? 'selected' : '';
                        html += `<option value="${item.id}" ${selected}>${item.collection_name}</option>`;
                    });
                    jQuery('#item-name').html(html);
                    jQuery('#item-name').trigger("chosen:updated");
                }
            });

            jQuery('.text-comment').on('blur', function () {
                var po_id = jQuery(this).data('po_id');
                var comment = jQuery(this).val();

                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/stock-inventory-comment.php",
                    type: "post",
                    dataType: "json",
                    data: {po_id: po_id, comment: comment},
                    success: function (response) {
                        if (response.status) {

                        }
                    }
                });
            });
        });
    </script>
    <?php
}
