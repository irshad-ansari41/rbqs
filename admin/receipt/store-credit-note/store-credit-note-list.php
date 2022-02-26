<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Store_Credit_Note_List_Table extends WP_List_Table {

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
            case 'qtn':
            case 'client_name':
            case 'type':
            case 'scope':
            case 'vat':
            case 'sales_person':
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
            'id' => __('ID'),
            'qtn' => __('QTN'),
            'client_name' => __('Client Name'),
            'type' => __('Quotation Type'),
            'scope' => __('Scope'),
            'vat' => __('Vat Status'),
            'sales_person' => __('Sales Person'),
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

        $qtn = !empty($qtn) ? " (t2.id like  '%" . $qtn . "%' OR  t2.revised_no like  '%" . $qtn . "%') " : 1;
        $qtn_scope = !empty($qtn_scope) ? " t2.scope ='{$qtn_scope}'" : 1;
        $qtn_type = !empty($qtn_type) ? " t2.type ='{$qtn_type}'" : 1;
        $client_id = !empty($client_id) ? " t2.client_id ={$client_id}" : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? 't1.' . filter_input(INPUT_GET, 'orderby') : 't1.id';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE  $qtn AND $qtn_scope AND $qtn_type AND $client_id ORDER BY {$orderby} {$order}";
        $sql = "SELECT t1.id,t1.updated_by,t1.updated_at, t1.quotation_id, t1.meta_ids,t1.status,t2.type,t2.scope,t2.vat,t2.client_id,t2.sales_person FROM {$wpdb->prefix}ctm_store_credit_note t1 LEFT JOIN  {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id=t2.id  $where LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($wpdb->num_rows > 0) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['qtn'] = get_revised_no($value->quotation_id);
                $listArr[$i]['client_name'] = get_client($value->client_id, 'name');
                $listArr[$i]['type'] = $value->type;
                $listArr[$i]['scope'] = $value->scope;
                $listArr[$i]['vat'] = $value->vat == 'wovat' ? 'Zero VAT' : '';
                $listArr[$i]['sales_person'] = get_sales_person($value->sales_person, 'display_name');
                $listArr[$i]['status'] = $value->status == 'Approved' ? "<span class='badge badge-success'>$value->status</span>" : "<span class='badge badge-primary'>$value->status</span>";
                $listArr[$i]['updated_by'] = get_user($value->updated_by, 'display_name');
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $view = !empty($value->meta_ids) ? "<a href='" . admin_url() . "admin.php?page=store-credit-note-view&id={$value->id}' class='btn-view' title='View' ><img alt='' src='{$asset_url}view.png'></a>&nbsp;&nbsp;&nbsp;" : '';
                $delete = "<a href='admin.php?page={$page}&id={$value->id}&action=delete' onclick='return confirm(`are you sure you want to delete?`)' class=btn-delete><img alt='' src='{$asset_url}delete.png'></a>";
                $listArr[$i]['action'] = $view . (has_this_role('accounts') ? $delete : '');

                $i++;
            }
        }
        $data = $listArr;

        function usort_reorder($a, $b) {
            $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? 't1.' . filter_input(INPUT_GET, 'orderby') : 't1.id';
            $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        //$rows = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_clients");
        $count = $wpdb->get_var("SELECT count(t1.id) FROM {$wpdb->prefix}ctm_store_credit_note t1 LEFT JOIN  {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id=t2.id $where"); //count($rows);
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

function admin_ctm_store_credit_note_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($postdata['id']) ? $postdata['id'] : (!empty($getdata['id']) ? $getdata['id'] : '');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $qtn = !empty($getdata['qtn']) ? $getdata['qtn'] : '';
    $type = !empty($getdata['type']) ? $getdata['type'] : '';
    $scope = !empty($getdata['scope']) ? $getdata['scope'] : '';
    $client_id = !empty($getdata['client_id']) ? $getdata['client_id'] : 0;

    $action = !empty($postdata['action']) ? $postdata['action'] : (!empty($getdata['action']) ? $getdata['action'] : '');
    $qid = !empty($postdata['qid']) ? $postdata['qid'] : '';
    $receipt_no = !empty($postdata['receipt_no']) ? $postdata['receipt_no'] : '';

    if ($action == 'create') {
        create_sales_reversal($qid, $receipt_no);
        wp_redirect("admin.php?page={$getdata['page']}&msg=added");
        exit();
    } else if ($id && $action == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_store_credit_note WHERE id={$id}");
        wp_redirect("admin.php?page={$getdata['page']}&msg=delete");
        exit();
    } else {
        ?>
        <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; max-width: 350px; }
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
            <h1 class="wp-heading-inline">Store Credit Note Registry</h1>
            <br/><br/>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="columns-1">
                    <div id="postbox-container" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div id="welcome-to-aquila" class="postbox"><br/>
                                <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                                <div class="inside">
                                    <div id="page-inner-content">
                                        <form id="filter-form1" method="get">
                                            <input type="hidden" name="page" value="<?= $page ?>" />
                                            <table class="form-table">
                                                <tr>
                                                    <td>
                                                        <input type="text" name="qtn" class="search-input" placeholder="QTN" value="<?= $qtn ?>" >
                                                    </td>
                                                    <td>
                                                        <select name="qtn_type" onchange="this.form.submit()">
                                                            <option value="">Select Type</option>
                                                            <option value="Stock" <?= $type == 'Stock' ? 'selected' : '' ?>>Stock</option>
                                                            <option value="Order" <?= $type == 'Order' ? 'selected' : '' ?>>Order</option>
                                                            <option value="Project" <?= $type == 'Project' ? 'selected' : '' ?>>Project</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="qtn_scope" onchange="this.form.submit()">
                                                            <option value="">Select Scope</option>
                                                            <option value="Local" <?= $scope == 'Local' ? 'selected' : '' ?> >Local</option>
                                                            <option value="Export" <?= $scope == 'Export' ? 'selected' : '' ?> >Export</option>
                                                            <option value="Promotion" <?= $scope == 'Promotion' ? 'selected' : '' ?> >Promotion</option>
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
                                            $testListTable = new CTM_Store_Credit_Note_List_Table();
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
                jQuery('#add-new-category').click(() => {
                    jQuery('#add-new-category-form').toggleClass('hide');
                    jQuery('#page-inner-content').toggleClass('hide');
                });
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
                        var client_id = '<?= $client_id ?>';
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
}
