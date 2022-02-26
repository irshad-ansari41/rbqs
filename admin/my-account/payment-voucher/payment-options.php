<?php
/**
 * ILC Tabbed Settings Page
 */
add_action('init', 'payment_option_init_init');

function payment_option_init_init() {
    $options = get_option("rb_payment_options");
    if (empty($options)) {
        $options = [
            'rb_expense_type' => [],
            'rb_exchange_rate' => [],
        ];
        update_option("rb_payment_options", $options);
    }
}

function rb_load_payment_option_page() {
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    $getdata['tab'] = !empty($getdata['tab']) ? $getdata['tab'] : 'rb_expense_type';

    if (!empty($postdata) && $postdata["payment-options-submit"] == 'Y') {
        //check_admin_referer("payment-options-page");
        rb_payment_options();
        $url_parameters = isset($getdata['tab']) ? 'updated=true&tab=' . $getdata['tab'] : 'updated=true';
        wp_redirect(admin_url('admin.php?page=payment-options&' . $url_parameters));
        exit();
    }
}

function rb_payment_options() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $rb_payment_options = get_option("rb_payment_options");
    if ($pagenow == 'admin.php' && $getdata['page'] == 'payment-options') {
        if (isset($getdata['tab'])) {
            $tab = $getdata['tab'];
        } else {
            $tab = 'rb_expense_type';
        }

        switch ($tab) {
            case 'rb_expense_type' :
                $rb_payment_options['rb_expense_type'] = $postdata['rb_expense_type'] ?? [];
                break;
            case 'rb_exchange_rate' :
                $rb_payment_options['rb_exchange_rate'] = $postdata['rb_exchange_rate'] ?? [];
                break;
        }
    }

    update_option("rb_payment_options", $rb_payment_options);
}

function rb_payment_option_tabs($current = 'rb_expense_type') {
    $tabs = [
        'rb_expense_type' => 'Type of Expense',
        'rb_exchange_rate' => 'Exchange Rate',
    ];
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=payment-options&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function admin_ctm_payment_option_page() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $tab = !empty($getdata['tab']) ? $getdata['tab'] : '';
    $rb_payment_options = get_option('rb_payment_options', []);

    ?>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Payment Options</h1>
        <span id="enable-edit" class="page-title-action">Edit</span>
        <a id="add-new-client" href="<?= "admin.php?page=purchase-voucher" ?>" class="page-title-action btn-primary" >Back</a>
        <?php
        if (!empty($getdata['updated']) && 'true' == esc_attr($getdata['updated'])) {
            echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
        }

        if (isset($getdata['tab'])) {
            rb_payment_option_tabs($getdata['tab']);
        } else {
            rb_payment_option_tabs('rb_expense_type');
        }

        wp_nonce_field("payment-options-page");

        if ($pagenow == 'admin.php' && $getdata['page'] == 'payment-options') {

            if (isset($getdata['tab'])) {
                $tab = $getdata['tab'];
            } else {
                $tab = 'rb_expense_type';
            }
            ?>
            <div id="poststuff">
                <form method="post">
                    <?php
                    switch ($tab) {
                        case 'rb_expense_type' :
                            ?>
                            <table class="form-table" border="1" style="border-collapse: collapse">
                                <?php for ($i = 0; $i <= count($rb_payment_options['rb_expense_type']); $i++) { ?>
                                    <tr>
                                        <td>
                                            <input type="text" name="rb_expense_type[<?= $i ?>]" placeholder="Expense Type"
                                                   value="<?= $rb_payment_options['rb_expense_type'][$i] ?? '' ?>" style="width:100%" />
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <?php
                            break;
                        case 'rb_exchange_rate' :
                            ?>
                            <table class="form-table" border="1" style="border-collapse: collapse">
                                <tr>
                                    <td colspan="3"><strong>EURO</strong></td>
                                    <td>
                                        <input type="number" name="rb_exchange_rate[EURO]" step="0.00001" placeholder="Exchange Rate"
                                               value="<?= $rb_payment_options['rb_exchange_rate']['EURO'] ?? '' ?>" style="width:100%" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"><strong>USD</strong></td>
                                    <td>
                                        <input type="number" name="rb_exchange_rate[USD]" step="0.00001" placeholder="Exchange Rate"
                                               value="<?= $rb_payment_options['rb_exchange_rate']['USD'] ?? '' ?>" style="width:100%" />
                                    </td>
                                </tr>
                            </table>
                            <?php
                            break;
                    }
                    ?>
                    <p class="submit" style="clear: both;">
                        <input type="submit" name="Submit"  class="button-primary" value="Save" />
                        <input type="hidden" name="payment-options-submit" value="Y" />
                    </p>
                </form>
            </div>
            <?php
        }
        ?>
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
            jQuery("input,select,textarea").prop("readonly", true);
            jQuery("#enable-edit").click(() => {
                jQuery("input,select,textarea").prop("readonly", false);
            });
        });
    </script>
    <?php
}
