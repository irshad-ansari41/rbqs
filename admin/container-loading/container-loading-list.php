<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Container_Loading_Order_List_Table extends WP_List_Table {

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
            case 'category':
            case 'sup_name':
            case 'entry':
            case 'description':
            case 'qty':
            case 'client':
            case 'qtn':
            case 'invoice_no':
            case 'invoice_amount':
            case 'pkgs':
            case 'cbm':
            case 'kg':
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
            'collection_name' => __('Collection<br/>Name '),
            'category' => __('Category'),
            'sup_name' => __('Supplier<br/>Name'),
            'entry' => __('Entry #'),
            'description' => __('Description'),
            'qty' => __('QTY'),
            'client' => __('Client'),
            'qtn' => __('QTN No'),
            'invoice_no' => __('Invoice No'),
            'invoice_amount' => __('Value'),
            'pkgs' => __('Pkgs'),
            'cbm' => __('CBM'),
            'kg' => __('KG'),
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

        $qid = !empty($qid) ? " (t1.quotation_id like  '%{$qid}%' OR t1.revised_no like  '%{$qid}%')" : 1;
        $entry = !empty($entry) ? " t1.entry like '%{$entry}%' " : 1;
        $po_id = !empty($po_id) ? " t1.po_id ='{$po_id}' " : 1;
        $confirmation_no = !empty($confirmation_no) ? " t1.confirmation_no ='{$confirmation_no}' " : 1;
        $po_date = !empty($po_date) ? " t1.po_date ='{$po_date}' " : 1;
        $client_id = !empty($client_id) ? " t1.client_id ='{$client_id}'" : 1;
        $item_id = !empty($item_id) ? " t1.item_id ='{$item_id}'" : 1;
        $item_desc = !empty($item_desc) ? " t1.item_desc like '%{$item_desc}%'" : 1;
        $category_id = !empty($category_id) ? " t2.category ='{$category_id}'" : 1;
        $sup_code = !empty($sup_code) ? " t1.sup_code ='{$sup_code}'" : 1;

        $where = "WHERE t1.cl_status=0 AND t1.order_registry ='DELIVERED TO FF' AND $qid AND $client_id AND $item_id AND $item_desc AND $category_id AND $sup_code AND $entry AND $confirmation_no AND $po_id AND $po_date";
        $sql = "SELECT t1.id,t1.quotation_id,t1.client_id,t1.item_id,t1.entry,t1.sup_code,t1.item_desc,t1.quantity,t1.po_id,t1.revised_no,t1.order_registry,t1.invoice_no,t1.invoice_amount,t1.cl_pkgs,t1.cl_cbm,t1.cl_kg,t1.updated_at, t1.add_in_list, t1.cl_priority,t1.confirmation_no,t1.po_date FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where ORDER BY t1.id DESC LIMIT $start, 10";

        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $client_name = get_client($value->client_id, 'name');
                $item = get_item($value->item_id);
                $category = get_item_category($item->category, 'name');
                $supplier_name = get_supplier($value->sup_code, 'name');
                $checked = $value->add_in_list ? 'checked="checked"' : '';
                $qtn = get_revised_no($value->quotation_id);

                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['collection_name'] = $item->collection_name;
                $listArr[$i]['category'] = $category;
                $listArr[$i]['sup_name'] = $supplier_name;
                $listArr[$i]['entry'] = make_entry_bold($value->entry);
                $listArr[$i]['description'] = nl2br($value->item_desc);
                $listArr[$i]['qty'] = $value->quantity;
                $listArr[$i]['client'] = $client_name;
                $listArr[$i]['qtn'] = $qtn;
                $listArr[$i]['invoice_no'] = $value->invoice_no;
                $listArr[$i]['invoice_amount'] = $value->invoice_amount;
                $listArr[$i]['pkgs'] = $value->cl_pkgs;
                $listArr[$i]['cbm'] = $value->cl_cbm;
                $listArr[$i]['kg'] = $value->cl_kg;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<label class='button-secondary'>"
                        . "<input type=checkbox $checked class=add_to_list value='{$value->id}' /> Add</label><br/>"
                        . "<select class='cl_priority' data-po_meta_id='{$value->id}'>"
                        . "<option value=''>Priority</option>"
                        . "<option value='High' " . ($value->cl_priority == 'High' ? 'selected' : '') . ">High</option>"
                        . "<option value='Less' " . ($value->cl_priority == 'Less' ? 'selected' : '') . ">Less</option>"
                        . "</select>";
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

function admin_ctm_container_loading_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $po_id = !empty($getdata['po_id']) ? $getdata['po_id'] : '';
    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $confirmation_no = !empty($getdata['confirmation_no']) ? $getdata['confirmation_no'] : '';
    $po_date = !empty($getdata['po_date']) ? $getdata['po_date'] : '';
    $sup_code = !empty($getdata['sup_code']) ? $getdata['sup_code'] : '';
    $item_desc = !empty($getdata['item_desc']) ? $getdata['item_desc'] : '';
    $category_id = !empty($getdata['category_id']) ? $getdata['category_id'] : '';
    $order_registry = !empty($getdata['order_registry']) ? $getdata['order_registry'] : '';
    $suppliers = get_cache_results("SELECT name,sup_code FROM {$wpdb->prefix}ctm_suppliers ORDER BY sup_code ASC", ['day' => true]);
    $categories = get_cache_results("SELECT id,name FROM {$wpdb->prefix}ctm_item_category ORDER BY name ASC", ['day' => true]);
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
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
        <h1 class="wp-heading-inline">DELIVERED FF - LOADING</h1>
        <a href="admin.php?page=container-loading-preview" class="page-title-action" target="_blank">Preview Container Loading List</a>
        <br/><br/>


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
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Search By Client</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="qid" class="search-input" value="<?= $qid ?>" placeholder="Search by QTN#"  >
                                                </td>
                                                <td>
                                                    <input type="text" name="entry" class="search-input" value="<?= $entry ?>" placeholder="Search By Entry#"  >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="confirmation_no" class="search-input" value="<?= $confirmation_no ?>" placeholder="Confirmation No #"  >
                                                </td>
                                                <td>
                                                    <input type="text" name="po_id" class="search-input" value="<?= $po_id ?>" placeholder="Search By PO#"  >
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
                                                        <option value="">Search By Keyword</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="item_desc" class="search-input" value="<?= $item_desc ?>" placeholder="Search By Description Keyword"  >
                                                </td>
                                                <td>
                                                    <select name="sup_code" id="supplier-name" class="chosen-select" onchange="this.form.submit()" >
                                                        <option value="">Search By Supplier</option>
                                                        <?php foreach ($suppliers as $value) { ?>
                                                            <option value="<?= $value->sup_code ?>" <?= $value->sup_code == $sup_code ? 'selected' : '' ?>>
                                                                <?= $value->sup_code ?> | <?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="order_registry" id="status" onchange="this.form.submit()">
                                                        <option value='' >Search By Status</option>
                                                        <option value='ORDERED' <?= $order_registry == 'ORDERED' ? 'selected' : '' ?> >ORDERED</option>
                                                        <option value='CONFIRMED' <?= $order_registry == 'CONFIRMED' ? 'selected' : '' ?> >CONFIRMED</option>
                                                        <option value='DELIVERED TO FF' <?= $order_registry == 'DELIVERED TO FF' ? 'selected' : '' ?> >DELIVERED TO FF</option>
                                                        <option value='TRANSIT' <?= $order_registry == 'TRANSIT' ? 'selected' : '' ?> >TRANSIT</option>
                                                        <option value='ARRIVED' <?= $order_registry == 'ARRIVED' ? 'selected' : '' ?> >ARRIVED</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="<?= $po_date ? 'date' : 'text' ?>" name="po_date" class="search-input" value="<?= $po_date ?>" placeholder="Search By PO Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                </td>
                                                <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form action="admin.php?page=container-loading-preview" method="post">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $page ?>" />
                                        <!-- Now we can render the completed list table -->
                                        <?php
                                        //Create an instance of our package class...
                                        $testListTable = new CTM_Container_Loading_Order_List_Table();
                                        //Fetch, prepare, sort, and filter our data...
                                        $testListTable->prepare_items();
                                        $testListTable->display();
                                        ?>
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
        jQuery(document).ready(() => {
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

            jQuery('.add_to_list').click(function () {
                var po_meta_id = jQuery(this).val();
                var status = 0;
                if (jQuery(this).is(':checked')) {
                    status = 1;
                } else {
                    status = 2;
                }
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/container-loading-list.php",
                    type: "post",
                    dataType: "json",
                    data: {po_meta_id: po_meta_id, status: status},
                    success: function (response) {
                        if (response.status) {
                            alert(`Item ${response.status} to container loading list successfully`);
                        }
                    }
                });

            });
            jQuery('.cl_priority').change(function () {
                var priority = jQuery(this).val();
                var po_meta_id = jQuery(this).data('po_meta_id');
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/container-loading-list.php",
                    type: "post",
                    dataType: "json",
                    data: {po_meta_id: po_meta_id, priority: priority},
                    success: function (response) {
                        if (response.status) {
                            alert(`Item priority has set to ${priority}.`);
                        }
                    }
                });

            });
        });
    </script>
    <?php
}
