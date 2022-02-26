<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_package_label_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    $containers_name = !empty($getdata['containers_name']) ? array_filter($getdata['containers_name']) : [];

    $containers = $wpdb->get_results("SELECT container_name FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name!='' GROUP BY container_name");

    $results = [];
    if (!empty($containers_name)) {
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name IN ('" . implode("', '", $containers_name) . "') AND no_of_pkgs>0 ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) DESC";
        $po_items = $wpdb->get_results($sql);
        foreach ($po_items as $value) {
            $client = get_client($value->client_id, 'name');
            $item = get_item($value->item_id);
            $category = get_item_category($item->category, 'name');
            if ($value->no_of_pkgs > 1) {
                for ($i = 1; $i <= $value->no_of_pkgs; $i++) {
                    $results[] = (object) ['entry' => $value->entry, 'client_id' => $value->client_id, 'cque' => $client, 'category' => $category, 'doa' => $value->arrival_date, 'pkgs' => "{$i}/{$value->no_of_pkgs}", 'desc' => $item->collection_name];
                }
            } else {
                $results[] = (object) ['entry' => $value->entry, 'client_id' => $value->client_id, 'cque' => $client, 'category' => $category, 'doa' => $value->arrival_date, 'pkgs' => "{$value->no_of_pkgs}/1", 'desc' => $item->collection_name];
            }
        }
    }
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Success!</strong> <?= $msg ?>
                            </div>
                        <?php } ?>

                        <form method="get">
                            <input type="hidden" name="page" value="<?= $page ?>" />
                            <table class="form-table">
                                <tr>
                                    <td style="vertical-align: top;text-align: left">
                                        <label>Select Containers</label>
                                        <select name="containers_name[]" required  class="chosen-select" multiple='true'>
                                            <option value="">Select Containers</option>
                                            <?php
                                            foreach ($containers as $value) {
                                                $selected = in_array($value->container_name, $containers_name) ? 'selected' : '';
                                                echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td style="vertical-align: top">
                                        <label>&nbsp;</label><br/>
                                        <input type="submit" name="update" value="Filter" class="btn btn-primary btn-sm" />
                                    </td>
                                    <td style="vertical-align: top">
                                        <label>&nbsp;</label><br/>
                                        <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <br/>
                        <form method=post>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>
                                    <?php
                                    $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th,table tr td{font-size:28px; font-weight:bold;padding:0 3px;}
                                        table{width:100%;}
                                        table#confirm-order-items {table-layout: initial!important;}
                                        #confirm-order-items tr th{white-space: nowrap;}
                                    </style>
                                    ";

                                    $table = "<table id='confirm-order-items' class='' border=0 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>";

                                    $i = 1;
                                    $body = "<tr>";
                                    foreach ($results as $value) {

                                        $style = $value->client_id != FLAGSHIP_ID ? "color:red;font-size:28px;" : "font-size:28px;";

                                        $t = "<table>"
                                                . "<tr><td style='width:100px'>ENTRY</td><td>: {$value->entry}</td></tr>"
                                                . "<tr><td>CQUE</td><td>: <span style='$style'>{$value->cque}</span></td></tr>"
                                                . "<tr><td>CAT</td><td>: {$value->category}</td></tr>"
                                                . "<tr><td>D O T</td><td>: " . rb_date($value->doa, 'd.m.Y') . "</td></tr>"
                                                . "<tr><td>PKGS</td><td>: {$value->pkgs}</td></tr>"
                                                . "<tr><td colspan=2 style='line-height: 25px;padding-top: 3px;'>DESC &nbsp;&nbsp;:<b style='font-size:18px;font-weight:bold'> $value->desc</b></td></tr>"
                                                . "</table>";

                                        $body .= "<td style='border:1px solid #000;vertical-align: top; height:335px'>$t</td>";

                                        if ($i % 2 == 0) {
                                            $body .= "</tr><tr>";
                                        }
                                        $i++;
                                    }
                                    $body .= "</tr>";
                                    /**/
                                    $table .= $body;


                                    $table .= "</table>";
                                    $html .= $table;
                                    $html .= "";
                                    echo $html;
                                    $pdf_file = make_pdf_file_name("PACKAGE_LABEL_$date.pdf")['path'];
                                    ?>
                                </div>
                            </div>
                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-left">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url('pdf=1&hide_footer=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                    }
                                    ?>
                                    <br/><br/>
                                </div>                                
                            </div>
                        </form>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('.chosen-select').chosen();
        });
    </script>

    <?php
    generate_pdf($html, $pdf_file, null, true);
}
