<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Stock_inventory_Order_List_Table extends WP_List_Table {

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
            case 'entry':
            case 'description':
            case 'selling_price':
            case 'qty':
            case 'eta':
            case 'cque':
            case 'qtn':
            case 'delivery_note':
            case 'delivery_date':
            case 'hs_code':
            case 'location':
            case 'status':
            case 'comment':
            case 'edit':
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
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'selling_price' => __('Selling<br/>Price'),
            'qty' => __('QTY'),
            'eta' => __('ETA'),
            'cque' => __('CQUE'),
            'qtn' => __('QTN No.'),
            'delivery_note' => __('Delivery<br/>Note'),
            'delivery_date' => __('Delivery<br/>Date'),
            'hs_code' => __('HS Code'),
            'location' => __('Location'),
            'status' => __('Status'),
            'comment' => __('Comment'),
            'edit' => __('Edit'),
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

        $po_meta_id = !empty($po_meta_id) ? " t1.id like '%" . $po_meta_id . "%' " : 1;
        $qid = !empty($qid) ? " (t1.quotation_id like  '%" . $qid . "%' or  t1.revised_no like  '%" . $qid . "%') " : 1;
        $client_id = !empty($client_id) ? " t1.client_id ='{$client_id}'" : 1;
        $item_id = !empty($item_id) ? " t1.item_id ='{$item_id}'" : 1;
        $item_desc = !empty($item_desc) ? " t1.item_desc like '%{$item_desc}%'" : 1;
        $category_id = !empty($category_id) ? " t2.category ='{$category_id}'" : 1;
        $entry = !empty($entry) ? " t1.entry like '%".trim($entry)."%'" : 1;
        $hs_code = !empty($hs_code) ? " t2.hs_code ='{$hs_code}' " : 1;
        $delivery_date = !empty($delivery_date) ? " t1.delivery_date ='{$delivery_date}' " : 1;
        $sup_code = !empty($sup_code) ? " t1.sup_code ='{$sup_code}'" : 1;
        $si_status = !empty($si_status) ? " t1.stk_inv_status ='{$si_status}'" : 1;
        $si_location = !empty($si_location) ? " t1.stk_inv_location ='{$si_location}'" : 1;
        $quantity = !empty($quantity) ? " t1.quantity $quantity" : 't1.quantity>0';

        $where = "WHERE t1.order_registry='ARRIVED' AND $po_meta_id AND $quantity AND $qid AND $client_id AND $item_id AND $item_desc AND $category_id AND $sup_code AND $entry AND $hs_code AND $delivery_date AND $si_status AND $si_location";
        $sql = "SELECT t1.id,t1.quotation_id,t1.client_id,t1.item_id,t1.entry,t1.item_desc,t1.selling_price,t1.quantity,t1.arrival_date,t1.delivery_date,t1.entry, t1.cl_pkgs,t1.stk_inv_location,t1.stk_inv_status,t1.stk_inv_comment,t1.revised_no, t1.receipt_no FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ORDER BY CAST(t1.entry AS UNSIGNED INTEGER)  DESC LIMIT $start, 10";

        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $client = get_client($value->client_id);
                $client_name = !empty($client->name) ? $client->name : '';
                $item = get_item($value->item_id);
                $category = get_item_category($item->category, 'name');
                $dn = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn where client_id='{$value->client_id}' AND quotation_id='{$value->quotation_id}' AND status!='RETURNED'");
                $qtn = $value->revised_no ? $value->revised_no : $value->quotation_id;
                $stf_status = $wpdb->get_var("SELECT status FROM {$wpdb->prefix}ctm_stock_transfer_meta where po_meta_id='$value->id' AND status='Pending'");

                $listArr[$i]['id'] = "<a href='admin.php?page=stock-inventory-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >$value->id</a>";
                $listArr[$i]['collection_name'] = $item->collection_name;
                $listArr[$i]['image'] = "<a href='" . get_image_src($item->image) . "' target='_image'><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></a>";
                $listArr[$i]['category'] = $category;
                $listArr[$i]['sup_code'] = $item->sup_code;
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['selling_price'] = number_format($value->selling_price,2);

                $listArr[$i]['qty'] = $value->quantity;
                $listArr[$i]['eta'] = $value->arrival_date;
                $listArr[$i]['cque'] = $client_name . ' ' . $qtn;
                $listArr[$i]['qtn'] = $qtn;
                $listArr[$i]['delivery_note'] = !empty($dn->id) ? $dn->id : '';
                $listArr[$i]['delivery_date'] = !empty($dn->delivery_date) ? rb_date($dn->delivery_date) : '';
                $listArr[$i]['hs_code'] = $item->hs_code;

                $disabled = $value->stk_inv_status != 'AVAILABLE' ? 'disabled' : '';
                if (has_this_role('accounts')) {
                    $disabled = '';
                }

                $stock_loc_dropdow = "<form method=post>"
                        . "<input type=hidden name=po_meta_id value='$value->id' />"
                        . "<input type=hidden name=quantity value='$value->quantity' />"
                        . "<input type=hidden name=cque value='" . ($client_name . ' ' . $qtn) . "' />"
                        . "<input type=hidden name=pkgs value='" . $value->cl_pkgs . "' />"
                        . "<input type=hidden name=from_location value='" . ($value->stk_inv_location ? $value->stk_inv_location : 'WH') . "' />";

                $stock_loc_dropdow .= "<select name='si_location' style='width:75px' onchange='this.form.submit()'>";
                foreach (STOCK_LOCATION as $stk_loc) {
                    $selected = $value->stk_inv_location == $stk_loc ? 'selected' : '';
                    $stock_loc_dropdow .= "<option value='$stk_loc' $selected $disabled>$stk_loc</option>";
                }
                $stock_loc_dropdow .= "</select></form>"
                .(!empty($stf_status) && $stf_status=='Pending'?"<span class='badge badge-warning'>Pending</span>":'');

                $disabled2 = has_this_role('logistics') && $value->stk_inv_status == 'AVAILABLE' ? '' : 'disabled';
                if (has_this_role('accounts')) {
                    $disabled2 = '';
                }

                $stock_staus_dropdow = "<form method=post>"
                        . "<input type=hidden name=quantity value='$value->quantity' />"
                        . "<input type=hidden name=po_meta_id value='$value->id' />"
                        . "<input type=hidden name=receipt_no value='{$value->receipt_no}' />";
                $stock_staus_dropdow .= "<select name='si_status' style='width:105px' onchange='if(confirm(`are you sure you want to change?`)){this.form.submit()}' class='{$value->stk_inv_status}'>";
                foreach (STOCK_STATUS as $stk_status) {
                    $selected = $value->stk_inv_status == $stk_status ? 'selected' : '';
                    $stock_staus_dropdow .= "<option value='$stk_status' $selected $disabled2>$stk_status</option>";
                }
                $stock_staus_dropdow .= "</select></form>";
                

                $listArr[$i]['location'] = $stock_loc_dropdow;
                $listArr[$i]['status'] = $stock_staus_dropdow;
                $listArr[$i]['comment'] = "<textarea name=comment style='width:150px' class=text-comment data-po_id='{$value->id}'>{$value->stk_inv_comment}</textarea>";
                $listArr[$i]['edit'] = "<a href='" . admin_url() . "admin.php?page=stock-inventory-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >"
                        . "<img alt='' src='{$asset_url}edit.png'></a>";
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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quotation_po_meta t1  LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ");

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

include_once 'popup.php';

function admin_ctm_stock_inventory_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $po_meta_id = !empty($getdata['po_meta_id']) ? $getdata['po_meta_id'] : '';
    $si_status = !empty($getdata['si_status']) ? $getdata['si_status'] : '';
    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $sup_code = !empty($getdata['sup_code']) ? $getdata['sup_code'] : '';
    $item_desc = !empty($getdata['item_desc']) ? $getdata['item_desc'] : '';
    $hs_code = !empty($getdata['hs_code']) ? $getdata['hs_code'] : '';
    $delivery_date = !empty($getdata['delivery_date']) ? $getdata['delivery_date'] : '';
    $category_id = !empty($getdata['category_id']) ? $getdata['category_id'] : '';
    $si_location = !empty($getdata['si_location']) ? $getdata['si_location'] : '';
    $quantity = !empty($getdata['quantity']) ? $getdata['quantity'] : '';

    if (!empty($postdata['si_location'])) {
        make_model_stock_transfer();
    }
    if (!empty($postdata['si_status']) && in_array($postdata['si_status'], ['AVAILABLE', 'DAMAGED', 'USED'])) {
        make_model_status_change_other();
    }
    if (!empty($postdata['si_status']) && $postdata['si_status'] == 'RESERVED') {
        make_model_pdi_change();
    }
    if (!empty($postdata['si_status']) && $postdata['si_status'] == 'DELIVERED') {
        make_model_status_change_delivered();
    }


    if (!empty($postdata['stock_transfer'])) {
        create_stock_transfer($postdata);
        $msg = 'Item has sent to stock transfer successfully.';
    }

    if (!empty($postdata['delivery_status_change'])) {
        create_order_delivery_note($postdata['po_meta_id'], $postdata['quantity'], $postdata['receipt_no']);
        $msg = 'Item has sent to delivery Note list successfully.';
    }

    if (!empty($postdata['pdi_status_change'])) {
        create_pre_delivery($postdata['po_meta_id'], $postdata['quantity']);
        $msg = 'Item has sent to pre delivery inspection successfully.';
    }

    if (!empty($postdata['other_status_change'])) {
        stock_inventroy_status_change($postdata['po_meta_id'], $postdata['quantity'], $postdata['status']);
        $msg = "Item status has been changed to {$postdata['status']} successfully.";
    }


    $suppliers = get_cache_results("SELECT name,sup_code FROM {$wpdb->prefix}ctm_suppliers ORDER BY sup_code ASC", ['day' => true]);
    $categories = get_cache_results("SELECT id,name FROM {$wpdb->prefix}ctm_item_category ORDER BY name ASC", ['day' => true]);
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
        <h1 class="wp-heading-inline">Stock Inventory</h1>
        <a  href='<?= get_template_directory_uri() ?>/export/export-stock-inventory.php' class="page-title-action" target="_blank">Export as Excel</a>
        <a  href='<?= admin_url("admin.php?page=sold-items") ?>' class="page-title-action" >Sold Item List</a>
        
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
                                                    <input type="number" name="po_meta_id"  value="<?= $po_meta_id ?>" placeholder="ID" />
                                                </td>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Search By Client</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="qid" class="search-input" value="<?= $qid ?>" placeholder="Search by QTN#"  >
                                                </td>
                                                

                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="entry" class="search-input" value="<?= $entry ?>" placeholder="Search By Entry#"  >
                                                </td>
                                                <td>
                                                    <input type="text" name="hs_code" class="search-input" value="<?= $hs_code ?>" placeholder="Search By HS Code#"  >
                                                </td>
                                                <td>
                                                    <select name="category_id" id="category-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Category</option>
                                                        <?php foreach ($categories as $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= $value->id == $category_id ? 'selected' : '' ?>>
                                                                <?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                

                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="item_id" id="item-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Collection Name</option>
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
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="si_status" id="status" onchange="this.form.submit()">
                                                        <option value='' >Stock Status</option>
                                                        <?php
                                                        foreach (STOCK_STATUS as $value) {
                                                            $selected = $si_status == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td style="width:170px">
                                                    <input type="<?= $delivery_date ? 'date' : 'text' ?>" name="delivery_date" class="search-input" value="<?= $delivery_date ?>" placeholder="Delivery Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td>
                                                    <select name='si_location'  onchange='this.form.submit()'>
                                                        <option value=''>Select Location</option>"
                                                        <?php
                                                        foreach (STOCK_LOCATION as $value) {
                                                            $selected = $si_location == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name='quantity' style="width:100px" onchange='this.form.submit()'>
                                                        <option value=''>Select Quantity</option>
                                                        <option value='<1' <?= $quantity == "<1" ? 'selected' : '' ?>>&LT;1</option>
                                                        <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                            <option value='>=<?= $i ?>' <?= $quantity == ">=$i" ? 'selected' : '' ?>>&GT;=<?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td colspan="2">
                                                    <button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>


                                    <?php
                                    //Create an instance of our package class...
                                    $testListTable = new CTM_Stock_inventory_Order_List_Table();
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
