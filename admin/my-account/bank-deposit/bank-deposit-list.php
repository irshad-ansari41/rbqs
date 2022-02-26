<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Bank_Deposit_List_Table extends WP_List_Table {

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
            case 'receipt_id':
            case 'qtn':
            case 'type':
            case 'receipt_amount':
            case 'received_from':
            case 'payment_date':
            case 'payment_status':
            case 'payment_method':
            case 'change':
            case 'charges':
            case 'hold_cash':
            case 'net_deposit':
            case 'bank_status':
            case 'bank_date':
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
            'receipt_id' => __('Receipt No'),
            'qtn' => __(' QTN&nbsp;ID&nbsp;&nbsp;&nbsp;'),
            'type' => __('Type'),
            'receipt_amount' => __('Receipt<br/> Amount'),
            'received_from' => __('Received From'),
            'payment_date' => __('Payment Date'),
            'payment_status' => __('Full /<br/> Advance /<br/> Balance '),
            'payment_method' => __('Mode'),
            'change' => __('Change'),
            'charges' => __('Charges'),
            'hold_cash' => __('Cash on<br/> Hold'),
            'net_deposit' => __('Net<br/>Deposit'),
            'bank_status' => __('Deposit<br/>Verified'),
            'bank_date' => __('Verified<br/> Date'),
            'updated_at' => __('Last <br/>Updated'),
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

        $receipt_id = !empty($receipt_id) ? " t1.receipt_id like '%{$receipt_id}%' " : 1;
        $qid = !empty($qid) ? "(t2.quotation_id like '%{$qid}%' OR t2.revised_no like '%{$qid}%') " : 1;
        $received_from = !empty($received_from) ? " t2.received_from like '%{$received_from}%' " : 1;
        $payment_date = !empty($payment_date) ? " t2.payment_date like '%{$payment_date}%' " : 1;
        $verify_date = !empty($verify_date) ? " t1.verify_date like '%{$verify_date}%' " : 1;
        $paid_amount = !empty($paid_amount) ? " t2.paid_amount like '%{$paid_amount}%' " : 1;
        $payment_method = !empty($payment_method) ? " t2.payment_method like '%{$payment_method}%' " : 1;

        $where = "WHERE {$receipt_id} AND {$qid} AND $paid_amount AND {$received_from} AND {$payment_date} AND {$payment_method} AND {$verify_date} ";
        $sql = "SELECT t1.id,t1.change_amount, t1.charges, t1.hold_cash, t1.net_deposit, t1.payment_status, t1.bank_status, t1.verify_date,t1.updated_at, "
                . "t2.id as receipt_id, t2.quotation_id,t2.revised_no,t2.paid_amount,t2.received_from,t2.payment_date,t2.payment_method "
                . "FROM {$wpdb->prefix}ctm_bank_deposits t1 LEFT JOIN {$wpdb->prefix}ctm_receipts t2 ON t1.receipt_id=t2.id $where ORDER BY t1.id DESC LIMIT $start, 10 ";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $balance = get_bank_deposit_balance($value->receipt_id);
                $listArr[$i]['id'] = "<a href='" . admin_url() . "admin.php?page=bank-deposit-edit&id={$value->id}&action=edit' title=Edit class=btn-edit >$value->id</a>";
                $listArr[$i]['receipt_id'] = $value->receipt_id;
                $listArr[$i]['qtn'] = $value->revised_no ? $value->revised_no : $value->quotation_id;
                $listArr[$i]['type'] = get_qtn_type($value->quotation_id);
                $listArr[$i]['receipt_amount'] = number_format($value->paid_amount, 2);
                $listArr[$i]['received_from'] = $value->received_from;
                $listArr[$i]['payment_date'] = rb_date($value->payment_date);
                $listArr[$i]['payment_status'] = $value->payment_status;
                $listArr[$i]['payment_method'] = $value->payment_method;
                $listArr[$i]['change'] = number_format($value->change_amount, 2);
                $listArr[$i]['charges'] = number_format($value->charges, 2);
                $listArr[$i]['hold_cash'] = number_format($value->hold_cash, 2);
                $listArr[$i]['net_deposit'] = number_format($value->net_deposit, 2);
                $listArr[$i]['bank_status'] = $value->bank_status;
                $listArr[$i]['bank_date'] = $value->verify_date;
                $listArr[$i]['updated_at'] = rb_date($value->updated_at) . '<br/>' . rb_time($value->updated_at);

                $edit = "<a href='" . admin_url() . "admin.php?page=bank-deposit-edit&id={$value->id}&action=edit' "
                        . "title=Edit class=btn-edit ><img alt='' src='{$asset_url}edit.png'></a>&nbsp;&nbsp;";
                $settle = !empty($balance) && !empty($value->hold_cash) && $value->hold_cash != '0.0' ? "<a href='" . admin_url() . "admin.php?page=bank-deposit-settle&id={$value->id}&action=settle' "
                        . "title=Settle class=btn-edit >Settle</a>" : '';

                $listArr[$i]['action'] = $edit . $settle;
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

        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_bank_deposits t1 LEFT JOIN {$wpdb->prefix}ctm_receipts t2 ON t1.receipt_id=t2.id $where"); //count($rows);
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

function admin_ctm_bank_deposit_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $receipt_id = !empty($getdata['receipt_id']) ? $getdata['receipt_id'] : '';
    $paid_amount = !empty($getdata['paid_amount']) ? $getdata['paid_amount'] : '';
    $payment_method = !empty($getdata['payment_method']) ? $getdata['payment_method'] : '';
    $payment_date = !empty($getdata['payment_date']) ? $getdata['payment_date'] : '';
    $verify_date = !empty($getdata['verify_date']) ? $getdata['verify_date'] : '';

    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_receipts WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_Bank_Deposit_List_Table();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], 
            #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], 
            #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], 
            #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
            table.wp-list-table {table-layout: initial!important;}
            .wp-list-table tr th{white-space: nowrap; text-align: center;}
            table tr td.collection_name{width:400px}
            table tr td.description{width:200px}
            #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;}
            th#received_from{width: 150px}
        </style>
        <div class="wrap">
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
            <h1 class="wp-heading-inline">Verified Customer Receipt</h1>
            <a  id="add-new-client" href="<?= 'admin.php?page=bank-deposit-registry' ?>" class="page-title-action btn-primary" target="_blank">View Receipt Registry </a>
            <a  id="add-new-client" href="<?= 'admin.php?page=daily-cash-check' ?>" class="page-title-action btn-primary" target="_blank">Bank Deposit Slip</a>
            <br/>
            <?php if (!empty($getdata['msg'])) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Bank Deposit has been settle successfully.
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
                                                        <input type=text name="receipt_id" value="<?= $receipt_id ?>" class="search-input" placeholder="Receipt No"  >
                                                    </td>
                                                    <td>
                                                        <input type=text name="qid" value="<?= $qid ?>" class="search-input" placeholder="QTN No"  >
                                                    </td>
                                                    <td>
                                                        <input type=text name="paid_amount" value="<?= $paid_amount ?>"  class="search-input" placeholder="Receipt Amount"  >
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="received_from" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                            <option value="">Loading...</option>
                                                        </select>
                                                    </td>
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
                                                        <input type="<?= $payment_date ? 'date' : 'text' ?>" name="payment_date" value="<?= $payment_date ?>"  class="search-input"  placeholder="Search By Payment Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="<?= $verify_date ? 'date' : 'text' ?>" name="verify_date" value="<?= $verify_date ?>"  class="search-input"  placeholder="Search By Verify Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td colspan="2"><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                        &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
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
                        var received_from = '<?= !empty($getdata['received_from']) ? $getdata['received_from'] : 0; ?>';
                        jQuery('#client-name').html('');
                        var html = '<option value="">Select Client</option>';
                        jQuery.each(data, function (i, client) {
                            var selected = received_from === client.name ? 'selected' : '';
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
