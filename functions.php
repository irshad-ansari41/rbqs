<?php

/**
 * Roche Bobois functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Roche_Bobois
 */
$getdata = filter_input_array(INPUT_GET);

$media_pages = ['rw-items',
    'receipt-create',
    'receipt-edit',
    'receipt-settle',
    'quotation-create',
    'quotation-edit',
    'project-job-order-add',
    'project-job-order-edit',
    'bank-deposit-edit',
    'payment-voucher-create',
    'payment-voucher-edit',
    'purchase-order-view',
    'employee-add',
    'employee-edit'
];
if (!empty($getdata['page']) && in_array($getdata['page'], $media_pages)) {
    add_action('admin_enqueue_scripts', 'wp_enqueue_media');
}

function minify_html() {
    ob_start('html_compress');
}

function html_compress($buffer) {

    $search = array(
        '/\n/', // replace end of line by a space
        '/\>[^\S]+/s', // strip whitespaces after tags, except space
        '/[^\S]+\</s', // strip whitespaces before tags, except space
        '/(\s)+/s', // shorten multiple whitespace sequences,
        '~<!--//(.*?)-->~s' //html comments
    );

    $replace = array(
        ' ',
        '>',
        '<',
        '\\1',
        ''
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}

if (get_option('html_minify')) {
    add_action('wp_loaded', 'minify_html');
}

/**
 * 
 * @param array $file
 * @return string
 */
function rename_file_name_timestamp($file) {
    $pathinfo = pathinfo($file['name']);
    $filename = $pathinfo['filename'];
    $timestamp = date('YmdHis');
    $file['name'] = $filename . '-' . $timestamp . '-original.' . $pathinfo['extension'];
    return $file;
}

add_filter('wp_handle_upload_prefilter', 'rename_file_name_timestamp');


add_action('admin_footer', function() {
    $last_db_backup = get_option('last_db_backup');
    if ($last_db_backup != date('Y-m-d-H')) {
        $db_backup = site_url('wp-content/themes/rb-theme/backup-mysql.php');
        echo "<script>setTimeout(function(){jQuery.get('$db_backup');},3000);</script>";
        update_option('last_db_backup', date('Y-m-d-H'));
        update_option('current_url', curr_url());
    }
});

/**
 * 
 * @global type $current_user
 * @global type $pagenow
 * @param type $wp_query_obj
 * @return type
 */
function rb_users_own_attachments($wp_query_obj) {

    global $current_user, $pagenow;

    $is_attachment_request = ($wp_query_obj->get('post_type') == 'attachment');

    if (!$is_attachment_request) {
        return;
    }
    if (!is_a($current_user, 'WP_User')) {
        return;
    }
    if (!in_array($pagenow, array('upload.php', 'admin-ajax.php'))) {
        return;
    }
    if (!current_user_can('administrator') && !current_user_can('editor') && !current_user_can('admin')) {
        $wp_query_obj->set('author', $current_user->ID);
    }

    return;
}

//add_action('pre_get_posts', 'rb_users_own_attachments');

/**
 * 
 * @param type $query
 * @return type
 */
function rb_show_current_user_attachments($query) {
    $user_id = get_current_user_id();
    if ($user_id && !current_user_can('administrator') && !current_user_can('editor') && !current_user_can('admin')) {
        $query['author'] = $user_id;
    }
    return $query;
}

add_filter('ajax_query_attachments_args', 'rb_show_current_user_attachments');


/**
 * 
 */

include 'lib/admin-functions.php';
