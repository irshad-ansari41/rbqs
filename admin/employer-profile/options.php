<?php
/**
 * ILC Tabbed Settings Page
 */
add_action('init', 'employer_profile_init_init');

function employer_profile_init_init() {
    $options = get_option("rb_employer_options");
    if (empty($options)) {
        $options = [
            'rb_profile' => [],
            'rb_bank_account' => [],
            'rb_email' => [],
            'rb_pc_password' => [],
            'rb_tally' => [],
            'rb_other' => [],
            'rb_renewal' => [],
            'rb_vehicle' => [],
            'rb_reminder' => [],
        ];
        update_option("rb_employer_options", $options);
    }
}

function rb_load_employer_profile_page() {
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    $getdata['tab'] = !empty($getdata['tab']) ? $getdata['tab'] : 'rb_profile';

    if (!empty($postdata) && $postdata["employer-options-submit"] == 'Y') {
        //check_admin_referer("employer-options-page");
        rb_employer_profile_options();
        $url_parameters = isset($getdata['tab']) ? 'updated=true&tab=' . $getdata['tab'] : 'updated=true';
        wp_redirect(admin_url('admin.php?page=employer-options&' . $url_parameters));
        exit();
    }
}

function rb_employer_profile_options() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $rb_employer_options = get_option("rb_employer_options");
    if ($pagenow == 'admin.php' && $getdata['page'] == 'employer-options') {
        if (isset($getdata['tab'])) {
            $tab = $getdata['tab'];
        } else {
            $tab = 'rb_profile';
        }

        switch ($tab) {
            case 'rb_profile' :
                $rb_employer_options['rb_profile'] = $postdata['rb_profile'] ?? [];
                break;
            case 'rb_bank_account' :
                $rb_employer_options['rb_bank_account'] = array_filter(array_map('array_filter', $postdata['rb_bank_account'] ?? []));
                break;
            case 'rb_email' :
                $rb_employer_options['rb_email'] = array_filter(array_map('array_filter', $postdata['rb_email'] ?? []));
                break;
            case 'rb_pc_password' :
                $rb_employer_options['rb_pc_password'] = array_filter(array_map('array_filter', $postdata['rb_pc_password'] ?? []));
                break;
            case 'rb_tally' :
                $rb_employer_options['rb_tally'] = $postdata['rb_tally'] ?? [];
                break;
            case 'rb_other' :
                $rb_employer_options['rb_other'] = array_filter(array_map('array_filter', $postdata['rb_other']));
                break;
            case 'rb_renewal' :
                $rb_employer_options['rb_renewal'] = array_filter(array_map('array_filter', $postdata['rb_renewal']));
                break;
            case 'rb_vehicle' :
                $rb_employer_options['rb_vehicle'] = array_filter(array_map('array_filter', $postdata['rb_vehicle']));
                break;
            case 'rb_reminder' :
                $rb_employer_options['rb_reminder'] = $postdata['rb_reminder'] ?? [];
                break;
        }
    }

    update_option("rb_employer_options", $rb_employer_options);
}

function rb_employer_profile_tabs($current = 'rb_profile') {
    $tabs = [
        'rb_profile' => 'Employer Profile',
        'rb_bank_account' => 'Bank Accounts',
        'rb_email' => 'Emails',
        'rb_pc_password' => 'PC Passwords',
        'rb_tally' => 'Tally',
        'rb_other' => 'Others',
        'rb_renewal' => 'For Renewals',
        'rb_vehicle' => 'Vehicle Registry',
        'rb_reminder' => 'Reminder Days',
    ];
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=employer-options&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function rb_empoyer_profile_page() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $tab = !empty($getdata['tab']) ? $getdata['tab'] : '';
    $rb_employer_options = get_option('rb_employer_options', []);
    ?>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Employer Options</h1>
        <span id="enable-edit" class="page-title-action">Edit</span>
        <?php
        if (!empty($getdata['updated']) && 'true' == esc_attr($getdata['updated'])) {
            echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
        }

        if (isset($getdata['tab'])) {
            rb_employer_profile_tabs($getdata['tab']);
        } else {
            rb_employer_profile_tabs('rb_profile');
        }

        wp_nonce_field("employer-options-page");

        if ($pagenow == 'admin.php' && $getdata['page'] == 'employer-options') {

            if (isset($getdata['tab'])) {
                $tab = $getdata['tab'];
            } else {
                $tab = 'rb_profile';
            }
            ?>
            <div id="poststuff">
                <form method="post">
                    <?php
                    switch ($tab) {
                        case 'rb_profile' :
                            include_once 'employer-profile.php';
                            break;
                        case 'rb_bank_account' :
                            include_once 'bank-account.php';
                            break;
                        case 'rb_email' :
                            include_once 'email.php';
                            break;
                        case 'rb_pc_password' :
                            include_once 'pc-password.php';
                            break;
                        case 'rb_tally' :
                            include_once 'tally.php';
                            break;
                        case 'rb_other' :
                            include_once 'other.php';
                            break;
                        case 'rb_renewal' :
                            include_once 'renewal.php';
                            break;
                        case 'rb_vehicle' :
                            include_once 'vehicle.php';
                            break;
                        case 'rb_reminder' :
                            include_once 'reminder.php';
                            break;
                    }
                    ?>
                    <p class="submit" style="clear: both;">
                        <input type="submit" name="Submit"  class="button-primary" value="Save" />
                        <input type="hidden" name="employer-options-submit" value="Y" />
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
