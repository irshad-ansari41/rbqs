<?php

function make_model_send_quotation_email($quotation, $client) {
    global $current_user;
    $content_id = 'content';
    $content_settings = array('wpautop' => false, 'media_buttons' => false, 'textarea_name' => $content_id, 'editor_height' => 425, 'textarea_rows' => 40, 'tabindex' => '', 'editor_css' => '', 'editor_class' => '', 'teeny' => false, 'dfw' => false, 'tinymce' => true, 'quicktags' => false);
    $cc_email = '';
    $reply_email = $bcc_email = "$current_user->display_name <$current_user->user_email>";
    $current_user->user_email;
    $subject = strtoupper("ROCHE BOBOIS QTN {$quotation->id} {$client->name} ({$quotation->type})");
    $designation = get_user_meta($current_user->ID, 'designation', true);
    $filename = $quotation->pdf_path;

    $content = "<p style='font-size:14px;'>Dear {$client->name},<p>"
            . "<p style='font-size:14px;'>&nbsp;</p>"
            . "<p style='font-size:14px;'>&nbsp;</p>"
            . "<p style='font-size:14px;'>Best Regards</p>"
            . "<p><img src='".THEME_URL."/assets/images/logo.jpg' width=150><br/></p>"
            . "<p style='font-size:14px;'><b>{$current_user->display_name}</b><br/>{$designation}</p>"
            . "<p style='font-size:12px;'>"
            . "Roche Bobois Flagship Dubai<br/>"
            . "SZR E11 North, Al Barsha 1st<br/>"
            . "Hassanicor Building Ground FLoor<br/>"
            . "P.O. Box 286, Dubai, UAE<br/>"
            . "Tel: <a href='tel:+97143990393'>+971 4 399 0393</a> ext. <a href='tel:+97143990207'>207</a><br/>"
            . "<a href='http://www.roche-bobois.com'>www.roche-bobois.com</a><br/>"
            . "Follow us on <a href='https://www.facebook.com/rocheboboisuae/'><img src='".THEME_URL."/assets/images/face-book.jpg' width=20></a> "
            . "<a href='https://www.instagram.com/rochebobois/'><img src='".THEME_URL."/assets/images/instagram.jpg' width=20></a>"
            . "</p>";
    ?>
    <style>

        .form-control{margin-bottom: 5px; font-family: 'Tahoma';font-size:14px;}
        .font10{font-size:10px;}
    </style>
    <!-- The Modal -->
    <div class="modal" id="myEmailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title">Send Quotation Email</h6>
                    <button type="button" id='emailModel' class="close" data-dismiss="modal">&times;</button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <?php
                    if (!file_exists($filename)) {
                        echo "<span style='color:red'>PDF file not exist. Please generate PDF before send email.</span>";
                    }
                    if (file_exists($filename)) {
                        echo "<span style='color:red'>Please make sure you that have latest PDF file if not please re-generate PDF before send email.</span>";
                    }
                    ?>
                    <form action="" method="post">
                        <span>To Email:</span>
                        <input type="email" name="to_email" class="form-control"  value="<?= !empty($client->email) ? $client->email : '' ?>" placeholder="To Email" required>
                        <span>CC Email: (Add multiple emails separated by comma[,])</span>
                        <input type="text" name="cc_email" class="form-control" value="<?= $cc_email ?>" placeholder="CC Emails">
                        <span>BCC Email:</span>
                        <input type="text" name="bcc_email" class="form-control"  value="<?= $bcc_email ?>" placeholder="BCC Email" required readonly>
                        <span>Reply Email:</span>
                        <input type="text" name="reply_email" class="form-control" value="<?= $reply_email ?>" placeholder="Reply Email" required readonly>
                        <span>Subject:</span>
                        <input type="text" name="subject" class="form-control" value="<?= $subject ?>" placeholder="Subject" required>
                        <span>Body</span>
                        <?php wp_editor($content, $content_id, $content_settings); ?>
                        <input type="hidden" name="attachment" value="<?= $filename ?>" /><br/>
                        <button type="submit" name="send_email" value="1" class="btn btn-primary btn-block">Send Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}