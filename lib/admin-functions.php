<?php
date_default_timezone_set("Asia/Dubai");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'constants.php';
include_once 'ExtraSetting.php';
include_once 'db-cache.php';
include_once 'myaccount-functions.php';
include_once 'stock-inventory-functions.php';
include_once 'hr-functions.php';
include_once 'report-functions.php';
include_once THEME_DIR . '/admin/admin-menu.php';

/**
 * 
 */
function app_output_buffer() {
    ob_start();
}

// soi_output_buffer
add_action('init', 'app_output_buffer');

/**
 * 
 * @param type $page
 * @param type $step
 * @param type $type
 * @param type $scope
 * @param type $vat
 * @return type
 */
function gen_quote_back_url($page, $step, $type = null, $scope = null, $vat = null) {
    return "admin.php?page={$page}&step=" . ($step - 1) . "&type={$type}&&scope={$scope}&&vat={$vat}";
}

/**
 * 
 */
function import_product_page() {
    include_once(ABSPATH . 'wp-content/themes/roche-bobois/import/import-product.php');
    import_product();
}

/**
 * 
 */
function admin_cutom_script_enqueue() {
    $load_minify = get_option('load_minify');
    if (!empty($load_minify)) {
        wp_enqueue_style('backend-css', get_template_directory_uri() . "/assets/dist/css/backend." . CSS_JS_VERSION . ".min.css");
        wp_enqueue_script('backend-js', get_template_directory_uri() . "/assets/dist/js/backend." . CSS_JS_VERSION . ".min.js", [], false, true);
    } else {
        wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/assets/css/bootstrap.css');
        wp_enqueue_style('admin-style-css', get_template_directory_uri() . '/assets/css/admin-style.css');
        wp_enqueue_style('chosen-css', get_template_directory_uri() . '/assets/css/chosen.css');

        wp_enqueue_script('popper-js', get_template_directory_uri() . '/assets/js/popper.min.js', [], false, true);
        wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.js', [], false, true);
        wp_enqueue_script('chosen-js', get_template_directory_uri() . '/assets/js/chosen.jquery.js', [], false, true);
    }
}

add_action('admin_enqueue_scripts', 'admin_cutom_script_enqueue');

/**
 * 
 * @param type $value
 * @return string
 */
function show_qtn_status($value) {

    if ($value->status == 'Draft') {
        $html = "<span class='badge badge-dark'>{$value->status}</span>";
    } else if ($value->status == 'Pending') {
        $html = "<span class='badge badge-warning'>{$value->status}</span>";
    } else if ($value->status == 'RESERVED') {
        $html = "<span class='badge badge-primary'>{$value->status}</span>";
    } else if ($value->status == 'CONFIRMED') {
        $html = "<span class='badge badge-danger'>{$value->status}</span>";
    } else if ($value->status == 'PURCHASED') {
        $html = "<span class='badge badge-primary' style='background: pink;'>{$value->status}</span>";
    } else if ($value->status == 'DELIVERED') {
        $html = "<span class='badge badge-success'>{$value->status}</span>";
    } else if ($value->status == 'Converted to PJO') {
        $html = "<span class='badge badge-primary' style='background: violet;'>{$value->status}</span>";
    }
    return $html;
}

/**
 * 
 * @param type $value
 * @return string
 */
function showroom_qtn_status($value) {
    $html = '';
    if ($value->status == 'Pending') {
        $html = "<span class='badge badge-dark'>Under Review</span>";
    } else if ($value->status == 'ORDERED') {
        $html = "<span class='badge badge-warning'>{$value->status}</span>";
    } else if ($value->status == 'RESERVED') {
        $html = "<span class='badge badge-primary'>{$value->status}</span>";
    } else if ($value->status == 'CONFIRMED') {
        $html = "<span class='badge badge-danger'>{$value->status}</span>";
    } else if ($value->status == 'PURCHASED') {
        $html = "<span class='badge badge-primary' style='background: pink;'>{$value->status}</span>";
    } else if ($value->status == 'DELIVERED') {
        $html = "<span class='badge badge-success'>{$value->status}</span>";
    } else if ($value->status == 'Converted to PJO') {
        $html = "<span class='badge badge-primary' style='background: violet;'>{$value->status}</span>";
    }
    return $html;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_client($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_clients WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_clients WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_sales_person($sp_id, $field = null) {
    global $wpdb;
    $user_id = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key='sales_person' AND meta_value='$sp_id'");
    $display_name = $wpdb->get_var("SELECT display_name FROM {$wpdb->prefix}users WHERE ID='{$user_id}' ");
    $result = ['display_name' => $display_name, 'sp_id' => $sp_id, 'user_id' => $user_id,
        'salary' => (int) get_user_meta($user_id, 'salary', true),
        'target' => (int) get_user_meta($user_id, 'target', true),
    ];
    if ($field) {
        return $result[$field];
    }
    return (object) $result;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_user($user_id, $field = null) {
    global $wpdb;
    $display_name = $wpdb->get_var("SELECT display_name FROM {$wpdb->prefix}users WHERE ID='{$user_id}' ");
    $employee_id = get_user_meta($user_id, 'employee_id', true);
    $sp_id = get_user_meta($user_id, 'sales_person', true);
    $salary = get_user_meta($user_id, 'salary', true);
    $target = get_user_meta($user_id, 'target', true);
    $achiever_90 = get_user_meta($user_id, 'achiever_90', true);
    $achiever_100 = get_user_meta($user_id, 'achiever_100', true);
    $additional_10 = get_user_meta($user_id, 'additional_10', true);
    $support_share = get_user_meta($user_id, 'support_share', true);
    $result = (object) ['user_id' => $user_id,
                'display_name' => $display_name,
                'name' => $display_name,
                'employee_id' => $employee_id ? $employee_id : 0,
                'sp_id' => $sp_id ? $sp_id : 0,
                'salary' => !empty($salary) ? $salary : 0,
                'target' => !empty($target) ? $target : 0,
                'achiever_90' => !empty($achiever_90) ? $achiever_90 : 0,
                'achiever_100' => !empty($achiever_100) ? $achiever_100 : 0,
                'additional_10' => !empty($additional_10) ? $additional_10 : 0,
                'support_share' => !empty($support_share) ? $support_share : 0,];
    return $field ? $result->$field : $result;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_employee($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_hr_employees WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_employees WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_location($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_locations WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_locations WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_country($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_country WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_country WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_item($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_items WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_item_by_entry($entry, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_items WHERE entry='$entry'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items WHERE id='$entry'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_item_category($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_item_category WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_item_category WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $code
 * @param type $field
 * @return type
 */
function get_supplier($sup_code, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_suppliers WHERE sup_code='$sup_code'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_suppliers WHERE sup_code='$sup_code'");
}

/**
 * 
 * @global type $wpdb
 * @param type $code
 * @param type $field
 * @return type
 */
function get_supplier_by_id($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_suppliers WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_suppliers WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_qtn_sales_person($qid) {
    global $wpdb;
    return $wpdb->get_var("SELECT sales_person FROM {$wpdb->prefix}ctm_quotations where  id='{$qid}'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_quotation($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_quotations WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_quotation_meta($id, $fields = null) {
    global $wpdb;
    $_fields = !empty($fields) ? implode(', ', $fields) : '*';
    if (count($fields) == 1) {
        return $wpdb->get_var("SELECT $_fields FROM {$wpdb->prefix}ctm_quotations_meta WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT $_fields FROM {$wpdb->prefix}ctm_quotations_meta WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_receipt($id, $fields = []) {
    global $wpdb;
    $_fields = !empty($fields) ? implode(', ', $fields) : '*';
    return $wpdb->get_row("SELECT $_fields FROM {$wpdb->prefix}ctm_receipts WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_payment_voucher($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_payment_vouchers WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_payment_vouchers WHERE id='$id'");
}

/**
 * 
 * @param type $data
 * @return string
 */
function wpdb_data_format($data) {
    for ($i = 1; $i <= count($data); $i++) {
        $r[] = '%s';
    }
    return $r;
}

/**
 * 
 * @param type $array
 * @return type
 */
function change_color_text($array) {
    $text = '';
    foreach ($array as $k => $v) {
        if ($k % 2 == 0) {
            $text .= "<span class='text-black'>$v</span>,";
        } else {
            $text .= "<span class='text-red'>$v</span>,";
        }
    }
    return $text;
}

/**
 * 
 * @global type $wpdb
 * @param type $quotation_id
 * @param type $postdata
 * @param type $date
 */
function save_quotation_meta($quotation_id, $postdata) {
    global $wpdb;
    $revised_no = get_revised_no($quotation_id);
    foreach ($postdata['items'] as $value) {
        $data = ['quotation_id' => $quotation_id, 'revised_no' => $revised_no, 'item_id' => $value['item_id'], 'po_meta_id' => $value['po_meta_id'], 'entry' => (!empty($value['entry']) ? $value['entry'] : ''), 'item_desc' => $value['desc'],
            'sup_code' => $value['sup_code'], 'price_incl_vat' => $value['price'], 'quantity' => $value['qty'], 'discount' => ($value['dis'] - $postdata['special_discount']), 'net_price' => $value['net'], 'vat' => $value['vat'], 'total_incl_vat' => $value['total'],];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations_meta", $data, wpdb_data_format($value));
    }
}

/**
 * Create cache directory
 */
function create_pdf_dir() {
    $old = umask(0);
    if (!is_dir(PDF_UPLOAD_PATH)) {
        mkdir(PDF_UPLOAD_PATH, 0755, true) || chmod(PDF_UPLOAD_PATH, 0755);
    }
    umask($old);
}

create_pdf_dir();

/**
 * Get the user's roles
 * @since 1.0.0
 */
function rb_get_current_user_roles() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $roles = (array) $user->roles;
        return $roles;
    } else {
        return array();
    }
}

/**
 * 
 * @global type $current_user
 * @param type $user
 */
function fb_add_custom_user_profile_fields($user) {
    global $current_user;
    if (has_this_role('accounts')) {
        ?>
        <h3><?php _e('Extra Profile Information', 'your_textdomain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="employee_id"><?php _e('Employee ID', 'your_textdomain'); ?></label></th><td>
                    <input type="text" name="employee_id" id="employee_id" value="<?php echo esc_attr(get_the_author_meta('employee_id', $user->ID)); ?>" class="regular-text" <?= in_array('sales', $current_user->roles) ? 'disabled' : '' ?> /><br />
                    <span class="description"><?php _e('Please enter Employee ID.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="sales_person"><?php _e('Sales Person ID', 'your_textdomain'); ?></label></th><td>
                    <input type="text" name="sales_person" id="sales_person" value="<?php echo esc_attr(get_the_author_meta('sales_person', $user->ID)); ?>" class="regular-text" <?= in_array('sales', $current_user->roles) ? 'disabled' : '' ?> /><br />
                    <span class="description"><?php _e('Please enter Sales Person ID.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="designation"><?php _e('Designation', 'your_textdomain'); ?></label></th><td>
                    <input type="text" name="designation" id="designation" value="<?php echo esc_attr(get_the_author_meta('designation', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description"><?php _e('Please enter Designation.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="salary"><?php _e('Salary', 'your_textdomain'); ?></label></th><td>
                    <input type="text" name="salary" id="salary" value="<?php echo esc_attr(get_the_author_meta('salary', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description"><?php _e('Please enter Salary.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="target"><?php _e('Target', 'your_textdomain'); ?></label></th><td>
                    <input type="text" name="target"  value="<?php echo esc_attr(get_the_author_meta('target', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description"><?php _e('Please enter Target.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="target"><?php _e('For 90% Achiever', 'your_textdomain'); ?></label></th><td>
                    Target * <input type="number" step="0.0001" name="achiever_90"  value="<?php echo esc_attr(get_the_author_meta('achiever_90', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description" style="padding-left: 60px;"><?php _e('Please enter bonus formula.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="target"><?php _e('For 100% Achiever', 'your_textdomain'); ?></label></th><td>
                    Target * <input type="number" step="0.0001" name="achiever_100"  value="<?php echo esc_attr(get_the_author_meta('achiever_100', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description" style="padding-left: 60px;"><?php _e('Please enter bonus formula.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="target"><?php _e('For each Additional 10%', 'your_textdomain'); ?></label></th><td>
                    <input type="number" step="0.01" name="additional_10"  value="<?php echo esc_attr(get_the_author_meta('additional_10', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description"><?php _e('Please enter bonus amount.', 'your_textdomain'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="target"><?php _e('Support\'s Share %', 'your_textdomain'); ?></label></th><td>
                    <input type="number" step="0.01" name="support_share" min='0' max="100" value="<?php echo esc_attr(get_the_author_meta('support_share', $user->ID)); ?>" class="regular-text" required /><br />
                    <span class="description"><?php _e('Please enter support share.', 'your_textdomain'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }
}

/**
 * 
 * @param type $user_id
 * @return boolean
 */
function fb_save_custom_user_profile_fields($user_id) {

    if (!current_user_can('edit_user', $user_id)) {
        return FALSE;
    }
    $postdata = filter_input_array(INPUT_POST);

    update_usermeta($user_id, 'employee_id', (!empty($postdata['employee_id']) ? $postdata['employee_id'] : 0));
    update_usermeta($user_id, 'sales_person', (!empty($postdata['sales_person']) ? $postdata['sales_person'] : 0));
    update_usermeta($user_id, 'designation', (!empty($postdata['designation']) ? $postdata['designation'] : ''));
    update_usermeta($user_id, 'salary', (!empty($postdata['salary']) ? $postdata['salary'] : 0));
    update_usermeta($user_id, 'target', (!empty($postdata['target']) ? $postdata['target'] : 0));
    update_usermeta($user_id, 'achiever_90', (!empty($postdata['achiever_90']) ? $postdata['achiever_90'] : 0));
    update_usermeta($user_id, 'achiever_100', (!empty($postdata['achiever_100']) ? $postdata['achiever_100'] : 0));
    update_usermeta($user_id, 'additional_10', (!empty($postdata['additional_10']) ? $postdata['additional_10'] : 0));
    update_usermeta($user_id, 'support_share', (!empty($postdata['support_share']) ? $postdata['support_share'] : 0));
}

add_action('show_user_profile', 'fb_add_custom_user_profile_fields');
add_action('edit_user_profile', 'fb_add_custom_user_profile_fields');

add_action('personal_options_update', 'fb_save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'fb_save_custom_user_profile_fields');

/**
 * 
 * @param type $html
 * @param type $file_name
 */
function generate_pdf($html, $file_name, $watermark = null, $landscape = []) {
    $pdf = filter_input(INPUT_GET, 'pdf');
    $hide_footer = filter_input(INPUT_GET, 'hide_footer');
    if (empty($pdf)) {
        return;
    }

    $data = $landscape ? ['mode' => 'utf-8', 'format' => 'A4-L', 'orientation' => 'L'] : [];
    require_once ABSPATH . 'vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($data);

    if ($watermark) {
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetWatermarkText($watermark);
    }

    $stylesheet = file_get_contents(get_template_directory() . '/assets/css/pdf-style.css');

    if (empty($hide_footer)) {
        $mpdf->SetHTMLFooter("<table style='width:100%'><tr><td style='font-size:12px;'>{PAGENO} of {nb}</td>"
                . "<td style='text-align:center;font-size:12px;'>" . COMPANY_ADDRESS . "</td>"
                . "<td style='text-align:right;font-size:12px;'>{DATE d/m/Y h:i a}</td></tr></table>");
    }
    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML('<!DOCTYPE html><head></head><body>' . $html . '</body></html>');
    if ($pdf == 1) {
        $mpdf->Output($file_name, \Mpdf\Output\Destination::FILE);
        wp_redirect(str_replace('&pdf=1', '', curr_url()));
    } else if ($pdf == 2) {
        ob_clean();
        $mpdf->Output();
        exit();
    }
}

function make_pdf_file_name($filename) {
    return ['path' => PDF_UPLOAD_PATH . str_replace([' ', '+', '-', '/',], ['_', '', '_', '_'], strtoupper($filename)),
        'url' => PDF_UPLOAD_URL . str_replace([' ', '+', '-', '/'], ['_', '', '_', '_'], strtoupper($filename))];
}

/**
 * 
 * @param type $html
 * @param type $file_name
 */
function download_pdf_url($pdf_path) {
    return str_replace(ABSPATH, site_url('/'), $pdf_path);
}

/**
 * 
 * @param type $html
 * @param type $file_name
 */
function pdf_exist($file_name) {
    if (file_exists($file_name) && !empty($file_name)) {
        return true;
    }
    return false;
}

/**
 * 
 * @global type $current_user
 * @param type $value
 * @return boolean
 */
function hide_edit($value) {
    global $current_user;

    if (in_array('admin', $current_user->roles) || in_array('administrator', $current_user->roles)) {
        return true;
    } else if ($value->status == 'Draft') {
        return true;
    } else if ($value->status == 'CONFIRMED') {
        return false;
    } else if ($value->status == 'PURCHASED') {
        return false;
    } else if ($value->status == 'RESERVED') {
        return false;
    }if (!empty($value->receipt_no)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 
 * @param type $to
 * @param type $subject
 * @param type $content
 * @param type $attchment
 * @return type
 */
function mail_to_link($to, $subject, $content, $attchment) {
    $sub = urlencode(strtoupper($subject));
    $attch = PDF_UPLOAD_URL . str_replace('.PDF', '.pdf', strtoupper($attchment));
    $cont = urlencode($content) . "<br/><br/><br/>$attch";
    return "mailto:$to?subject={$sub}&body={$cont}&attachment={$attch}";
}



if (get_option('load_phpmailer')) {
    add_action('phpmailer_init', 'set_phpmailer_details');
}

function onMailError($wp_error) {
    echo "<pre>";
    print_r($wp_error->errors);
    echo "</pre>";
}

add_action('wp_mail_failed', 'onMailError', 10, 1);

/**
 * 
 * @param type $wp_error
 */
function log_mailer_errors($wp_error) {
    $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
    $fp = fopen($fn, 'a');
    fputs($fp, "Mailer Error: " . $wp_error->get_error_message() . "\n");
    fclose($fp);
}

add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);

/**
 * 
 * @global type $current_user
 * @param type $data
 */
function rb_send_email($data) {
    global $current_user;
    $to_email = $data['to_email'];
    if (!empty($data['cc_email'])) {
        $emails = explode(',', $data['cc_email']);
        foreach ($emails as $email) {
            $headers[] = "Cc: {$email}";
        }
    }
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = "From: {$current_user->display_name} <{$current_user->user_email}>";
    $headers[] = "Bcc: {$current_user->display_name} <{$current_user->user_email}>";
    $headers[] = "Reply-To: {$current_user->display_name} <{$current_user->user_email}>";
    $subject = $data['subject'];
    $message = $data['content'];
    $attachments = $data['attachment'];

    $r = wp_mail($to_email, $subject, $message, $headers, $attachments);
    return $r;
}

/**
 * 
 * @param type $status
 */
function status_change_send_email($status) {
    if ($status == 'CONFIRMED') {
        
    } elseif ($status == 'RESERVED') {
        
    } elseif ($status == 'DELIVERED') {
        
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_qtn_paid_amount($qid) {
    global $wpdb;
    $amount = $wpdb->get_var("SELECT sum(paid_amount) FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$qid' AND receipt_type != 'Dummy'");
    return $amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_qtn_balance_amount($qid) {
    global $wpdb;
    $balance_amount = $wpdb->get_var("SELECT balance_amount FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$qid' ORDER BY id DESC LIMIT 1");
    return $balance_amount == '0.0' || $balance_amount == '0.00' ? 0 : $balance_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_qtn_total_amount($id) {
    global $wpdb;
    $amount = $wpdb->get_var("SELECT total_amount FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$id' ORDER BY id DESC limit 1");
    return $amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_revised_no($qid) {
    global $wpdb;
    $qtn = $wpdb->get_var("SELECT revised_no FROM {$wpdb->prefix}ctm_quotations WHERE id='$qid'");
    return !empty($qtn) ? $qtn : $qid;
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_qtn_type($qid) {
    global $wpdb;
    $type = $wpdb->get_var("SELECT type FROM {$wpdb->prefix}ctm_quotations WHERE id='$qid' OR revised_no='$qid'");
    return $type;
}

/**
 * 
 * @global type $wpdb
 * @param type $qtn
 * @return type
 */
function get_qtn_no($revised_no) {
    global $wpdb;
    $qid = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations WHERE revised_no='$revised_no'");
    return !empty($qid) ? $qid : $revised_no;
}

/**
 * 
 * @global type $current_user
 * @return boolean
 */
function has_role_super_and_admin() {
    global$current_user;

    if (in_array('admin', $current_user->roles)) {
        return true;
    }
    if (in_array('administrator', $current_user->roles)) {
        return true;
    }
    return false;
}

/**
 * 
 * @global type $current_user
 * @param type $role
 * @return boolean
 */
function has_this_role($role = null) {
    global $current_user;

    if (in_array('administrator', $current_user->roles)) {
        return true;
    }

    if (in_array('admin', $current_user->roles)) {
        return true;
    }

    if (in_array($role, $current_user->roles)) {
        return true;
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @param type $qtype
 */
function create_stock_delivery_note($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_dn WHERE quotation_id='{$qid}'");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='{$qid}'");

    if (empty($exist) && !empty($quotation)) {

        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'is_showroom' => $quotation->is_showroom, 'client_id' => $quotation->client_id, 'qtn_type' => $quotation->type, 'status' => 'Pending', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn", $data, wpdb_data_format($data));

        $dn_id = $wpdb->insert_id;

        $qtn_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' ORDER BY id ASC");
        foreach ($qtn_meta as $value) {
            $location = $wpdb->get_var("SELECT stk_inv_location FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id='{$value->po_meta_id}'");
            $data = ['dn_id' => $dn_id, 'qtn_meta_id' => $value->id, 'po_meta_id' => $value->po_meta_id, 'quotation_id' => $quotation->id, 'item_id' => $value->item_id, 'item_desc' => $value->item_desc, 'quantity' => $value->quantity, 'entry' => $value->entry, 'location' => $location, 'sup_code' => $value->sup_code,];
            $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn_meta", $data, wpdb_data_format($data));
        }
        stock_qtn_update_stock_inventroy($quotation->id, $quotation->client_id, 'RESERVED');
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @param type $qtype
 */
function create_project_delivery_note($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $dn_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_dn WHERE quotation_id='{$qid}'");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='{$qid}'");
    if (empty($dn_id) && !empty($quotation)) {

        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'is_showroom' => $quotation->is_showroom, 'client_id' => $quotation->client_id, 'qtn_type' => $quotation->type, 'status' => 'Pending', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn", $data, wpdb_data_format($data));

        $dn_id = $wpdb->insert_id;

        $dn_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where quotation_id='{$qid}' ORDER BY id ASC");
        foreach ($dn_meta as $value) {
            $data = ['dn_id' => $dn_id, 'qtn_meta_id' => $value->qtn_meta_id, 'quotation_id' => $quotation->id, 'item_id' => $value->item_id, 'item_desc' => $value->item_desc, 'quantity' => $value->quantity, 'entry' => $value->entry, 'sup_code' => $value->sup_code,];
            $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn_meta", $data, wpdb_data_format($data));
        }
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $city_id
 * @param type $type
 * @param type $scope
 * @param type $vat
 * @param type $promo_type
 * @return type
 */
function get_freight_charge($city_id, $type, $scope, $vat, $promo_type = null) {
    global $wpdb;
    $field = '';
    if ($type == 'Stock' && $scope == 'Local') {
        $field = 'local';
    } elseif ($type == 'Stock' && $scope == 'Export' && $vat == 'wovat') {
        $field = 'export';
    } elseif ($type == 'Order' && $scope == 'Local') {
        $field = 'local';
    } elseif ($type == 'Order' && $scope == 'Export' && $vat == 'wovat') {
        $field = 'export';
    } elseif ($type == 'Order' && $scope == 'Promotion' && $promo_type == 'Local') {
        $field = 'local';
    } elseif ($type == 'Order' && $scope == 'Promotion' && $promo_type == 'Export') {
        $field = 'export';
    }

    if (!empty($field)) {
        $freight_charge = $wpdb->get_var("SELECT {$field}_freight_charge FROM {$wpdb->prefix}ctm_locations WHERE id='{$city_id}' ");
    }
    return !empty($freight_charge) ? $freight_charge : 0;
}

/**
 * 
 * @global type $wpdb
 * @param type $city_id
 * @param type $type
 * @param type $scope
 * @param type $vat
 * @param type $promo_type
 * @return type
 */
function get_discount($city_id, $type, $scope, $vat, $promo_type = null) {
    global $wpdb;
    $field = '';
    if ($type == 'Stock' && $scope == 'Local') {
        $field = '';
    } elseif ($type == 'Stock' && $scope == 'Export' && $vat == 'wovat') {
        $field = '';
    } elseif ($type == 'Order' && $scope == 'Local') {
        $field = 'local';
    } elseif ($type == 'Order' && $scope == 'Export' && $vat == 'wovat') {
        $field = 'export';
    } elseif ($type == 'Order' && $scope == 'Promotion' && $promo_type == 'Local') {
        $field = 'local';
    } elseif ($type == 'Order' && $scope == 'Promotion' && $promo_type == 'Export') {
        $field = 'export';
    } elseif ($type == 'Stock' && $scope == 'Promotion' && $promo_type == 'Local') {
        $field = 'local';
    } elseif ($type == 'Stock' && $scope == 'Promotion' && $promo_type == 'Export') {
        $field = 'export';
    }

    if (!empty($field)) {
        $discount = $wpdb->get_var("SELECT {$field}_discount FROM {$wpdb->prefix}ctm_locations WHERE id='{$city_id}' ");
    }
    return !empty($discount) ? $discount : 0;
}

/**
 * 
 * @param type $time
 * @param type $format
 * @return type
 */
function rb_time($time, $format = 'h:i a') {
    if (!empty($time) && $time != '00:00') {
        return date_format(date_create($time), $format);
    } else if (!empty($time) && $time != '00:00') {
        return'';
    } else {
        return date('h:i a');
    }
}

/**
 * 
 * @param type $date
 * @param type $format
 * @return type
 */
function rb_date($date = null, $format = 'd-M-Y') {
    if ($date == 'now') {
        return date('d-m-Y');
    } elseif (!empty($date) && $date != '0000-00-00') {
        return date_format(date_create($date), $format);
    }
    return false;
}

/**
 * 
 * @param type $datetime
 * @param type $format
 * @return type
 */
function rb_datetime($datetime = null, $format = 'd-M-Y h:i a') {
    if ($datetime == 'now') {
        return date('d-m-Y h:i a');
    } else if (!empty($datetime) && $datetime != '0000-00-00 00:00:00') {
        return date_format(date_create($datetime), $format);
    }
    return false;
}

/**
 * 
 * @param type $date1
 * @param type $date2
 * @return type
 */
function rb_datediff($date1, $date2) {
    $diff = date_diff(date_create($date1), date_create($date2));
    return $diff;
}

function create_revised_quptation($qid, $postdata) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $fields = "`revised_no`, `client_id`, `type`, `scope`, `vat`, `city_id`, `country_id`, `sales_person`, `special_discount`, `freight_percent`, `freight_charge`, `total_amount`, `word`, `terms`, `notes`, `receipt_no`, `status`, `is_container_loading`, `cl_sequence_no`, `revised_id`, `delivery_note`, `project_job_order`, `order_registry`, `account_statement`, `logo`, `promo_type`, `is_showroom`, `trash`, `pdf_path`, `pdf_url`, `created_by`, `updated_by`, `created_at`, `updated_at`";

    $sql = "INSERT INTO {$wpdb->prefix}ctm_quotations ($fields) SELECT $fields  FROM {$wpdb->prefix}ctm_quotations where id='$qid'";

    $wpdb->query($sql);

    $id = $wpdb->insert_id;

    $data = ['word' => $postdata['word'], 'sales_person' => $postdata['sales_person'], 'revised_id' => $postdata['revised_id'], 'special_discount' => $postdata['special_discount'], 'total_amount' => $postdata['total_amount'], 'terms' => $postdata['terms'], 'notes' => $postdata['notes'], 'logo' => $postdata['logo'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

    $wpdb->update("{$wpdb->prefix}ctm_quotations", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);

    save_quotation_meta($id, $postdata);

    $count = $wpdb->get_var("select count(revised_id) FROM {$wpdb->prefix}ctm_quotations WHERE revised_id='{$postdata['revised_id']}'");
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET revised_no='{$postdata['revised_id']}-{$count}' WHERE id = $id");
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @param type $receipt_no
 */
function create_confirm_order($qid, $receipt_no = null) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_co where quotation_id='{$qid}'");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");
    if (empty($exist) && !empty($quotation)) {

        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'client_id' => $quotation->client_id, 'is_showroom' => $quotation->is_showroom, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_quotation_co", $data, wpdb_data_format($data));

        $co_id = $wpdb->insert_id;

        $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' ORDER BY id ASC");
        foreach ($quotation_meta as $value) {
            if (!in_array(trim($value->sup_code), ['RB', 'TSAR'])) {
                $data = ['co_id' => $co_id, 'qtn_meta_id' => $value->id, 'item_id' => $value->item_id, 'sup_code' => $value->sup_code, 'item_desc' => $value->item_desc, 'entry' => $value->entry, 'quantity' => $value->quantity,];
                $wpdb->insert("{$wpdb->prefix}ctm_quotation_co_meta", $data, wpdb_data_format($data));
                $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET stk_inv_status='RESERVED' WHERE id='$value->po_meta_id'");
            }
        }
    }
    if (!empty($receipt_no)) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET receipt_no='{$receipt_no}',status='CONFIRMED', updated_at= '{$date}' WHERE id='{$qid}'");
    }
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $quotation_id
 * @param type $revised_no
 * @param type $client_id
 * @param type $co_meta_id
 * @param type $entry
 * @return boolean
 */
function reserve_stock_in_cop($quotation_id, $revised_no, $client_id, $co_meta_id, $entry) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $po = $wpdb->get_row("SELECT id,entry FROM {$wpdb->prefix}ctm_quotation_po_meta where entry='{$entry}'");
    $po_meta_id = !empty($po->id) ? $po->id : 0;
    $po_entry = !empty($po->entry) ? $po->entry : 0;

    if (!empty($quotation_id) && !empty($client_id) && !empty($co_meta_id) && !empty($po_entry) && !empty($po_meta_id)) {

        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co_meta SET entry='{$entry}', po_meta_id='{$po_meta_id}', updated_by='{$current_user->ID}', updated_at='{$date}' where id='{$co_meta_id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET stk_inv_status='RESERVED', quotation_id='{$quotation_id}', revised_no='{$revised_no}',client_id='{$client_id}', updated_by='{$current_user->ID}', updated_at='{$date}' where id='{$po_meta_id}'");
        return true;
    }
    return false;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 */
function create_purchase_order($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $co = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_co where quotation_id='{$qid}' ORDER BY id ASC");
    $co_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_co_meta where co_id='{$co->id}' and po_meta_id=0 GROUP BY sup_code");

    foreach ($co_meta as $value) {
        $data = ['quotation_id' => $co->quotation_id, 'revised_no' => $co->revised_no, 'client_id' => $co->client_id, 'sup_code' => $value->sup_code, 'is_showroom' => $co->is_showroom, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_po", $data, wpdb_data_format($data));

        $po_id = $wpdb->insert_id;

        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co_meta SET po_id='{$po_id}', po_date='{$date}' where co_id='{$value->co_id}' AND sup_code='{$value->sup_code}'");

        $meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_co_meta where co_id='{$co->id}' AND sup_code='{$value->sup_code}' ORDER BY id ASC");
        foreach ($meta as $v) {
            $data = ['po_id' => $po_id, 'qtn_meta_id' => $value->qtn_meta_id, 'quotation_id' => $co->quotation_id, 'revised_no' => $co->revised_no, 'client_id' => $co->client_id, 'sup_code' => $v->sup_code, 'item_id' => $v->item_id, 'item_desc' => $v->item_desc, 'quantity' => $v->quantity, 'entry' => $v->entry, 'is_showroom' => $co->is_showroom, 'po_date' => $date, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            $wpdb->insert("{$wpdb->prefix}ctm_quotation_po_meta", $data, wpdb_data_format($data));
        }
    }
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='PURCHASED', updated_at='{$date}' where id='{$co->quotation_id}'");
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co SET status='ORDERED', updated_at='{$date}' where id='{$co->id}'");
}

/**
 * 
 * @global type $wpdb
 * @param type $quotation_id
 * @return string
 */
function show_purchase_order_status($status) {

    if ($status == 'ORDERED') {
        return "<span class='badge badge-primary' style='background: black;'>ORDERED</span>";
    } elseif ($status == 'CONFIRMED') {
        return "<span class='badge badge-danger'>CONFIRMED</span>";
    } elseif ($status == 'DELIVERED TO FF') {
        return "<span class='badge badge-primary' style='background: blue;'>DELIVERED TO FF</span>";
    } elseif ($status == 'ARRIVED') {
        return "<span class='badge badge-primary' style='background: green;'>ARRIVED</span>";
    } elseif ($status == 'TRANSIT') {
        return "<span class='badge badge-primary' style='background: pink;'>TRANSIT</span>";
    } elseif ($status == 'CANCELLED') {
        return "<span class='badge badge-seconday' style='color:#000;'>CANCELLED</span>";
    } else {
        return "<span class='badge badge-warning'>Pending</span>";
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $quotation_id
 * @return string
 */
function set_purchase_order_status($id) {
    global $wpdb;
    $results = $wpdb->get_results("SELECT order_registry FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE po_id='{$id}' GROUP BY order_registry");
    if (count($results) == 1) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po SET status='{$results[0]->order_registry}' WHERE id='{$id}'");
    }
}

/**
 * 
 * @param type $status
 * @return string
 */
function show_order_tracker_status($status) {

    if ($status == 'ORDERED') {
        return "<span class='badge badge-primary' style='background: black;'>ORDERED</span>";
    } elseif ($status == 'CONFIRMED') {
        return "<span class='badge badge-danger'>CONFIRMED</span>";
    } elseif ($status == 'DELIVERED TO FF') {
        return "<span class='badge badge-primary' style='background: blue;'>DELIVERED TO FF</span>";
    } elseif ($status == 'ARRIVED') {
        return "<span class='badge badge-primary' style='background: green;'>ARRIVED</span>";
    } elseif ($status == 'TRANSIT') {
        return "<span class='badge badge-primary' style='background: pink;'>TRANSIT</span>";
    } elseif ($status == 'CANCELLED') {
        return "<span class='badge badge-warning'>CANCELLED</span>";
    } else {
        return "<span class='badge badge-warning'>Pending</span>";
    }
}

/**
 * 
 * @param type $status
 * @return string
 */
function show_stock_status($status) {

    if ($status == 'AVAILABLE') {
        return "<span class='badge badge-primary' style='background: black;'>AVAILABLE</span>";
    } elseif ($status == 'RESERVED') {
        return "<span class='badge badge-danger'>RESERVED</span>";
    } elseif ($status == 'DAMAGED') {
        return "<span class='badge badge-primary' style='background: blue;'>DAMAGED</span>";
    } elseif ($status == 'DELIVERED') {
        return "<span class='badge badge-primary' style='background: green;'>DELIVERED</span>";
    } elseif ($status == 'USED') {
        return "<span class='badge badge-primary' style='background: pink;'>USED</span>";
    } else {
        return "<span class='badge badge-warning'></span>";
    }
}

/**
 * 
 * @param type $status
 * @return string
 */
function show_dn_status($status) {

    if ($status == 'DELIVERED') {
        return "<span class='badge badge-success'>DELIVERED</span>";
    } elseif ($status == 'CANCELLED') {
        return "<span class='badge badge-warning'>CANCELLED</span>";
    } elseif ($status == 'RETURNED') {
        return "<span class='badge badge-danger'>RETURNED</span>";
    } else {
        return "<span class='badge badge-dark'>Pending</span>";
    }
}

/**
 * 
 * @param type $status
 * @return string
 */
function show_order_arrival_status($status) {

    if ($status == 'Completed') {
        return "<span class='badge badge-success'>Complete</span>";
    } elseif ($status == 'Partial') {
        return "<span class='badge badge-warning'>Partial</span>";
    } else {
        return "<span class='badge badge-dark'>Pending</span>";
    }
}

/**
 * 
 * @return type
 */
function admin_default_page() {
    return admin_url('admin.php?page=daily-schedule');
}

add_filter('login_redirect', 'admin_default_page');

/**
 * 
 */
function aquila_login_load_style() {
    wp_enqueue_style('login-css', get_template_directory_uri() . '/assets/css/login.css');
}

add_action('login_enqueue_scripts', 'aquila_login_load_style');

/**
 * 
 * @global type $wp
 * @param type $query_string
 * @return type
 */
function curr_url($query_string = null) {
    global $wp;
    $getdata = filter_input_array(INPUT_GET);
    if (!empty($getdata)) {
        return $current_url = admin_url(add_query_arg(array($getdata), 'admin.php' . $wp->request)) . "&{$query_string}";
    } else {
        return admin_url($wp->request);
    }
}

function wpdb_query_error() {
    global $wpdb;
    echo '<pre>';
    print_r($wpdb->last_error);
    print_r($wpdb->last_query);
    print_r($wpdb->last_result);
    echo '</pre>';
    die;
}

/**
 * 
 * @global type $wpdb
 * @param type $quotation_id
 * @param type $postdata
 * @param type $date
 */
function save_sh_order_meta($quotation_id, $postdata) {
    global $wpdb;
    foreach ($postdata['items'] as $value) {
        $data = ['quotation_id' => $quotation_id, 'item_id' => $value['id'], 'hs_code' => $value['hs_code'], 'item_desc' => $value['desc'],
            'sup_code' => $value['code'], 'quantity' => $value['qty'], 'is_showroom' => 1,];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations_meta", $data, wpdb_data_format($value));
    }
}

/**
 * 
 * @param type $filename
 * @return type
 */
function export_excel_report($filename, $option_key, $table) {
    update_option($option_key, $table);
    $excel = str_ireplace([' ', '+', '-', '.pdf'], ['_', '', '_', '.xls'], strtoupper($filename));
    $pathinfo = pathinfo($excel);
    return get_template_directory_uri() . "/export/export-report.php?file_name={$pathinfo['basename']}&option_key={$option_key}";
}

/**
 * 
 * @param type $delivery_time_from
 */
function get_schedule_from($field_name, $delivery_time_from = null) {
    foreach (DELIVERY_TIME_FROM as $key => $value) {
        $checked = $value == $delivery_time_from ? 'checked' : '';
        $disabled = $key < get_all_schedule() && empty($delivery_time_from) ? 'disabled' : '';
        if ($checked == 'checked' && $disabled == 'disabled') {
            $disabled = '';
        }
        echo "<label><input type='radio' name='{$field_name}' class='form-control' required='required' $checked  value='{$value}' />{$value}</label>&nbsp;&nbsp;";
    }
}

/**
 * 
 * @param type $delivery_time_to
 */
function get_schedule_to($field_name, $delivery_time_to = null) {
    foreach (DELIVERY_TIME_TO as $key => $value) {
        $checked = $value == $delivery_time_to ? 'checked' : '';
        $disabled = $key <= get_all_schedule() && empty($delivery_time_to) ? 'disabled' : '';
        if ($checked == 'checked' && $disabled == 'disabled') {
            $disabled = '';
        }
        echo "<label><input type='radio' name='{$field_name}' class='form-control' required='required' $checked  value='{$value}' />{$value}</label>&nbsp;&nbsp;";
    }
}

/**
 * 
 * @global type $wpdb
 * @return type
 */
function get_all_schedule() {
    global $wpdb;
    $slot = [0, 1];
    $results = $wpdb->get_results("SELECT id,delivery_time_from,delivery_time_to from {$wpdb->prefix}ctm_quotation_dn where delivery_date='" . date('Y-m-d') . "'");
    foreach ($results as $value) {
        $slot[] = date_format(date_create($value->delivery_time_from), 'H');
        $slot[] = date_format(date_create($value->delivery_time_to), 'H');
    }
    return max($slot);
}

/**
 * 
 * @global type $wpdb
 * @return type
 */
function get_schedule_slots($date = null) {
    global $wpdb;
    $delivery_date = !empty($date) ? $date : date('Y-m-d');
    $results = $wpdb->get_results("SELECT id,delivery_time_from,delivery_time_to from {$wpdb->prefix}ctm_quotation_dn where delivery_date='{$delivery_date}'");
    return $results;
}

/**
 * 
 * @global type $wpdb
 * @param type $quotation_id
 * @param type $postdata
 * @param type $date
 */
function save_pjo_meta($pjo_id, $postdata) {
    global $wpdb;
    foreach ($postdata['items'] as $value) {
        $data = ['pjo_id' => $pjo_id, 'item_id' => $value['item_id'], 'item_desc' => $value['desc'], 'action' => $value['action'], 'image' => $value['image'],
            'quantity' => $value['qty'], 'responsibility' => $value['responsibility'], 'start_date' => $value['start_date'], 'start_time' => $value['start_time'],
            'end_date' => $value['end_date'], 'end_time' => $value['end_time']];
        $wpdb->insert("{$wpdb->prefix}ctm_project_job_order_meta", $data, wpdb_data_format($data));
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $table
 * @param type $id
 * @param type $file_name
 * @param type $has_pdf
 */
function store_pdf_path($table, $id, $file_name, $has_pdf) {
    global $wpdb;
    if (!empty($id) && $has_pdf != $file_name) {
        $pdf = make_pdf_file_name($file_name);
        $wpdb->update("{$wpdb->prefix}$table", ['pdf_path' => $pdf['path'], 'pdf_url' => $pdf['url']], ['id' => $id], ['%s', '%s'], ['%d']);
    }
}

/**
 * 
 * @param type $source
 * @param type $destination
 */
function pdf_copy($source, $destination) {
    if (pdf_exist($source)) {
        $pathinfo = pathinfo($source);
        copy($source, $destination . $pathinfo['basename']);
    }
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @param type $client_id
 * @return type
 */
function set_daily_schedule($ds_type_id, $ds_timestamp) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $ds_id = check_daily_schedule($ds_type_id, $ds_timestamp);
    if (empty($ds_id)) {
        $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_daily_schedule SET ds_type_id='{$ds_type_id}', ds_timestamp='{$ds_timestamp}',created_by ='{$current_user->ID}', updated_by ='{$current_user->ID}',created_at ='{$date}', updated_at ='{$date}'");
        $ds_id = $wpdb->insert_id;
    }
    return get_daily_schedule($ds_id);
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @return type
 */
function check_daily_schedule($ds_type_id, $ds_timestamp) {
    global $wpdb;
    $id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_daily_schedule WHERE ds_type_id='{$ds_type_id}' AND ds_timestamp='{$ds_timestamp}'");
    return !empty($id) ? $id : false;
}

/**
 * 
 * @global type $wpdb
 * @param type $ds_id
 * @return type
 */
function get_daily_schedule($ds_id) {
    global $wpdb;
    $row = $wpdb->get_row("SELECT id,note FROM {$wpdb->prefix}ctm_daily_schedule WHERE id='{$ds_id}'");
    return !empty($row) ? $row : ['id' => '', 'note' => ''];
}

/**
 * 
 * @param type $id
 * @return type
 */
function get_image_src($id) {
    $image = !empty($id) ? wp_get_attachment_url($id) : '';
    $image_src = !empty($image) ? $image : get_template_directory_uri() . '/assets/images/white.png';
    return $image_src;
}

/**
 * 
 * @param type $value
 * @return type
 */
function show_pjo_status($value) {
    if ($value->status == 'Draft') {
        $status = "<span class='badge badge-dark'>{$value->status}</span>";
    } else if ($value->status == 'Pending') {
        $status = "<span class='badge badge-primary'>{$value->status}</span>";
    } else if ($value->status == 'Reschedule') {
        $status = "<span class='badge badge-warning'>{$value->status}</span>";
    } else if ($value->status == 'Successful') {
        $status = "<span class='badge badge-success'>$value->status</span>";
    }
    return $status;
}

/**
 * 
 * @param type $value
 * @return type
 */
function show_qcr_status($value) {
    if ($value->status == 'Draft') {
        $status = "<span class='badge badge-dark'>{$value->status}</span>";
    } else if ($value->status == 'Pending') {
        $status = "<span class='badge badge-primary'>{$value->status}</span>";
    } else if ($value->status == 'Resolved') {
        $status = "<span class='badge badge-warning'>{$value->status}</span>";
    } else if ($value->status == 'Proceed to Order') {
        $status = "<span class='badge badge-success'>$value->status</span>";
    }
    return $status;
}

/**
 * 
 * @global type $wpdb
 * @param type $postdata
 */
function update_dn_meta_location($postdata) {
    global $wpdb;
    foreach ($postdata['dn'] as $key => $value) {
        $export_packing = !empty($value['export_packing']) ? $value['export_packing'] : 0;
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_dn_meta SET location='{$value['location']}', export_packing='{$export_packing}' WHERE id='{$key}'");
    }
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function convert_project_to_pjp($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_project_job_order where quotation_id='{$qid}'");

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");
    if (empty($exist)) {

        $client_id = get_client($quotation->client_id);
        $data = ['client_id' => $quotation->client_id, 'quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'qtn_type' => 'Project', 'contact_no' => $client_id->phone,
            'requested_by' => $current_user->display_name, 'address' => $client_id->address, 'status' => 'Draft', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_project_job_order", $data, wpdb_data_format($data));
        $pjo_id = $wpdb->insert_id;

        $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' ORDER BY id ASC");
        foreach ($quotation_meta as $value) {
            get_item($value->item_id, 'image');
            $data = ['pjo_id' => $pjo_id, 'qtn_meta_id' => $value->id, 'item_id' => $value->item_id, 'item_desc' => $value->item_desc, 'image' => get_item($value->item_id, 'image'),
                'quantity' => $value->quantity];
            $wpdb->insert("{$wpdb->prefix}ctm_project_job_order_meta", $data, wpdb_data_format($data));
        }

        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='Converted to PJO' where id='{$qid}'");
    } else {
        $wpdb->update("{$wpdb->prefix}ctm_project_job_order", ['revised_no' => $quotation->revised_no], ['id' => $exist], ['%s'], ['%d']);
        $pjo_meta_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_project_job_order_meta where pjo_id='{$exist}' ORDER BY item_id ASC");
        $j = 1;
        foreach ($pjo_meta_items as $value) {
            $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' AND item_id='{$value->item_id}'");
            $qtn_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' AND item_id='{$value->item_id}' LIMIT " . (!empty($count) ? $count - $j : 0) . ",1");
            if (!empty($qtn_meta_id)) {
                $wpdb->query("UPDATE {$wpdb->prefix}ctm_project_job_order_meta SET qtn_meta_id = $qtn_meta_id where id='{$value->id}'");
            }
            if ($count > $j) {
                $j++;
            } else {
                $j = 1;
            }
        }
    }
    return true;
}

/**
 * 
 * @param type $_num
 * @return string
 */
function num_to_words($_num) {
    $decones = array('01' => "One", '02' => "Two", '03' => "Three", '04' => "Four", '05' => "Five", '06' => "Six", '07' => "Seven", '08' => "Eight", '09' => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen");

    $ones = array(0 => "", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen");

    $tens = array(0 => "", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety");

    $hundreds = array("Hundred", "Thousand", "Million", "Billion", "Trillion", "Quadrillion"); //limit t quadrillion

    $num = number_format($_num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {
        if ($i < 20) {
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            $rettxt .= $tens[substr($i, 0, 1)];
            $rettxt .= " " . $ones[substr($i, 1, 1)];
        } else {
            if ($key == 0) {
                $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                if (substr($i, 1, 1) == 1) {
                    $rettxt .= " " . $ones[substr($i, 1, 2)];
                } else {
                    $rettxt .= " " . $tens[substr($i, 1, 1)];
                    $rettxt .= " " . $ones[substr($i, 2, 1)];
                }
            } else {
                $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                $rettxt .= " " . $tens[substr($i, 1, 1)];
                $rettxt .= " " . $ones[substr($i, 2, 1)];
            }
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }
    $rettxt = $rettxt . ""; // AED

    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            $rettxt .= $decones[$decnum];
        } elseif ($decnum < 100) {
            $rettxt .= $tens[substr($decnum, 0, 1)];
            $rettxt .= " " . $ones[substr($decnum, 1, 1)];
        }
        $rettxt = $rettxt . " Fils";
    } else {
        $rettxt = $rettxt . "and Zero Fils";
    }
    return $rettxt;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_po($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_quotation_po WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_po_meta_data($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id='$id'");
}

/**
 * 
 * @param type $value
 * @return type
 */
function rb_float($value, $output = null) {
    if ($output) {
        return $value == '0.0' || $value == '0.00' ? '-' : number_format($value, 2);
    }
    return $value == '0.0' || $value == 0 || $value == '0.00' ? 0 : number_format($value, 2);
}

/**
 * 
 * @global type $wpdb
 * @param type $po_meta_id
 * @return type
 */
function get_stock_inventory_items($po_meta_id = null) {
    global $wpdb;
    $flagship = FLAGSHIP_ID;
    $client_id = has_role_super_and_admin() ? 1 : "client_id='{$flagship}'";
    if ($po_meta_id) {
        $row = $wpdb->get_row("SELECT t1.item_desc as description, t1.quantity, t1.id as po_meta_id,  t1.entry, t2.id,  t2.collection_name, t2.sup_code, t2.hs_code "
                . "FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id"
                . " WHERE (t1.stk_inv_status='AVAILABLE' OR order_registry IN ('DELIVERED TO FF','TRANSIT','ARRIVED')) AND t1.id='{$po_meta_id}'");
        return $row;
    }
    $sql = "SELECT t1.id as po_meta_id, t1.item_desc as description, t1.entry, t1.quantity,  "
            . "t2.id as id, CONCAT(COALESCE(t2.collection_name,''),'|',COALESCE(t1.entry,''),'|',COALESCE(t1.stk_inv_location,'')) AS collection_name, t2.sup_code, t2.hs_code  "
            . "FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id"
            . " WHERE (t1.stk_inv_status='AVAILABLE' OR order_registry IN ('DELIVERED TO FF','TRANSIT','ARRIVED')) AND $client_id AND t1.quantity>0 AND t1.entry!='' ORDER BY t2.collection_name ASC";
    $results = $wpdb->get_results($sql);
    return $results;
}

/**
 * 
 * @global type $wpdb
 * @param type $po_meta_id
 * @param type $field
 * @return type
 */
function get_qcr_by_po_meta_id($po_meta_id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_quality_control_report WHERE po_meta_id='$po_meta_id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quality_control_report WHERE po_meta_id='$po_meta_id'");
}

/**
 * 
 * @param type $amount
 * @return type
 */
function get_ex_vat_amount($amount) {
    $ex_vat_amount = $amount / 1.05;
    return $ex_vat_amount;
}

/**
 * 
 * @param type $amount
 * @return type
 */
function get_vat_ex_amount($amount) {
    $vat_ex_amount = $amount / 1.05 * .05;
    return $vat_ex_amount;
}

/**
 * 
 * @param type $amount
 * @return type
 */
function get_vat_amount($amount) {
    $vat_amount = $amount * .05;
    return $vat_amount;
}

function make_entry_bold($entry) {
    $arr = explode('/', $entry);
    if (!empty($arr[1])) {
        return "<strong>{$arr[0]}</strong>/{$arr[1]}";
    } else {
        return "<strong>$entry</strong>";
    }
}

function get_main_entry($entry) {
    $_entry = strstr($entry, '/', true);
    return $_entry ? $_entry : $entry;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @param type $client_id
 * @return type
 */
function set_upcoming_renewal($ur_type_id, $ur_timestamp) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $ur_id = check_upcoming_renewal($ur_type_id, $ur_timestamp);
    if (empty($ur_id)) {
        $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_upcoming_renewals SET ur_type_id='{$ur_type_id}', ur_timestamp='{$ur_timestamp}',created_by ='{$current_user->ID}', updated_by ='{$current_user->ID}',created_at ='{$date}', updated_at ='{$date}'");
        $ur_id = $wpdb->insert_id;
    }
    return get_upcoming_renewal($ur_id);
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @return type
 */
function check_upcoming_renewal($ur_type_id, $ur_timestamp) {
    global $wpdb;
    $id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_upcoming_renewals WHERE ur_type_id='{$ur_type_id}' AND ur_timestamp='{$ur_timestamp}'");
    return !empty($id) ? $id : false;
}

/**
 * 
 * @global type $wpdb
 * @param type $ds_id
 * @return type
 */
function get_upcoming_renewal($ur_id) {
    global $wpdb;
    $row = $wpdb->get_row("SELECT id,status,note FROM {$wpdb->prefix}ctm_upcoming_renewals WHERE id='{$ur_id}'");
    return !empty($row) ? $row : ['id' => '', 'note' => ''];
}

/**
 * 
 * @param type $number
 * @param type $text
 * @return type
 */
function send_in_whatsapp($number, $text) {
    $url = "https://api.whatsapp.com/send?phone={$number}&text=" . urlencode($text);
    return $url;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_purchase_voucher($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $entry
 * @param type $fields
 * @return type
 */
function get_first_entry_details($entry, $fields = []) {
    global $wpdb;
    $_fields = !empty($fields) ? implode(', ', $fields) : '*';
    $_entry = strstr($entry, '/', true);
    $new_entry = $_entry ? $_entry . '/' : $entry;
    $where = $_entry ? "entry like '{$new_entry}%'" : "entry = '{$new_entry}'";
    $sql = "SELECT $_fields FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE $where ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC limit 2";
    $results = $wpdb->get_results($sql);
    return $results[0];
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $order_id
 * @param type $reorder_id
 * @param type $quantity
 * @param type $order_type
 */
function create_showroom_order($order_id, $reorder_id, $quantity, $order_type) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $sp_id = get_user($current_user->ID, 'sp_id');
    $data = (array) get_stock_inventory_items($reorder_id);
    $_order_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations WHERE status='Pending' AND is_showroom=1  ORDER BY id DESC");
    if ($order_type == 'New') {
        $qtndata = ['sales_person' => $sp_id, 'is_showroom' => 1, 'client_id' => FLAGSHIP_ID, 'status' => 'Pending', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations", $qtndata, wpdb_data_format($qtndata));
        $order_id = $wpdb->insert_id;
        $meta_data = ['quotation_id' => $order_id, 'item_id' => $data['id'], 'po_meta_id' => $data['po_meta_id'], 'entry' => $data['entry'], 'item_desc' => $data['description'], 'sup_code' => $data['sup_code'], 'hs_code' => $data['hs_code'], 'quantity' => $quantity,];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations_meta", $meta_data, wpdb_data_format($meta_data));
    } else {
        $order_id = $order_type == 'Existing' ? $order_id : $_order_id;
        $meta_data = ['quotation_id' => $order_id, 'item_id' => $data['id'], 'po_meta_id' => $data['po_meta_id'], 'entry' => $data['entry'], 'item_desc' => $data['description'], 'sup_code' => $data['sup_code'], 'hs_code' => $data['hs_code'], 'quantity' => $quantity,];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations_meta", $meta_data, wpdb_data_format($meta_data));
    }
}
