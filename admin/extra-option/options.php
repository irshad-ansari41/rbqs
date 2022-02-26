<?php
/**
 * ILC Tabbed Settings Page
 */
add_action('init', 'app_admin_init');

function app_admin_init() {
    $options = get_option("rb_options");
    if (empty($options)) {
        $options = [
        ];
        update_option("rb_options", $options);
    }
}

function rb_load_options_page() {
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    $getdata['tab'] = !empty($getdata['tab']) ? $getdata['tab'] : 'db_backup';

    if (!empty($postdata) && $postdata["roche-bobois-submit"] == 'Y') {
        //check_admin_referer("roche-bobois-page");
        rb_save_admin_options();
        $url_parameters = isset($getdata['tab']) ? 'updated=true&tab=' . $getdata['tab'] : 'updated=true';
        wp_redirect(admin_url('admin.php?page=roche-bobois&' . $url_parameters));
        exit();
    }
}

function rb_save_admin_options() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $rb_options = get_option("rb_options");
    if ($pagenow == 'admin.php' && $getdata['page'] == 'roche-bobois') {
        if (isset($getdata['tab'])) {
            $tab = $getdata['tab'];
        } else {
            $tab = 'db_backup';
        }

        switch ($tab) {

            case 'db_backup' :
                break;
            case 'clear_cache' :
                break;
        }
    }

    update_option("rb_options", $rb_options);
}

function rb_admin_tabs($current = 'clear_cache') {
    $tabs = [
        'db_backup' => 'DB Backup',
        'clear_cache' => 'Clear Cache',
    ];
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=roche-bobois&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function rb_options_page() {
    global $pagenow;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $tab = !empty($getdata['tab']) ? $getdata['tab'] : '';
    if (!empty($postdata['clear_cache'])) {
        array_map('unlink', glob(CACHE_JSON_DIR . "/*.*"));
    }
    $rb_options = get_option('rb_options', []);
    ?>
    <div class="wrap">
        <h2>Roche Bobois Options</h2>
        <?php
        if (!empty($getdata['updated']) && 'true' == esc_attr($getdata['updated'])) {
            echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
        }

        if (isset($getdata['tab'])) {
            rb_admin_tabs($getdata['tab']);
        } else {
            rb_admin_tabs('rb_profile');
        }

        wp_nonce_field("roche-bobois-page");

        if ($pagenow == 'admin.php' && $getdata['page'] == 'roche-bobois') {

            if (isset($getdata['tab'])) {
                $tab = $getdata['tab'];
            } else {
                $tab = 'db_backup';
            }
            ?>
            <div id="poststuff">
                <form method="post">
                    <?php
                    switch ($tab) {

                        case 'db_backup' :
                            include_once 'db-backup.php';

                            break;
                        case 'clear_cache' :
                            ?>
                            <table class="form-table" style="border-collapse:collapse;">
                                <tr>
                                    <th><label for="clear_cache">Clear Cache:</label></th>
                                    <td>
                                        <label for="clear_cache"><input id="clear_cache" name="clear_cache" type="checkbox"  value="1" /> 
                                            <span class="description">Checked this to clear cache.</span></label>
                                    </td>
                                </tr>
                            </table>
                            <?php
                            break;
                    }
                    ?>
                    <p class="submit" style="clear: both;">
                        <input type="submit" name="Submit"  class="button-primary" value="Save" />
                        <input type="hidden" name="roche-bobois-submit" value="Y" />
                    </p>
                </form>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
