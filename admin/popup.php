<?php

function make_model($fields, $status) { ?>
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

                    <form action="">
                        <?php foreach ($fields as $key => $value) { ?>
                            <div class="form-group">
                                <span><?= $value ?></span>
                                <input type="text" name="<?= $key ?>" class="form-control" required="required" placeholder="Enter <?= $value ?>" id="<?= $key ?>">
                            </div>
                        <?php } ?>
                        <input type="hidden" name="<?= $status[0] ?>"  id="<?= $status[1] ?>">
                        <button type="submit" name="save" class="btn btn-primary btn-block">Save</button>
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
<?php } ?>


<?php

function make_model_delivery() {
    $postdata = filter_input_array(INPUT_POST);
    ?>
    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Flagship & Stock Registry</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="" method="post">
                        <span>Receipt No</span>
                        <input type="text" name='receipt_no' placeholder="Receipt No" class="form-control" required="required"><br/>
                        <span>Receiver's Name</span>
                        <input type="text" name='receiver_name' placeholder="Receiver's Name" class="form-control" required="required"><br/>
                        <span>Delivered By</span>
                        <input type="text" name='delivery_by' placeholder="Delivered By" class="form-control" required="required"><br/>
                        <span>Delivery Date</span>
                        <input type="date" name='delivery_date' placeholder="Delivery Date" class="form-control" required="required"><br/>
                        <span>Delivery Time</span>
                        <input type="time" name='delivery_time' placeholder="Delivery Time" class="form-control" required="required"><br/>
                        <input type="hidden" name="sup_code"  value="<?= $postdata['sup_code'] ?>">
                        <button type="submit" name="transit" value="1" class="btn btn-primary btn-block">Save</button>
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
<?php } ?>