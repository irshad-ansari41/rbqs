<?php

function make_model_stock_transfer() {
    $postdata = filter_input_array(INPUT_POST);
    $quantity = $postdata['quantity'];
    ?>
    <!-- The Modal -->
    <div class="modal" id="mySTFModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Stock Transfer Form</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <div>
                            <span style="font-size: 12px;">STF Type:<span class="text-red">*</span></span><br/>
                            <label class="font-weight-light" style="font-size: 12px;"><input type="radio" name="stf_type" class="stf_type" value="New" required >New</label>
                            &nbsp;&nbsp;
                            <label class="font-weight-light" style="font-size: 12px;"><input type="radio" name="stf_type" class="stf_type" value="Current" required >Current</label>
                            &nbsp;&nbsp;
                            <label class="font-weight-light" style="font-size: 12px;"><input type="radio" name="stf_type"  class="stf_type" value="Existing" >Existing</label>
                        </div>
                        <input type="number" name="stf_id" id="stf_id" class="form-control" placeholder="STF ID" required>
                        <input type="hidden" name="po_meta_id" value="<?= $postdata['po_meta_id'] ?>" >
                        <input type="hidden" name="from_location"  value="<?= $postdata['from_location'] ?>" >
                        <input type="hidden" name="to_location"  value="<?= $postdata['si_location'] ?>" >
                        <span style="font-size: 12px;">Quantity</span>
                        <input type="number" name="quantity" class="form-control" min='1' max="<?= $quantity ?>" value="<?= $quantity ?>" placeholder="Quantity">
                        <span style="font-size: 12px;">CQUE</span>
                        <input type="text" name="cque" class="form-control" placeholder="CQUE/QTN" value="<?= $postdata['cque'] ?>">
                        <span style="font-size: 12px;">NO. OF PKGS</span>
                        <input type="number" name="pkgs" class="form-control" placeholder="NO. OF PKGS" value="<?= $postdata['pkgs'] ?>">
                        <span style="font-size: 12px;">PURPOSE</span>
                        <input type="text" name="purpose" class="form-control" placeholder="PURPOSE"><br/>
                        <button type="reset" class="btn btn-dark btn-sm float-left">Reset</button>
                        <button type="submit" name="stock_transfer" value="1" class="btn btn-primary btn-sm float-right">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#mySTFModal").modal();
            jQuery('#stf_id').hide().removeAttr('required');
            jQuery('.stf_type').click(function () {
                var pv_type = jQuery('input[name="stf_type"]:checked').val();
                if (pv_type === 'New' || pv_type === 'Current') {
                    jQuery('#stf_id').hide().removeAttr('required');
                }
                if (pv_type === 'Existing') {
                    jQuery('#stf_id').show().prop('required', true);
                }
            });
        });
    </script>
    <?php
}

function make_model_pdi_change() {
    $postdata = filter_input_array(INPUT_POST);
    $quantity = $postdata['quantity'];
    ?>
    <!-- The Modal -->
    <div class="modal" id="myPDiModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Status Change</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <span style="font-size: 12px;">Quantity</span>
                        <input type="number" name="quantity" class="form-control" min='1' max="<?= $quantity ?>" value="<?= $quantity ?>" placeholder="Quantity"><br/>
                        <input type="hidden" name="po_meta_id" value="<?= $postdata['po_meta_id'] ?>" >
                        <button type="submit" name="pdi_status_change" value="1" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myPDiModal").modal();
        });
    </script>
    <?php
}

function make_model_status_change_delivered() {
    $postdata = filter_input_array(INPUT_POST);
    $quantity = $postdata['quantity'];
    ?>
    <!-- The Modal -->
    <div class="modal" id="myStatusModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Status Change</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <span style="font-size: 12px;">Quantity</span>
                        <input type="number" name="quantity" class="form-control" min='1' max="<?= $quantity ?>" value="<?= $quantity ?>" placeholder="Quantity"><br/>
                        <span style="font-size: 12px;">Receipt No</span>
                        <input type="text" name="receipt_no" class="form-control" value="<?= $postdata['receipt_no'] ?>" placeholder="Receipt No"><br/><br/>
                        <input type="hidden" name="po_meta_id" value="<?= $postdata['po_meta_id'] ?>" >                        
                        <button type="submit" name="delivery_status_change" value="1" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myStatusModal").modal();
        });
    </script>
    <?php
}


function make_model_status_change_other() {
    $postdata = filter_input_array(INPUT_POST);
    $quantity = $postdata['quantity'];
    ?>
    <!-- The Modal -->
    <div class="modal" id="myStatusModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Status Change</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post">
                        <span style="font-size: 12px;">Quantity</span>
                        <input type="number" name="quantity" class="form-control" min='1' max="<?= $quantity ?>" value="<?= $quantity ?>" placeholder="Quantity"><br/>
                        <input type="hidden" name="status" class="form-control" value="<?= $postdata['si_status'] ?>"><br/><br/>
                        <input type="hidden" name="po_meta_id" value="<?= $postdata['po_meta_id'] ?>" >                        
                        <button type="submit" name="other_status_change" value="1" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myStatusModal").modal();
        });
    </script>
    <?php
}