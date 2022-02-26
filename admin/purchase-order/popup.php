<?php

function make_model_confirm() {
    $postdata = filter_input_array(INPUT_POST);
    ?>
    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Confirm Order Registry</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="text" name="confirmation_no" class="form-control" required="required" placeholder="Enter Confirmation No"><br/>
                        <input type="text" name="dispatch_date" class="form-control" placeholder="Dispatch Date" 
                               onblur="(this.type = 'text')" onfocus="(this.type = 'date')" required="required"><br/>
                        <input type="text" name="delivery_date" class="form-control" required="required" placeholder="Delivery Date To FF" 
                               onblur="(this.type = 'text')" onfocus="(this.type = 'date')" ><br/>
                        <input type="hidden" name="item_id"  value="<?= $postdata['item_id'] ?>">
                        <input type="hidden" name="po_meta_id"  value="<?= $postdata['po_meta_id'] ?>">
                        <button type="submit" name="confirm" value="1" class="btn btn-primary btn-sm float-right">Save</button>
                        <button type="reset" class="btn btn-dark btn-sm">Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myModal").modal();
        });
    </script>
    <?php
}

function make_model_dff() {
    $postdata = filter_input_array(INPUT_POST);
    ?>
    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">DFF Order Registry</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="" method="post">
                        <input type="text" name="entry" class="form-control" required="required" placeholder="Enter Entry #"><br/>
                        <input type="text" name="invoice_no" class="form-control" required="required" placeholder="Enter Invoice No #">
                        <div style="margin-top: 12px;margin-bottom: 10px;font-size: 12px;">Invoice Currency<br/>
                            <label class="font-weight-light" style="font-size: 12px;">
                                <input type="radio" name="currency" value="EURO" required>EURO(€)</label>
                            &nbsp;&nbsp;
                            <label class="font-weight-light" style="font-size: 12px;">
                                <input type="radio" name="currency" value="USD" required>USD($)</label><br/>
                        </div>
                        <input type="number" name="invoice_amount" step="0.01" min="0" class="form-control" required="required" placeholder="Enter Invoice Amount"><br/>
                        <input type="text" name="invoice_date" class="form-control" required="required" placeholder="Invoice Date)" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"><br/>
                        <input type="text" name="due_date" class="form-control" required="required" placeholder="Invoice Due Date" onblur="(this.type = 'text')" onfocus="(this.type = 'date')"><br/>
                        <input type="hidden" name="item_id"  value="<?= $postdata['item_id'] ?>">
                        <input type="hidden" name="po_meta_id"  value="<?= $postdata['po_meta_id'] ?>">
                        <button type="submit" name="dff" value="1" class="btn btn-primary btn-sm float-right">Save</button>
                        <button type="reset" class="btn btn-dark btn-sm">Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myModal").modal();
        });
    </script>
    <?php
}

function make_model_project_arrived() {
    $postdata = filter_input_array(INPUT_POST);
    ?>
    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Update Order Registry</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="" method="post">
                        <span>Entry #</span>
                        <input type="text" name="entry" class="form-control" required="required" placeholder="Enter Entry #"><br/>
                        <input type="hidden" name="quotation_id"  value="<?= $postdata['quotation_id'] ?>">
                        <input type="hidden" name="item_id"  value="<?= $postdata['item_id'] ?>">
                        <input type="hidden" name="po_meta_id"  value="<?= $postdata['po_meta_id'] ?>">
                        <button type="submit" name="arrived" value="1" class="btn btn-primary btn-block">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery("#myModal").modal();
        });
    </script>
    <?php
}
