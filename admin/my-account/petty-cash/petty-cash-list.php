<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Petty_cash_List_Table extends WP_List_Table {

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
            case 'transaction_date':
            case 'bank_date':
            case 'particulars':
            case 'voucher_type':
            case 'voucher_no':
            case 'opening':
            case 'credit':
            case 'debit':
            case 'closing':
            case 'note':
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
            'transaction_date' => __('Transaction Date'),
            'bank_date' => __(' Bank Date'),
            'particulars' => __('Particulars'),
            'voucher_type' => __('Voucher Type'),
            'voucher_no' => __('Voucher No'),
            'opening' => __('Opening'),
            'credit' => __('Credit'),
            'debit' => __('Debit'),
            'closing' => __('Closing'),
            'note' => __('Note'),
            'updated_at' => __('Last&nbsp;Updated'),
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

        $id = !empty($id) ? " id like '%{$id}%' " : 1;
        $account_name = !empty($account_name) ? " account_name='{$account_name}' " : 1;
        $particulars = !empty($particulars) ? " particulars like '%{$particulars}%' " : 1;
        $voucher_type = !empty($voucher_type) ? " voucher_type='{$voucher_type}' " : 1;
        $voucher_no = !empty($voucher_no) ? " voucher_no like '%{$voucher_no}%' " : 1;
        $bank_date = !empty($bank_date) ? " bank_date='{$bank_date}' " : 1;
        $transaction_date = !empty($transaction_date) ? " transaction_date='{$transaction_date}' " : 1;

        $where = "WHERE $id AND $account_name AND $particulars AND $transaction_date AND $bank_date AND $voucher_type AND $voucher_no";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_bank_transactions $where ORDER BY transaction_time DESC LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['transaction_date'] = rb_date($value->transaction_date);
                $listArr[$i]['bank_date'] = rb_date($value->bank_date);
                $listArr[$i]['particulars'] = $value->particulars;
                $listArr[$i]['voucher_type'] = $value->voucher_type;
                $listArr[$i]['voucher_no'] = $value->voucher_no;
                $listArr[$i]['opening'] = number_format($value->opening, 2);
                $listArr[$i]['credit'] = number_format($value->credit, 2);
                $listArr[$i]['debit'] = number_format($value->debit, 2);
                $listArr[$i]['closing'] = number_format($value->closing, 2);
                $listArr[$i]['note'] = $value->note;
                $listArr[$i]['updated_at'] = rb_date($value->updated_at) . '<br/>' . rb_time($value->updated_at);

                $edit = "<a href='" . admin_url() . "admin.php?page=bank-account-edit&id={$value->id}&action=edit' "
                        . "title=Edit class=btn-edit ><img alt='' src='{$asset_url}edit.png'></a>&nbsp;&nbsp;";
                $delete = has_this_role('accounts') ? "<a href='" . admin_url() . "admin.php?page=bank-account&id={$value->id}&action=delete' "
                        . "onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete title='Delete' ><img alt='' src='{$asset_url}delete.png'></a>" : '';

                $listArr[$i]['action'] = $edit . $delete;
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_bank_transactions $where"); //count($rows);
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

function admin_ctm_petty_cash_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $particulars = !empty($getdata['particulars']) ? $getdata['particulars'] : '';
    $account_name = !empty($getdata['account_name']) ? $getdata['account_name'] : '';
    $voucher_no = !empty($getdata['voucher_no']) ? $getdata['voucher_no'] : '';
    $voucher_type = !empty($getdata['voucher_type']) ? $getdata['voucher_type'] : '';
    $transaction_date = !empty($getdata['transaction_date']) ? $getdata['transaction_date'] : '';
    $bank_date = !empty($getdata['bank_date']) ? $getdata['bank_date'] : '';

    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $action = !empty($getdata['action']) ? $getdata['action'] : '';

    if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_bank_transactions WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        //Create an instance of our package class...
        $testListTable = new CTM_Petty_cash_List_Table();
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
            <h1 class="wp-heading-inline">Bank Current Account</h1>
            <a  id="add-new-client" href="<?= 'admin.php?page=bank-account-add' ?>" class="page-title-action btn-primary" target="_blank">Transfer Between Own Accounts</a>
            <a  id="add-new-client" href="<?= 'admin.php?page=bank-account-registry' ?>" class="page-title-action btn-primary" target="_blank">Current Account Reconciliation </a>
            <br/>
            <?php if (!empty($getdata['msg'])) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Transaction has been delete successfully.
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
                                                        <input type=text name="id" value="<?= $id ?>" class="search-input" placeholder="ID"  >
                                                    </td>
                                                    <td>
                                                        <select name="account_name" onchange="this.form.submit()">
                                                            <option value="">Source Account</option>
                                                            <?php
                                                            foreach (PAYMENT_SOURCE as $value) {
                                                                $selected = $value == $account_name ? 'selected' : '';
                                                                ?>
                                                                <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="<?= $transaction_date ? 'date' : 'text' ?>" name="transaction_date" value="<?= $transaction_date ?>"  class="search-input"  placeholder="Search By Transaction Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>
                                                    <td>
                                                        <input type="<?= $bank_date ? 'date' : 'text' ?>" name="bank_date" value="<?= $bank_date ?>"  class="search-input"  placeholder="Search By Verify Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')" >
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type=text name="particulars" value="<?= $particulars ?>" class="search-input" placeholder="Particulars"  >
                                                    </td>
                                                    <td>
                                                        <select name='voucher_type'>
                                                            <option value="">Voucher Type</option>
                                                            <option value="Payment" <?= $voucher_type == 'Payment' ? 'selected' : '' ?>>Payment</option>
                                                            <option value="Receipt" <?= $voucher_type == 'Receipt' ? 'selected' : '' ?>>Receipt</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type=text name="voucher_no" value="<?= $voucher_no ?>" class="search-input" placeholder="Voucher No"  >
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



            });
        </script>
        <?php
    }
}
