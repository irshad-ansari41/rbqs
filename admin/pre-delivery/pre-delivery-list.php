<?php
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CTM_Pre_Delivery_List_Table extends WP_List_Table {

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
            case 'client':
            case 'pdi_date':
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
            'client' => __('Client Name'),
            'pdi_date' => __('Pre Delivery Date'),
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

        $client_id = !empty($client_id) ? " client_id = '{$client_id}' " : 1;
        $pdi_date = !empty($pdi_date) ? " pdi_date = '{$pdi_date}' " : 1;

        $orderby = (!empty(filter_input(INPUT_GET, 'orderby'))) ? filter_input(INPUT_GET, 'orderby') : 'updated_at';
        $order = (!empty(filter_input(INPUT_GET, 'order'))) ? filter_input(INPUT_GET, 'order') : 'desc';

        $where = "WHERE {$pdi_date} AND $client_id";
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_pre_delivery $where ORDER BY {$orderby} {$order} LIMIT $start, 10";
        $rs = $wpdb->get_results($sql);
        $listArr = array();
        $i = 0;
        $asset_url = get_template_directory_uri() . '/assets/images/';
        if ($rs) {
            foreach ($rs as $value) {
                $listArr[$i]['id'] = $value->id;
                $listArr[$i]['client'] = get_client($value->client_id, 'name');
                $listArr[$i]['pdi_date'] = rb_date($value->pdi_date);
                $listArr[$i]['updated_at'] = rb_datetime($value->updated_at);
                $listArr[$i]['action'] = "<a href='" . admin_url() . "admin.php?page={$page}-view&id={$value->id}&action=view' title=View class=btn-edit>"
                        . "<img alt='' src='{$asset_url}view.png'></a>";
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

        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_pre_delivery $where"); //count($rows);
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

function admin_ctm_pre_delivery_list_page() {
    $getdata = filter_input_array(INPUT_GET);
    $pdi_date = !empty($getdata['pdi_date']) ? trim($getdata['pdi_date']) : '';
    $from_location = !empty($getdata['from_location']) ? trim($getdata['from_location']) : '';
    $to_location = !empty($getdata['to_location']) ? trim($getdata['to_location']) : '';

    //Create an instance of our package class...
    $testListTable = new CTM_Pre_Delivery_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Pre Delivery Inspect List</h1>
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
                                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                        <table class="form-table">
                                            <tr>
                                                <td>
                                                    <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                                        <option value="">Search By Client</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="pdi_date" value="<?= $pdi_date ?>" placeholder="Pre Delivery Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"/>
                                                </td>
                                                <td><button type="submit"  class="button-primary" value="Filter" >Filter</button></td>
                                                <td><a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                            </tr>
                                        </table>
                                    </form>

                                    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                    <form id="deletes-filter" method="get">
                                        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                        <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                                        <!-- Now we can render the completed list table -->
                                        <?php $testListTable->display(); ?>
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
        });
    </script>
    <?php
}
