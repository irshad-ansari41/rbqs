<?php

function admin_ctm_stock_transfer_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=stock-transfer'));
        exit();
    }

    if (!empty($postdata['update'])) {
        $data = ['st_date' => $postdata['st_date'], 'requested_by' => $postdata['requested_by'], 'delivered_by' => $postdata['delivered_by'], 'received_by' => $postdata['received_by'], 'price_tag_created_by' => $postdata['price_tag_created_by'], 'requested_date' => $postdata['requested_date'], 'delivered_date' => $postdata['delivered_date'], 'received_date' => $postdata['received_date'], 'price_tag_created_date' => $postdata['price_tag_created_date'],];
        $wpdb->update("{$wpdb->prefix}ctm_stock_transfer", $data, ['id' => $id], wpdb_data_format($data), ['%d']);
        foreach ($postdata['status'] as $meta_id => $value) {
            $wpdb->update("{$wpdb->prefix}ctm_stock_transfer_meta", ['status' => $value], ['id' => $meta_id], ['%s'], ['%d']);
            if ($value == 'Approved') {
                $to_location = $wpdb->get_var("SELECT to_location FROM {$wpdb->prefix}ctm_stock_transfer where id='$id'");
                $stf_meta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_stock_transfer_meta where id='$meta_id'");
                stock_inventroy_location_change($stf_meta->po_meta_id, $stf_meta->quantity, $to_location);
                stock_inventroy_combine_quantity($stf_meta->po_meta_id);
            }
        }
    }


    $stf = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_stock_transfer where id='$id'");
    $stf_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_stock_transfer_meta where stf_id='$id'");
    ?>
    <div class="wrap">
        <form method="post">
            <div id='welcome-to-aquila' class='postbox'>
                <div class='inside' style='max-width:800px;margin:auto'>

                    <?php
                    $html = "
            <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], 
            #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], 
            #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], 
            #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
            #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
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
                            <td colspan=3 style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            </td>
                        </tr>
                        <tr>
                        <td><b>SL No. {$stf->id}</b><br/><br/><b>FROM : {$stf->from_location}</b><br/><b>DATE : " . rb_date($stf->st_date) . "</b></td>
                            <td  style='text-align:center'><br/><br/>
                                <h6><span style='font-size:22px;'>RBFD STOCK TRANSFER FORM</span></h6><br/><br/>
                            </td>
                        <td><b>TO: {$stf->to_location}</b></td>
                        </tr>
                    </table>
                     <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>

              <tr class=bg-blue>
              <td colspan='" . (empty($getdata['pdf']) ? 12 : 11) . "' class='text-center'><b>SHOWROOM</b></td>
              </tr>

              <tr class='text-center'>
              <td class='text-center'><b>COLLECTION</b></td>
              <td class='text-center'><b>CATEGORY</b></td>
              <td class='text-center'><b>SUPPLIER</b></td>
              <td class='text-center'><b>SUPPLIER CODE</b></td>
              <td class='text-center'><b>ENTRY No.</b></td>
              <td class='text-center' style='width:350px'><b>ITEM DESCRIPTION</b></td>
              <td class='text-center'><b>IMAGE</b></td>
              <td class='text-center'><b>QTY</b></td>
              <td class='text-center'><b>CQUE</b></td>
              <td class='text-center'><b>QTN</b></td>
              <td class='text-center'><b>PURPOSE</b></td>"
                            . (empty($getdata['pdf']) ? "<td class='text-center'><b>STATUS</b></td>" : '') .
                            "</tr>";

                    foreach ($stf_meta as $value) {
                        $po_meta = get_po_meta_data($value->po_meta_id);
                        $item = get_item($po_meta->item_id);
                        $category = get_item_category($item->category, 'name');
                        $sup_name = get_supplier($item->sup_code, 'name');
                        $client_name = get_client($po_meta->client_id, 'name');
                        $qtn = get_revised_no($po_meta->quotation_id);

                        $checked1 = $value->status == 'Pending' ? 'checked' : 'disabled';
                        $checked2 = $value->status == 'Approved' ? 'checked' : '';

                        $html .= "<tr class='text-center'>
                    <td class='text-left'>{$item->collection_name}</td>
                    <td class='text-left'>{$category}</td>
                    <td class='text-left'>{$sup_name}</td>
                    <td class='text-center'>{$po_meta->sup_code}</td>
                    <td class='text-center'>{$po_meta->entry}</td>
                    <td class='text-left'>" . nl2br($po_meta->item_desc) . "</td>
                    <td class='text-center'><img src='" . get_image_src($item->image) . "' width=50  style='margin: auto;width: 50px; '></td>
                    <td class='text-center'>{$value->quantity}</td>
                    <td class='text-left'>{$value->cque}</td>
                    <td class='text-center'>{$client_name}<vr/>{$qtn}</td>
                    <td class='text-left'>{$value->purpose}</td>"
                                . (empty($getdata['pdf']) ? "<td class='text-left'><label><input type='radio' name='status[{$value->id}]' $checked1 value='Pending' required />Pending</label>&nbsp;&nbsp;
                    <label><input type='radio' name='status[{$value->id}]' $checked2 value='Approved' required />Approved</label></td>" : '') .
                                "</tr>";
                    }


                    if (!empty($getdata['pdf'])) {
                        $html .= "<tr class=bg-blue><td colspan='" . (empty($getdata['pdf']) ? 12 : 11) . "' style='height: 25px;'></td></tr>

                            <tr class='text-center'>
                              <td class='text-left' style='height:100px' colspan=3><b>REQUESTED&nbsp;BY</b></td>
                              <td class='text-left;' colspan=4>$stf->requested_by</td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2>" . rb_date($stf->requested_date) . "</td>
                            </tr>

                             <tr class='text-center'>
                              <td class='text-left'  style='height:100px' colspan=3><b>DELIVERED&nbsp;BY</b></td>
                              <td class='text-left' colspan=4>{$stf->delivered_by}</td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2>" . rb_date($stf->delivered_date) . "</td>
                             </tr>

                             <tr class='text-center'>
                              <td class='text-left'  style='height:100px' colspan=3><b>RECEIVED&nbsp;BY</b></td>
                              <td class='text-left' colspan=4>{$stf->received_by}</td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2>" . rb_date($stf->received_date) . "</td>
                             </tr>

                             <tr class='text-center'>
                              <td class='text-left'  style='height:100px' colspan=3><b>PRICE TAG CREATED&nbsp;BY</b></td>
                               <td class='text-left' colspan=4>{$stf->price_tag_created_by}</td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2>" . rb_date($stf->price_tag_created_date) . "</td>
                             </tr>";
                    } else {
                        $html .= "<tr class=bg-blue><td colspan='" . (empty($getdata['pdf']) ? 12 : 11) . "' style='height: 25px;'></td></tr>

                            <tr class='text-center'>
                              <td class='text-left' colspan=3><b>REQUESTED&nbsp;BY</b></td>
                              <td class='text-center;' colspan='5'><textarea name='requested_by' rows=4>{$stf->requested_by}</textarea></td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2><input type='date' value='{$stf->requested_date}' name='requested_date'></td>
                            </tr>

                             <tr class='text-center'>
                              <td class='text-left' colspan=3><b>DELIVERED&nbsp;BY</b></td>
                              <td class='text-center' colspan='5'><textarea name='delivered_by' rows=4>{$stf->delivered_by}</textarea></td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2><input type='date' value='{$stf->delivered_date}' name='delivered_date'></td>
                             </tr>

                             <tr class='text-center'>
                              <td class='text-left' colspan=3><b>RECEIVED&nbsp;BY</b></td>
                              <td class='text-center' colspan='5'><textarea name='received_by' rows=4>{$stf->received_by}</textarea></td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2><input type='date' value='{$stf->received_date}' name='received_date'></td>
                             </tr>

                             <tr class='text-center'>
                              <td class='text-left' colspan=3><b>PRICE TAG CREATED&nbsp;BY</b></td>
                               <td class='text-center' colspan='5'><textarea name='price_tag_created_by' rows=4>{$stf->price_tag_created_by}</textarea></td>
                              <td class='text-center' colspan=2><b>DATE</b></td>
                              <td class='text-center' colspan=2><input type='date' value='{$stf->price_tag_created_date}' name='price_tag_created_date'></td>
                             </tr>";
                    }

                    $html .= "</table>

         <br/><br/><br/><br/>


         ";
                    echo $html;

                    $pdf_file = $stf->pdf_path;
                    store_pdf_path('ctm_stock_transfer', $stf->id, "stock_transfer_{$stf->id}.pdf", $pdf_file);
                    ?>

                    <?php
                    if (has_role_super_and_admin() || has_this_role('commercial')) {
                        ?>
                        <label>STF Date: </label>
                        <input type="date" name="st_date" value="<?= $stf->st_date ?>" style="width:175px" required />
                    <?php } else { ?>
                        <input type="hidden" name="st_date" value="<?= $stf->st_date ?>" readonly />
                    <?php } ?>

                </div>
            </div>
            <div class="row btn-bottom">
                <div class="col-sm-12 text-center">
                    <?php
                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                        echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                    } if (pdf_exist($pdf_file)) {
                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                    }
                    ?>

                    <button type="submit" name="update" class="btn btn-primary btn-sm" value="update" onclick='return confirm(`are you sure you want to update?`)'>Update</button>
                </div>
            </div>
        </form>
    </div>
    <?php
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('stf_copy_dir'));
}
