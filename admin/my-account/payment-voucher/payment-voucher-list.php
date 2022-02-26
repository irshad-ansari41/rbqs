<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_payment_voucher_List_Table extends WP_List_Table {

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
            case 'pv_type':
            case 'currency':
            case 'amount':
            case 'paid_to':
            case 'payment_source':
            case 'payment_method':
            case 'payment_date':
            case 'manager':
            case 'receiver':
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
            'pv_type' => __('Type'),
            'currency' => __('Currency'),
            'amount' => __('Amount'),
            'paid_to' => __('Paid To'),
            'payment_source' => __('Payment Source'),
            'payment_method' => __('Payment Method'),
            'payment_date' => __('Payment Date'),
            'manager' => __('Manager'),
            'receiver' => __('Receiver'),
            'updated_at' => __('Updated At'),
            'action' => __('Action'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('payment_voucher_no', true),
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

        $payment_voucher_id = !empty($payment_voucher_id) ? " id like '%{$payment_voucher_id}%' " : 1;
        $paid_to = !empty($paid_to) ? " paid_to like '%{$paid_to}%' " : 1;
        $payment_date = !empty($payment_date) ? " payment_date like '%{$payment_date}%' " : 1;
        $amount = !empty($amount) ? " amount like '%{$amount}%' " : 1;
        $payment_method = !empty($payment_method) ? " payment_method like '%{$payment_method}%' " : 1;

        $where = "WHERE {$payment_voucher_id} AND {$payment_method} AND $amount AND {$paid_to} AND {$payment_date}";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_payment_vouchers $where ORDER BY id DESC LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['pv_type'] = $value->pv_type;
                $listArr[$i]['currency'] = $value->currency;
                $listArr[$i]['amount'] = number_format($value->amount, 2);
                $listArr[$i]['paid_to'] = $value->paid_to;
                $listArr[$i]['payment_source'] = $value->payment_source;
                $listArr[$i]['payment_method'] = $value->payment_method;
                $listArr[$i]['payment_date'] = rb_date($value->payment_date);
                $listArr[$i]['manager'] = $value->manager;
                $listArr[$i]['receiver'] = $value->receiver;
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

        $count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}ctm_payment_vouchers $where"); //count($rows);
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

function admin_ctm_payment_voucher_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $payment_voucher_id = !empty($getdata['payment_voucher_id']) ? $getdata['payment_voucher_id'] : '';
    $paid_to = !empty($getdata['paid_to']) ? $getdata['paid_to'] : '';
    $payment_date = !empty($getdata['payment_date']) ? $getdata['payment_date'] : '';
    $amount = !empty($getdata['amount']) ? $getdata['amount'] : '';
    $payment_method = !empty($getdata['payment_method']) ? $getdata['payment_method'] : '';

    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_payment_vouchers WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_payment_voucher_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();
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
            <h1 class="wp-heading-inline">Payment Voucher List</h1>
            <a id="add-new-client" href="<?= "admin.php?page={$page}-create" ?>" class="page-title-action btn-primary" >Issue Payment Voucher</a>
            <a  id="add-new-client" href="<?= 'admin.php?page=payment-registry' ?>" class="page-title-action btn-primary" >View Registry </a>
            <br/><br/>
            <?php if (!empty($getdata['msg'])) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Payment Voucher has been created successfully
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
                                                        <input type=text name="payment_voucher_id" value="<?= $payment_voucher_id ?>" class="search-input" placeholder="Payment Voucher No"  >
                                                    </td>

                                                    <td>
                                                        <input type=text name="amount" value="<?= $amount ?>" class="search-input" placeholder="Total Amount"  >
                                                    </td>

                                                    <td>
                                                        <input type=text name="paid_to" value="<?= $paid_to ?>"  class="search-input" placeholder="Paid To"  >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name='payment_method' onchange="this.form.submit()">
                                                            <option value="">Payment Method</option>
                                                            <option value="Cash" <?= $payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                                            <option value="Check" <?= $payment_method == 'Check' ? 'selected' : '' ?>>Check</option>
                                                            <option value="Card" <?= $payment_method == 'Card' ? 'selected' : '' ?>>Card</option>
                                                            <option value="Bank Transfer" <?= $payment_method == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                                            <option value="Bank Deposit" <?= $payment_method == 'Bank Deposit' ? 'selected' : '' ?>>Bank Deposit</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="<?= $payment_date ? 'date' : 'text' ?>" name="payment_date" class="search-input" value="<?= $payment_date ?>" placeholder="Payment Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                        &nbsp;&nbsp;
                                                        <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                    </td>
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
