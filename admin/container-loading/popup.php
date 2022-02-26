<?php

function make_model_transit() {
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
                        <span>Arrival Date</span>
                        <input type="date" name="Date of Arrival to Dubai" class="form-control" required="required"><br/>
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
    <?php
}
