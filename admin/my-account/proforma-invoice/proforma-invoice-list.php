<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Proforma_Invoice_List_Table extends WP_List_Table {

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
            'type' => __('Quotation Type'),
            'scope' => __('Scope'),
            'vat' => __('Vat Status'),
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
            'client_name' => array('client_name', true),
            'revised_no' => array('revised_no', true),
            'type' => array('type', true),
            'scope' => array('scope', true),
            'vat' => array('vat', true),
            'status' => array('status', true),
            'updated_by' => array('updated_by', true),
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

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE trash=0 AND is_showroom=0 AND $keyword AND $qtn_scope AND $qtn_type AND $client_id ORDER BY {$orderby} {$order}  LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $sales_person = get_sales_person($value->sales_person);

                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['revised_no'] = $value->revised_no;
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['type'] = $value->type;
                $listArr[$i]['scope'] = $value->scope;
                $listArr[$i]['vat'] = $value->vat == 'wovat' ? 'Zero VAT' : '';
                $listArr[$i]['status'] = show_qtn_status($value);
                $listArr[$i]['updated_by'] = 'SP ' . $sales_person->sp_id . ' / ' . $sales_person->display_name;
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page=proforma-invoice-view&id={$value->id}' class='btn-view' title='View' >"
                        . "<img alt='' src='{$asset_url}view.png'></a>";
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
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations WHERE trash=0 AND is_showroom=0 AND $keyword AND $qtn_scope AND $qtn_type AND $client_id"); //count($rows);
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

function admin_ctm_proforma_invoice_page() {
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        table.wp-list-table {table-layout: initial!important;}
        .wp-list-table tr th,.wp-list-table tr td{white-space: nowrap;}
        .wp-list-table tr th#client_name,.wp-list-table tr td.client_name{white-space:normal;}
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Proforma Invoice</h1>
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
                                                <td><button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                                    &nbsp;&nbsp;
                                                    <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form id="deletes-filter" method="get">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $page ?>" />
                                        <!-- Now we can render the completed list table -->
                                        <?php
                                        //Create an instance of our package class...
                                        $testListTable = new CTM_Proforma_Invoice_List_Table();
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
