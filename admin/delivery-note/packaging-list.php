<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_packaging_list_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $dn_id = !empty($getdata['dn_id']) ? $getdata['dn_id'] : '';

    
    if (!empty($postdata)) {
        foreach ($postdata['dimension'] as $key => $value) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_dn_meta SET dimension='{$value}' WHERE id='{$key}'");
        }
    }


    $dn = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn WHERE id='{$dn_id}'");
    $dn_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn_meta WHERE dn_id='{$dn->id}'");

    $no_of_pkgs = $cl_cbm = $cl_kg = 0;
    foreach ($dn_items as $value) {
        $po_meta = get_po_meta_data($value->po_meta_id);
        $no_of_pkgs += $po_meta->no_of_pkgs;
        $cl_cbm += $po_meta->cl_cbm;
        $cl_kg += $po_meta->cl_kg;
        $hs_codes[] = get_item($value->item_id, 'hs_code');
    }

    $tax_invoice = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice WHERE quotation_id='{$dn->quotation_id}'");

    $client = get_client($dn->client_id);
    $qtn = get_revised_no($dn->quotation_id);
    $updated_at = rb_date($dn->updated_at);
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
        <h1 class="wp-heading-inline">PACKAGING LIST</h1>
        <a href = 'admin.php?page=delivery-note-list' class='page-title-action'>Back</a>&nbsp;
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <form method=post>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:800px;margin:auto'>
                                    <form action='' method=post>

                                        <?php
                                        $html = "
                <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                    table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                    h6 span{text-transform:uppercase;}
                    p{margin-bottom: 0;}
                    table{empty-cells:hidden;}
                    table tr td{font-size:12px;font-family:'Tahoma'}
                    table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                    .text-center{text-align:center;}
                    .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                    .attachment-large{width:50px;height: auto;}
                    ul{margin: auto;padding: 0 15px;}
                    ul#terms{ padding: 0; margin: 0;} 
                    ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
                </style>
                
                        <br/>
                        <table width='800'>
                            <tr valign='top'>
                                <td colspan=2 style='text-align:right'>
                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                </td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align:center'><br/><br/>
                                    <h6><span style='font-size:22px;'>PACKAGING LIST</span></h6><br/><br/>
                                </td>
                            </tr>
                        </table>
                         <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                  <tr class=bg-blue>
                  <td colspan='3'><b>Customer Details</b></td><td class='text-center' colspan=2><b>Tax Invoice # </b></td><td class='text-center' style='width:100px'><b>Date</b></td>
                  </tr>
                  <tr>
                  <td><b>Name:</b></td><td colspan='2'><b>{$client->name}</b></td><td class='text-center' colspan=2><b>{$tax_invoice}</b></td><td class='text-center'><b>$updated_at</b></td>
                  </tr>
                  <tr>
                  <td rowspan=2><b>Address</b></td><td colspan='2'><b>" . ($dn->address ? $dn->address : $client->address) . "</b></td><td class='bg-blue text-center' colspan=2><b>Quotation #</b></td><td class='bg-blue text-center'><b>Destination</b></td>
                  </tr>
                  <tr>
                  <td colspan='2'><b>{$client->city}, {$client->country}</b></td><td class='text-center' colspan=2><b>{$qtn}</b></td><td class='text-center'><b>Export</b></td>
                  </tr>
                  <tr>
                  <td><b>Phone</b></td><td colspan='2'><b>{$client->phone}</b></td><td  class='bg-blue text-center' rowspan='2' colspan=3 ><b>Roche Bobois<br/>TRN# 100383178900003</b></td>
                  </tr>

                  <tr>
                  <td><b>TRN</b></td><td colspan='2'><b>{$client->trn}</b></td>
                  </tr>
                  

                  <tr><td colspan='6'><br/></td></tr>
                  <tr>
                  <td class='bg-blue'><b>HS Code:</b></td><td colspan='2'><b>" . implode(',', $hs_codes) . "</b></td><td class='text-center bg-blue'><b>Weight</b></td><td class='text-center' colspan=2><b>" . number_format($cl_kg, 2) . " KGS</b></td>
                  </tr>
                  <tr>
                  <td class='bg-blue'><b>No of PKGS</b></td><td colspan='2'><b>{$no_of_pkgs}</b></td><td class='bg-blue text-center' ><b>CBM</b></td><td class='text-center' colspan=2><b>" . number_format($cl_cbm, 4) . "</b></td>
                  </tr>
                  



                  <tr><td colspan='6'><br/></td></tr>
                  <tr class='text-center bg-blue'>
                  <td class='text-center'><b>SUPPLIER CODE</b></td>
                  <td class='text-center'><b>ENTRY #</b></td>
                  <td class='text-center'><b>ITEM DESCRIPTION</b></td>
                  <td class='text-center'><b>QTY</b></td>
                  <td class='text-center'><b>COO</b></td>
                  <td class='text-center'><b>DIMENTION</b></td>
                  </tr>";

                                        $total = 0;

                                        foreach ($dn_items as $value) {
                                            $total += $value->quantity;
                                            $country_name = get_country(get_supplier($value->sup_code, 'country_origin'), 'name');
                                            $html .= "<tr>"
                                                    . "<td class='text-center'>{$value->sup_code}</td>"
                                                    . "<td class='text-center'>{$value->entry}</td>"
                                                    . "<td>" . nl2br($value->item_desc) . "</td>"
                                                    . "<td class='text-center'>{$value->quantity}</td>"
                                                    . "<td class='text-center' style='text-transform:uppercase'>{$country_name}</td>"
                                                    . "<td class='text-center'>"
                                                    . (empty($getdata['pdf']) ? "<textarea rows=6 name='dimension[$value->id]' />$value->dimension</textarea>" : '')
                                                    . (!empty($getdata['pdf']) ? "$value->dimension" : '') . "</td>"
                                                    . "</tr>";
                                        }
                                        $html .= "<tr class='text-right font-weight-bold'>
                  <td colspan=3 class='text-center'><b>TOTAL</b></td><td class='text-center'><b>{$total}</b></td><td></td><td>CBM - ".number_format($cl_cbm, 4)."</td></tr>
                  <tr><td colspan='6' class='text-center'><b>GOODS RECEIVED IN GOOD CONDITION</b></td></tr>
                  </table>
                <br/><br/><br/><br/>
                ";
                                        echo $html;

                                        $pdf_file = make_pdf_file_name("PACKAGE_LIST_{$dn_id}.pdf")['path'];
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
                                    <button type="submit" class="btn btn-primary btn-sm" >Update Dimension</button>
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
    generate_pdf($html, $pdf_file, null, null);
}
