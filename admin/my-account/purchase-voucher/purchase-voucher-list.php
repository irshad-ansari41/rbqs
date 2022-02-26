<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_purchase_voucher_List_Table extends WP_List_Table {

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
            case 'supplier':
            case 'invoice_no':
            case 'invoice_date':
            case 'expense_type':
            case 'amount':
            case 'vat':
            case 'total_amount':
            case 'narration':
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
            'supplier' => __('Supplier'),
            'invoice_no' => __('Invoice No'),
            'invoice_date' => __('Invoice Date'),
            'expense_type' => __('Expense Type'),
            'amount' => __('Amount'),
            'vat' => __('VAT'),
            'total_amount' => __('Total Amount'),
            'narration' => __('Narration'),
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

        $purchase_voucher_id = !empty($purchase_voucher_id) ? " id like '%{$purchase_voucher_id}%' " : 1;
        $sup_id = !empty($sup_id) ? " sup_id like '%{$sup_id}%' " : 1;
        $invoice_no = !empty($invoice_no) ? " invoice_no like '%{$invoice_no}%' " : 1;
        $invoice_date = !empty($invoice_date) ? " invoice_date like '%{$invoice_date}%' " : 1;
        $expense_type = !empty($expense_type) ? " expense_type like '%{$expense_type}%' " : 1;

        $where = "WHERE {$purchase_voucher_id} AND $sup_id AND $invoice_no AND $invoice_date AND $expense_type";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers $where ORDER BY id DESC LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['supplier'] = get_supplier_by_id($value->sup_id, 'name');
                $listArr[$i]['invoice_no'] = $value->invoice_no;
                $listArr[$i]['invoice_date'] = rb_date($value->invoice_date);
                $listArr[$i]['expense_type'] = $value->expense_type;
                $listArr[$i]['amount'] = number_format($value->amount, 2);
                $listArr[$i]['vat'] = number_format($value->vat, 2);
                $listArr[$i]['total_amount'] = number_format($value->total_amount, 2);
                $listArr[$i]['narration'] = $value->narration;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a> | "
                        . (has_this_role('accounts') ? "<a href='" . admin_url() . "admin.php?page={$page}-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >"
                        . "<img alt='' src='{$asset_url}edit.png'></a> | " : '')
                        . (has_this_role('accounts') ? "<a href='admin.php?page={$page}&id={$value->id}&action=delete' "
                        . "onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete >"
                        . "<img alt='' src='{$asset_url}delete.png'></a>" : '');
                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'collection_name';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        $count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}ctm_purchase_vouchers $where"); //count($rows);
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

function admin_ctm_purchase_voucher_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $purchase_voucher_id = !empty($getdata['purchase_voucher_id']) ? $getdata['purchase_voucher_id'] : '';
    $sup_id = !empty($getdata['sup_id']) ? $getdata['sup_id'] : '';
    $invoice_no = !empty($getdata['invoice_no']) ? $getdata['invoice_no'] : '';
    $invoice_date = !empty($getdata['invoice_date']) ? $getdata['invoice_date'] : '';
    $expense_type = !empty($getdata['expense_type']) ? $getdata['expense_type'] : '';

    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_purchase_voucher_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        $rb_purchase_options = get_option('rb_payment_options', []);
        $suppliers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_suppliers WHERE sup_type='Local'");
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], 
            #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], 
            #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], 
            #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
            table.wp-list-table {table-layout: initial!important;}
            .wp-list-table tr th{white-space: nowrap;}
            table tr td.collection_name{width:400px}
            table tr td.description{width:200px}
            #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;}
            th#paid_to{width: 150px}
        </style>
        <div class="wrap">
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
            <h1 class="wp-heading-inline">Local Payment Master</h1>
            <a id="add-new-client" href="<?= "admin.php?page={$page}-create" ?>" class="page-title-action btn-primary" >Add New For Payment</a>
            <a  id="add-new-client" href="<?= 'admin.php?page=purchase-registry' ?>" class="page-title-action btn-primary" >Pay Local Invoice </a>
            <a  id="add-new-client" href="<?= 'admin.php?page=payment-options' ?>" class="page-title-action btn-primary" >Type of Expense</a>
            <br/><br/>
            <?php if (!empty($getdata['msg'])) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Purchase Voucher has been created successfully.
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
                                        <form method="get">
                                            <input type=hidden name="page" value="<?= $getdata['page'] ?>" />
                                            <table class="form-table">
                                                <tr>
                                                    <td>
                                                        <input type=text name="purchase_voucher_id" value="<?= $purchase_voucher_id ?>" class="search-input" placeholder="Purchase Voucher No" >
                                                    </td>

                                                    <td>
                                                        <select name="sup_id" class="chosen-select" onchange="this.form.submit()">
                                                            <option value="">Select Payment Expense Type</option>
                                                            <?php
                                                            foreach ($suppliers as $value) {
                                                                $selected = $sup_id == $value->id ? 'selected' : '';
                                                                echo "<option value='$value->id' $selected>$value->name</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <input type=text name="invoice_no" value="<?= $invoice_no ?>"  class="search-input" placeholder="Invoice No"  >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="<?= $invoice_date ? 'date' : 'text' ?>" name="invoice_date" class="search-input" value="<?= $invoice_date ?>" placeholder="Invoice Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td>
                                                        <select name="expense_type" id="expense_type" >
                                                            <option value="">Payment Expense Type</option>
                                                            <?php
                                                            foreach ($rb_purchase_options['rb_expense_type'] as $value) {
                                                                 $selected = $expense_type == $value ? 'selected' : '';
                                                                echo "<option value='{$value}' $selected >{$value}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>

                                                    <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                        &nbsp; &nbsp;
                                                        <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                                </tr>
                                            </table>
                                        </form>
                                        <!-- Now we can render the completed list table -->
                                        <?php $testListTable->display(); ?>
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
                        var paid_to = '<?= !empty($getdata['paid_to']) ? $getdata['paid_to'] : 0; ?>';
                        jQuery('#client-name').html('');
                        var html = '<option value="">Select Client</option>';
                        jQuery.each(data, function (i, client) {
                            var selected = paid_to === client.name ? 'selected' : '';
                            html += `<option value="${client.name}" ${selected}>${client.name}</option>`;
                        });
                        jQuery('#client-name').html(html);
                        jQuery('.chosen-select').chosen();
                    }
                });

            });
        </script>
        <?php
    }
}
