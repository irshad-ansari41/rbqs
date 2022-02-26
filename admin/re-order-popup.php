<?php

function make_model_reorder($postdata) {
    global $wpdb;
    $quantity = $postdata['quantity'];
    $reorder_id = $postdata['reorder_id'];
    $order_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations WHERE status='Pending' AND is_showroom=1  ORDER BY id DESC");
    ?>
    <!-- The Modal -->
    <div class="modal" id="myReOrderModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Reorder Form</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <div>
                            <span style="font-size: 12px;">Reorder Type:<span class="text-red">*</span></span><br/>
                            <label class="font-weight-light" style="font-size: 12px;"><input type="radio" name="order_type" class="order_type" value="New" required >New</label>
                            &nbsp;&nbsp;
                            <label class="font-weight-light" style="font-size: 12px;"><input type="radio" name="order_type"  class="order_type" value="Existing" >Existing</label>  
                        </div>
                        <input type="number" name="order_id" id="order_id" class="form-control" placeholder="Order ID" value="<?=$order_id?>" required>
                        <input type="hidden" name="reorder_id" value="<?= $reorder_id ?>" >
                        <span style="font-size: 12px;">Quantity</span>
                        <input type="number" name="quantity" class="form-control" min='1' max="<?= $quantity ?>" value="<?= $quantity ?>" placeholder="Quantity"><br/>
                        <button type="reset" class="btn btn-dark btn-sm float-left">Reset</button>
                        <button type="submit" name="make_reorder" value="1" class="btn btn-primary btn-sm float-right">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myReOrderModal").modal();
            jQuery('#order_id').hide().removeAttr('required');
            jQuery('.order_type').click(function () {
                var pv_type = jQuery('input[name="order_type"]:checked').val();
                if (pv_type === 'New' || pv_type === 'Current') {
                    jQuery('#order_id').hide().removeAttr('required');
                }
                if (pv_type === 'Existing') {
                    jQuery('#order_id').show().prop('required', true);
                }
            });
        });
    </script>
    <?php
}
