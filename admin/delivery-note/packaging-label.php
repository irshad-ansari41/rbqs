<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_packaging_label_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $dn_id = !empty($getdata['dn_id']) ? $getdata['dn_id'] : '';

    if (!empty($postdata)) {
        update_option("packing_label_{$dn_id}", $postdata['pack']);
    }


    $dn_items = [];
    if (!empty($dn_id)) {
        $dn = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn WHERE id='{$dn_id}'");
        $dn_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn_meta WHERE dn_id='{$dn->id}'");
        $client_name = get_client($dn->client_id, 'name');
        $packs = get_option("packing_label_{$dn_id}");
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
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">PACKAGING LABEL</h1>
        <a href = 'admin.php?page=delivery-note-list' class='page-title-action'>Back</a>&nbsp;
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <form method=post>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>
                                    <?php
                                    $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th,table tr td{font-weight:bold;padding:0 3px;text-align:center;}
                                        table{width:100%;}
                                        table#confirm-order-items {table-layout: initial!important;}
                                        #confirm-order-items tr th{white-space: nowrap;}
                                    </style>
                                    ";

                                    $table = '';
                                    foreach ($dn_items as $value) {
                                        $table .= "<table id='confirm-order-items' class='' border=0 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>";
                                        $pack = $packs[$value->id] ?? '';
                                        $item_name = get_item($value->item_id, 'collection_name');
                                        $table .= "<tr><td style='font-size:80px;text-align:center;text-transform:uppercase'>$client_name</td></tr>"
                                                . "<tr><td style='font-size:80px;text-align:center;'>QTN NO: " . ($dn->revised_no ? $dn->revised_no : $dn->quotation_id) . "</td></tr>"
                                                . "<tr><td style='font-size:60px;text-align:center;'>E# {$value->entry} - {$item_name}</td></tr>"
                                                . "<tr><td></td></tr>";
                                        if (!empty($getdata['pdf'])) {
                                            $table .= "<tr><td style='font-size:100px;text-align:center;font-weight:bold'>PACK {$pack}</td></tr>";
                                        } else {
                                            $table .= "<tr><td style='font-size:100px;text-align:center;'>PACK <input type='text' name=pack[$value->id] placeholder='X/Y' value='{$pack}' style='width: 250px; font-size: 100px!important; line-height: normal; margin-top: -25px;' required/></td></tr>";
                                        }
                                        $table .= $dn->delivery_date?"<tr><td style='font-size:30px;text-align:center;color:red'>DEL. DATE: ". rb_date($dn->delivery_date, 'd-M-Y')."</td></tr>":'';
                                        $table .= $dn->delivery_date?"<tr><td style='font-size:30px;text-align:center;color:red'>TIME. DATE: ". rb_time($dn->delivery_time_from)." to ". rb_time($dn->delivery_time_to)."</td></tr>":'';

                                        $table .= "</table>";
                                        $table .= empty($getdata['pdf']) ? "<hr/>" : '';
                                        $table .= "<pagebreak />";
                                    }



                                    $html .= $table;
                                    echo $html;
                                    $pdf_file = make_pdf_file_name("PACKAGE_LABEL_{$dn_id}.pdf")['path'];
                                    ?>
                                </div>
                            </div>
                            <div class="row btn-bottom">
                                <div class="col-sm-6 text-left">
                                    <a href = 'admin.php?page=delivery-note-list' class='btn btn-secondary btn-sm text-white'>Back</a>&nbsp;
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url('pdf=1&hide_footer=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                    }
                                    ?>
                                    <br/><br/>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button type="submit" class="btn btn-primary btn-sm" >Update Pack</button>
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
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });
    </script>

    <?php
    generate_pdf($html, $pdf_file, null, true);
}
