<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_view_showroon_order_page() {

    global $wpdb;
    $postdata = filter_input_array(INPUT_POST);
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=showroom-order'));
        exit();
    }
    $date = current_time('mysql');

    if (!empty($postdata['confrim_order'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='CONFIRMED', updated_at= '{$date}' WHERE id='{$postdata['id']}'");
        create_confirm_order($postdata['id']);
        status_change_send_email('CONFIRMED');
        $msg = "<strong>Success!</strong> Quotation has been confirmed successfully.";
    }

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where  id='{$id}'");

    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$quotation->id}' ORDER BY id ASC");



    if (!empty($msg)) {
        ?>
        <br/>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?= $msg ?>
        </div>
        <?php
    }
    $qtn = get_revised_no($quotation->id);

    $html = "
            <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
            #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
            #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
            #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
                table tr th,table tr td,p,b,span{font-size:11px;font-family:'Tahoma'}
                h6 span{text-transform:uppercase;}
                p{margin-bottom: 0;}
                table{empty-cells:hidden;}
                table tr td{font-size:11px;font-family:'Tahoma'}
                .fnt11{font-size:10px;font-family:'Tahoma'}
                table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                .text-center{text-align:center;}
                .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                .attachment-large{width:50px;height: auto;}
                ul{margin: auto;padding: 0 15px;}
                ul#terms{ padding: 0; margin: 0;} 
                ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
            </style>
            <div id='welcome-to-aquila' class='postbox'>
                <div class='inside' style='max-width:800px;margin:auto'>
                    <br/>
                    <table width='800'>
                        <tr valign='top'>
                            <td style='text-align:left'></td>
                            <td style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan='2' style='text-align:center'><br/>
                                <h6><span style='font-size:22px;text-transform: uppercase;'>FLAGSHIP ORDER</span></h6>
                            </td>
                        </tr>
                    </table>
                    <br/>
                     <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
              <tr class=bg-blue>
              <td colspan='4'><b>Order ID # </b></td><td class='text-center' colspan=3 width=100><b>Date</b></td>
              </tr>
              <tr>
              <td colspan=4><b>{$quotation->id}</b></td>
               <td class='text-center' colspan=3 ><b>" . rb_datetime($quotation->updated_at) . "</b></td>
              </tr>

              <tr class='text-center bg-blue'>
                <td class='text-center'><b>Sr.</b></td>
                <td class='text-center'><b>SUP</b></td>
                <td class='text-center'><b>Item Name</b></td>
                <td class='text-center'><b>Item Description</b></td>
                <td class='text-center'><b>Image</b></td>
                <td class='text-center'><b>qty</b></td>
                <td class='text-center'><b>HS&nbsp;Code</b></td>
              </tr>";
    $i = 1;
    foreach ($quotation_meta as $value) {
        $item = get_item($value->item_id);
        $image = !empty($item->image) ? wp_get_attachment_image($item->image, 'large') : 'NA';
        $html .= "<tr>"
                . "<td class='fnt11 text-center' >" . $i++ . "</td>"
                . "<td class='fnt11 text-center' >" . $item->sup_code . "</td>"
                . "<td class='fnt11 text-center' >" . $item->collection_name . "</td>"
                . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                . "<td class=fnt11 >" . $image . "</td>"
                . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                . "<td class='fnt11 text-center' >" . $item->hs_code . "</td>"
                . "</tr>";
    }



    $html .= "</table><br/><br/><br/>
              <table width='800'>
                  <tr>
                      <td><b>For Roche Bobois</b></td>
                      <td style='text-align:right'></td>
                  </tr>
              </table>
              <br/><br/><br/><br/>
              </div>
              </div>";
    echo $html;
    $pdf_file = $quotation->pdf_path;
    store_pdf_path('ctm_quotations', $quotation->id, "FLAGSHIP_$quotation->id.pdf", $pdf_file);
    ?>

    <form method="post">
        <div class="row">
            <div class="col-sm-2 text-center">
                <a href="<?= admin_url("admin.php?page=showroom-order") ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
            </div>
            <div class="col-sm-6 text-center">
                <?php
                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                } if (pdf_exist($pdf_file)) {
                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a> &nbsp;&nbsp;&nbsp;&nbsp;";
                }
                ?>
            </div>
            <div class="col-sm-4 text-center">
                <input type="hidden" name="id" value="<?= $quotation->id ?>"/>
                <button type="submit" name="confrim_order" value="1" class="btn btn-primary btn-sm " 
                        <?= $quotation->status=='CONFIRMED' ? 'disabled' : '' ?>>Confirm Order</button>
            </div>
        </div>
    </form>

    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('showroom_order_copy_dir'));
}
?>