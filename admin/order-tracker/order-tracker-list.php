<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Order_Tracker_List_Table extends WP_List_Table {

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
            case 'qty':
            case 'sp':
            case 'po_no':
            case 'po_date':
            case 'confirmation_no':
            case 'client_name':
            case 'qtn':
            case 'qcr':
            case 'item_status':
            case 'stock_status':
            case 'dispatch_date':
            case 'delivery_date':
            case 'departure_date':
            case 'arrival_date':
            case 'invoice_no':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns() {
        $columns = array(
            'id' => __('ID'),
            'collection_name' => __('Collection<br/> Name'),
            'image' => __('Image'),
            'category' => __('Category'),
            'sup_code' => __('Supplier<br/> Code'),
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'qty' => __('QTY'),
            'sp' => __('Sales<br/> Person'),
            'po_no' => __('PO No.'),
            'po_date' => __('PO Date'),
            'confirmation_no' => __('Confirmation<br/> No'),
            'client_name' => __('Customer<br/> Name'),
            'qtn' => __('QTN No.'),
            'qcr' => __('QCR #'),
            'item_status' => __('Item Status'),
            'stock_status' => __('Stock Status'),
            'dispatch_date' => __('Dispatch Date<br/>From Factory'),
            'delivery_date' => __('Delivery<br/>Date To FF'),
            'departure_date' => __('Departure<br/>From Italy'),
            'arrival_date' => __('Arrival Date<br/> In Dubai'),
            'invoice_no' => __('Invoice<br/>No'),
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

        $po_meta_id = !empty($po_meta_id) ? " t1.id like '%" . $po_meta_id . "%' " : 1;
        $qid = !empty($qid) ? " (t1.quotation_id like  '%{$qid}%' OR t1.revised_no like  '%{$qid}%')" : 1;
        $entry = !empty($entry) ? " t1.entry like '%{$entry}%'" : 1;
        $po_id = !empty($po_id) ? " t1.po_id ='{$po_id}' " : 1;
        $confirmation_no = !empty($confirmation_no) ? " t1.confirmation_no ='{$confirmation_no}' " : 1;
        $po_date = !empty($po_date) ? " t1.po_date ='{$po_date}' " : 1;
        $client_id = !empty($client_id) ? " t1.client_id ='{$client_id}'" : 1;
        $item_id = !empty($item_id) ? " t1.item_id ='{$item_id}'" : 1;
        $item_desc = !empty($item_desc) ? " t1.item_desc like '%{$item_desc}%'" : 1;
        $category_id = !empty($category_id) ? " t2.category ='{$category_id}'" : 1;
        $sup_code = !empty($sup_code) ? " t1.sup_code ='{$sup_code}'" : 1;
        $order_registry = !empty($order_registry) ? " t1.order_registry ='{$order_registry}'" : 1;
        $qcr_id = !empty($qcr_id) ? " t1.qcr_id ='{$qcr_id}'" : 1;
        $dispatch_date = !empty($dispatch_date) ? " t1.dispatch_date ='{$dispatch_date}'" : 1;
        $delivery_date = !empty($delivery_date) ? " t1.delivery_date ='{$delivery_date}'" : 1;
        $arrival_date = !empty($arrival_date) ? " t1.arrival_date ='{$arrival_date}'" : 1;
        $invoice_no = !empty($invoice_no) ? " t1.invoice_no ='{$invoice_no}'" : 1;
        $stock_status = !empty($stock_status) ? " t1.stk_inv_status ='{$stock_status}'" : 1;

        $where = "WHERE t1.quantity>0 AND $po_meta_id AND $qid AND $client_id AND $item_id AND $item_desc AND $category_id AND $sup_code AND $entry AND $confirmation_no AND $po_id AND $po_date AND $order_registry AND $stock_status AND $qcr_id AND $dispatch_date AND $delivery_date AND $arrival_date AND $invoice_no";
        $sql = "SELECT t1.id,t1.quotation_id,t1.client_id,t1.item_id,t1.entry,t1.item_desc,t1.quantity,t1.po_id,t1.revised_no,t1.qcr_id,t1.order_registry,t1.stk_inv_status,t1.dispatch_date,t1.delivery_date, t1.departure_date,t1.arrival_date,t1.invoice_no,t1.confirmation_no,t1.po_date FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ORDER BY t1.po_id DESC LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $sales_person = get_qtn_sales_person($value->quotation_id);
                $client_name = get_client($value->client_id, 'name');
                $item = get_item($value->item_id);
                $category = get_item_category($item->category, 'name');
                $listArr[$i]['id'] = "<a href='admin.php?page=order-tracker-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >$value->id</a>";
                $listArr[$i]['collection_name'] = $item->collection_name;
                $listArr[$i]['image'] = "<a href='" . get_image_src($item->image) . "' target='_image'><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></a>";
                $listArr[$i]['category'] = $category;
                $listArr[$i]['sup_code'] = $item->sup_code;
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['qty'] = $value->quantity;
                $listArr[$i]['sp'] = $sales_person;
                $listArr[$i]['po_no'] = $value->po_id;
                $listArr[$i]['po_date'] = rb_date($value->po_date);
                $listArr[$i]['confirmation_no'] = $value->confirmation_no;
                $listArr[$i]['client_name'] = $client_name . $value->client_id;
                $listArr[$i]['qtn'] = !empty($value->revised_no) ? $value->revised_no : $value->quotation_id;
                $listArr[$i]['qcr'] = $value->qcr_id;
                $listArr[$i]['item_status'] = show_order_tracker_status($value->order_registry);
                $listArr[$i]['stock_status'] = show_stock_status($value->stk_inv_status);
                $listArr[$i]['dispatch_date'] = rb_date($value->dispatch_date);
                $listArr[$i]['delivery_date'] = rb_date($value->delivery_date);
                $listArr[$i]['departure_date'] = rb_date($value->departure_date);
                $listArr[$i]['arrival_date'] = rb_date($value->arrival_date);
                $listArr[$i]['invoice_no'] = $value->invoice_no;
                $view = "<a href='" . admin_url() . "admin.php?page=order-tracker-view&id={$value->id}&action=view' title='View' class=btn-view ><img alt='' src='{$asset_url}view.png'></a>";
                $edit = "<a href='" . admin_url() . "admin.php?page=order-tracker-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >"
                        . "<img alt='' src='{$asset_url}edit.png'></a>&nbsp;&nbsp;";
                $reorder = "<form method=post>"
                        . "<input type='hidden' name='page' value='{$page}' />"
                        . "<input type='hidden'  name='reorder_id' value='{$value->id}' />"
                        . "<input type='hidden'  name='quantity' value='{$value->quantity}' />"
                        . "<button type='submit' name='reorder_form' value=1 style='border: 1px solid #333;'><span class='dashicons dashicons-update-alt'></span></button>"
                        . "</form>";

                $listArr[$i]['action'] = $view . $edit . ($value->order_registry == 'DELIVERED TO FF' ? $reorder : '');
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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_quotation_po_meta t1  LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where");
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

function admin_ctm_order_tracker_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $po_meta_id = !empty($getdata['po_meta_id']) ? $getdata['po_meta_id'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $po_id = !empty($getdata['po_id']) ? $getdata['po_id'] : '';
    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $confirmation_no = !empty($getdata['confirmation_no']) ? $getdata['confirmation_no'] : '';
    $po_date = !empty($getdata['po_date']) ? $getdata['po_date'] : '';
    $sup_code = !empty($getdata['sup_code']) ? $getdata['sup_code'] : '';
    $item_desc = !empty($getdata['item_desc']) ? $getdata['item_desc'] : '';
    $category_id = !empty($getdata['category_id']) ? $getdata['category_id'] : '';
    $order_registry = !empty($getdata['order_registry']) ? $getdata['order_registry'] : '';
    $qcr_id = !empty($getdata['qcr_id']) ? $getdata['qcr_id'] : '';
    $dispatch_date = !empty($getdata['dispatch_date']) ? $getdata['dispatch_date'] : '';
    $delivery_date = !empty($getdata['delivery_date']) ? $getdata['delivery_date'] : '';
    $arrival_date = !empty($getdata['arrival_date']) ? $getdata['arrival_date'] : '';
    $invoice_no = !empty($getdata['invoice_no']) ? $getdata['invoice_no'] : '';
    $stock_status = !empty($getdata['stock_status']) ? $getdata['stock_status'] : '';

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
        table.wp-list-table {table-layout: initial!important;}
        .chosen-container,#client_name_chosen{min-width:250px!important}
        .wp-list-table tr th{white-space: nowrap;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table.wp-list-table th {text-align: center;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Order Tracker</h1>
        <a href="admin.php?page=order-tracker-add"  class="page-title-action" >Add New</a>
        <a  href='<?= get_template_directory_uri() ?>/export/export-order-tracker.php' class="page-title-action" target="_blank">Export as Excel</a>
        <a  href='<?= admin_url("admin.php?page=client-order-status") ?>' class="page-title-action" >Client Order Status</a>
        <a  href='<?= admin_url("admin.php?page=client-order-delivery-status") ?>' class="page-title-action" >Customer Order & Delivery Status Order Status</a>
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
                                                    <input type="text" name="confirmation_no" class="search-input" value="<?= $confirmation_no ?>" placeholder="Confirmation No #"  >
                                                </td>
                                                <td>
                                                    <input type="text" name="po_id" class="search-input" value="<?= $po_id ?>" placeholder="Search By PO#"  >
                                                </td>

                                            </tr>
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
                                                    <select name="item_id" id="item-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Keyword</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="item_desc" class="search-input" value="<?= $item_desc ?>" placeholder="Search By Description Keyword"  >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="sup_code" id="supplier-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Supplier</option>
                                                        <?php foreach ($suppliers as $value) { ?>
                                                            <option value="<?= $value->sup_code ?>" <?= $value->sup_code == $sup_code ? 'selected' : '' ?>>
                                                                <?= $value->sup_code ?> | <?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="order_registry" id="status" onchange="this.form.submit()">
                                                        <option value='' >Search By Item Status</option>
                                                        <?php
                                                        foreach (PO_STATUS as $value) {
                                                            echo "<option value='$value' " . ($order_registry == $value ? 'selected' : '') . " >$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="<?= $po_date ? 'date' : 'text' ?>" name="po_date" class="search-input" value="<?= $po_date ?>" placeholder="Search By PO Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" name="qcr_id" class="search-input" value="<?= $qcr_id ?>" placeholder="QCR#"  >
                                                </td>

                                                <td>
                                                    <input type="<?= $dispatch_date ? 'date' : 'text' ?>" name="dispatch_date" class="search-input" value="<?= $dispatch_date ?>" placeholder="Dispatch Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td>
                                                    <input type="<?= $delivery_date ? 'date' : 'text' ?>" name="delivery_date" class="search-input" value="<?= $delivery_date ?>" placeholder="Delivery Date to FF" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="<?= $arrival_date ? 'date' : 'text' ?>" name="arrival_date" class="search-input" value="<?= $arrival_date ?>" placeholder="Arrival Date in Dubai" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td>
                                                    <input type="text" name="invoice_no" class="search-input" value="<?= $invoice_no ?>" placeholder="Invoice No"  >
                                                </td>

                                                <td>
                                                    <select name="stock_status" id="status" onchange="this.form.submit()">
                                                        <option value='' >Stock Status</option>
                                                        <?php
                                                        foreach (STOCK_STATUS as $value) {
                                                            $selected = $stock_status == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->

                                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                    <input type="hidden" name="page" value="<?= $page ?>" />
                                    <!-- Now we can render the completed list table -->
                                    <?php
                                    //Create an instance of our package class...
                                    $testListTable = new CTM_Order_Tracker_List_Table();
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
                    var html = '<option value="">Search By Keyword</option>';
                    jQuery.each(data, function (i, item) {
                        var selected = item_id === item.id ? 'selected' : '';
                        html += `<option value="${item.id}" ${selected}>${item.collection_name}</option>`;
                    });
                    jQuery('#item-name').html(html);
                    jQuery('#item-name').trigger("chosen:updated");
                }
            });



        });
    </script>
    <?php
}
