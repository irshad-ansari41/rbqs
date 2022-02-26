<?php

function make_model_send_whatsapp($quotation, $client) {
    global $wpdb, $current_user;
    $subject = strtoupper("ROCHE BOBOIS QTN {$quotation->id} {$client->name} ({$quotation->type})");
    $designation = get_user_meta($current_user->ID, 'designation', true);

    $text_content = "*{$subject}* \n"
            . "Dear {$client->name},"
            . "\n"
            . "\n"
            . "Best Regards\n"
            . "*{$current_user->display_name}*  *{$designation}*\n"
            . "\n"
            . "Roche Bobois Flagship Dubai\n"
            . "SZR E11 North, Al Barsha 1st\n"
            . "Hassanicor Building Ground FLoor\n"
            . "P.O. Box 286, Dubai, UAE \n"
            . "Tel: +97143990393, +97143990207 \n"
            . "www.roche-bobois.com";

    $countries = get_cache_results("SELECT id, name, code FROM {$wpdb->prefix}ctm_country WHERE status=1", ['day' => true]);
    ?>
    <style>

        .form-control{margin-bottom: 5px; font-family: 'Tahoma';font-size:14px;}
        .font10{font-size:10px;}
    </style>
    <!-- The Modal -->
    <div class="modal" id="myWhatsAppModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Send in WhatsApp</h6>
                    <button type="button" id='emailModel' class="close" data-dismiss="modal">&times;</button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form id="whatsapp-form" action="https://api.whatsapp.com/send" method="get" target="_blank">
                        <input type="hidden" id="phone" name="phone">
                        <table style="width:100%">
                            <tr>
                                <td><span>Country Code</span>
                                    <select  id='country_id' class="chosen-select" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $value) { ?>
                                            <option value="<?= $value->code ?>"><?= $value->name ?> <?= $value->code ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td><span>Mobile Number</span>
                                    <input type="text" id="to_number"  class="form-control"  value="<?= !empty($client->phone) ? (int) $client->phone : '' ?>" placeholder="To Number" required>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span>Text</span>
                                    <textarea name="text" rows="15" style="width:100%"><?= $text_content ?></textarea>
                                </td>
                            </tr>
                        </table>    
                        <button type="button" value="1" class="btn btn-primary btn-block" onclick="get_phone()">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function get_phone() {
            var value1 = document.getElementById('country_id').value;
            if (value1 !== '') {
                var value2 = document.getElementById('to_number').value;
                var optionValue = value1 + " " + value2;
                document.getElementById('phone').value = optionValue;
                jQuery('#myWhatsAppModal').modal('toggle');
                jQuery('#whatsapp-form').submit()
            } else {
                alert('Please choose country code.');
            }
        }
    </script>
    <?php
}
