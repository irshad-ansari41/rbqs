<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtraSetting
 *
 * @author Irshad
 */
class ExtraSetting {

    //put your code here

    public function __construct() {
        add_action('admin_init', array($this, 'register_extra_options_settings'));
    }

    public function register_extra_options_settings() {
        add_settings_section('my_settings_section', __('Extra Options'), array($this, 'my_settings_section_callback'), 'general');

        add_settings_field('load_minify', 'Load Minify', array($this, 'load_minify_callback'), 'general', 'my_settings_section', array('load_minify'));
        register_setting('general', 'load_minify', 'esc_attr');

        add_settings_field('load_phpmailer', 'Enable PHP Mailer', array($this, 'load_phpmailer_callback'), 'general', 'my_settings_section', array('load_phpmailer'));
        register_setting('general', 'load_phpmailer', 'esc_attr');

        add_settings_field('debug_phpmailer', 'Debug PHP Mailer', array($this, 'debug_phpmailer_callback'), 'general', 'my_settings_section', array('debug_phpmailer'));
        register_setting('general', 'debug_phpmailer', 'esc_attr');

        add_settings_field('html_minify', 'HTML Minify', array($this, 'html_minify_callback'), 'general', 'my_settings_section', array('html_minify'));
        register_setting('general', 'html_minify', 'esc_attr');

        add_settings_field('qtn_copy_dir', 'Quotation Copy Directory', array($this, 'qtn_copy_dir_callback'), 'general', 'my_settings_section', array('qtn_copy_dir'));
        register_setting('general', 'qtn_copy_dir', 'esc_attr');

        add_settings_field('showroom_order_copy_dir', 'Showroom Order Copy Directory', array($this, 'showroom_order_copy_dir_callback'), 'general', 'my_settings_section', array('showroom_order_copy_dir'));
        register_setting('general', 'showroom_order_copy_dir', 'esc_attr');

        add_settings_field('receipt_copy_dir', 'Receipt Copy Directory', array($this, 'receipt_copy_dir_callback'), 'general', 'my_settings_section', array('receipt_copy_dir'));
        register_setting('general', 'receipt_copy_dir', 'esc_attr');

        add_settings_field('cop_copy_dir', 'COP Copy Directory', array($this, 'cop_copy_dir_callback'), 'general', 'my_settings_section', array('cop_copy_dir'));
        register_setting('general', 'cop_copy_dir', 'esc_attr');

        add_settings_field('po_copy_dir', 'PO Copy Directory', array($this, 'po_copy_dir_callback'), 'general', 'my_settings_section', array('po_copy_dir'));
        register_setting('general', 'po_copy_dir', 'esc_attr');

        add_settings_field('dn_copy_dir', 'DN Copy Directory', array($this, 'dn_copy_dir_callback'), 'general', 'my_settings_section', array('dn_copy_dir'));
        register_setting('general', 'dn_copy_dir', 'esc_attr');

        add_settings_field('pjo_copy_dir', 'PJO Copy Directory', array($this, 'pjo_copy_dir_callback'), 'general', 'my_settings_section', array('pjo_copy_dir'));
        register_setting('general', 'pjo_copy_dir', 'esc_attr');

        add_settings_field('stf_copy_dir', 'STF Copy Directory', array($this, 'stf_copy_dir_callback'), 'general', 'my_settings_section', array('stf_copy_dir'));
        register_setting('general', 'stf_copy_dir', 'esc_attr');

        add_settings_field('loading_copy_dir', 'Loading List Copy Directory', array($this, 'loading_copy_dir_callback'), 'general', 'my_settings_section', array('loading_copy_dir'));
        register_setting('general', 'loading_copy_dir', 'esc_attr');

        add_settings_field('offloading_copy_dir', 'Offloading List Copy Directory', array($this, 'offloading_copy_dir_callback'), 'general', 'my_settings_section', array('offloading_copy_dir'));
        register_setting('general', 'offloading_copy_dir', 'esc_attr');

        add_settings_field('imp_decl_copy_dir', 'Import Declaration List Copy Directory', array($this, 'imp_decl_copy_dir_callback'), 'general', 'my_settings_section', array('imp_decl_copy_dir'));
        register_setting('general', 'imp_decl_copy_dir', 'esc_attr');

        add_settings_field('int_imp_decl_copy_dir', 'Internal Import Declaration List Copy Directory', array($this, 'int_imp_decl_copy_dir_callback'), 'general', 'my_settings_section', array('int_imp_decl_copy_dir'));
        register_setting('general', 'int_imp_decl_copy_dir', 'esc_attr');

        add_settings_field('cost_sheet_copy_dir', 'Cost Sheet List Copy Directory', array($this, 'cost_sheet_copy_dir_callback'), 'general', 'my_settings_section', array('cost_sheet_copy_dir'));
        register_setting('general', 'cost_sheet_copy_dir', 'esc_attr');

        add_settings_field('single_cost_sheet_copy_dir', 'Single Cost Sheet Copy Directory', array($this, 'single_cost_sheet_copy_dir_callback'), 'general', 'my_settings_section', array('single_cost_sheet_copy_dir'));
        register_setting('general', 'single_cost_sheet_copy_dir', 'esc_attr');

        add_settings_field('daily_cash_check_copy_dir', 'Daily Cash Check Copy Directory', array($this, 'daily_cash_check_copy_dir_callback'), 'general', 'my_settings_section', array('daily_cash_check_copy_dir'));
        register_setting('general', 'daily_cash_check_copy_dir', 'esc_attr');

        add_settings_field('performa_invoice_copy_dir', 'Performa Invoice Copy Directory', array($this, 'performa_invoice_copy_dir_callback'), 'general', 'my_settings_section', array('performa_invoice_copy_dir'));
        register_setting('general', 'performa_invoice_copy_dir', 'esc_attr');

        add_settings_field('tax_invoice_copy_dir', 'Tax Invoice Copy Directory', array($this, 'tax_invoice_copy_dir_callback'), 'general', 'my_settings_section', array('tax_invoice_copy_dir'));
        register_setting('general', 'tax_invoice_copy_dir', 'esc_attr');

        add_settings_field('tax_credit_note_copy_dir', 'Tax Credit Note Copy Directory', array($this, 'tax_credit_note_copy_dir_callback'), 'general', 'my_settings_section', array('tax_credit_note_copy_dir'));
        register_setting('general', 'tax_credit_note_copy_dir', 'esc_attr');

        add_settings_field('reversal_of_qtn_copy_dir', 'Reversal of Confirmed Quotation Copy Directory', array($this, 'reversal_of_qtn_copy_dir_callback'), 'general', 'my_settings_section', array('reversal_of_qtn_copy_dir'));
        register_setting('general', 'reversal_of_qtn_copy_dir', 'esc_attr');

        add_settings_field('payment_voucher_copy_dir', 'Payment Voucher Copy Directory', array($this, 'payment_voucher_copy_dir_callback'), 'general', 'my_settings_section', array('payment_voucher_copy_dir'));
        register_setting('general', 'payment_voucher_copy_dir', 'esc_attr');

        add_settings_field('purchase_voucher_copy_dir', 'Purchase Voucher Copy Directory', array($this, 'purchase_voucher_copy_dir_callback'), 'general', 'my_settings_section', array('purchase_voucher_copy_dir'));
        register_setting('general', 'purchase_voucher_copy_dir', 'esc_attr');

        add_settings_field('supplier_payment_copy_dir', 'Supplier Payment List Copy Directory', array($this, 'supplier_payment_copy_dir_callback'), 'general', 'my_settings_section', array('supplier_payment_copy_dir'));
        register_setting('general', 'supplier_payment_copy_dir', 'esc_attr');

        add_settings_field('sales_bonus_copy_dir', 'Sales Monthly Bonus Copy Directory', array($this, 'sales_bonus_copy_dir_callback'), 'general', 'my_settings_section', array('sales_bonus_copy_dir'));
        register_setting('general', 'sales_bonus_copy_dir', 'esc_attr');

        add_settings_field('support_bonus_copy_dir', 'Support Monthly Bonus Copy Directory', array($this, 'support_bonus_copy_dir_callback'), 'general', 'my_settings_section', array('support_bonus_copy_dir'));
        register_setting('general', 'support_bonus_copy_dir', 'esc_attr');

        add_settings_field('employee_profile_copy_dir', 'Employee Profile Copy Directory', array($this, 'employee_profile_copy_dir_callback'), 'general', 'my_settings_section', array('employee_profile_copy_dir'));
        register_setting('general', 'employee_profile_copy_dir', 'esc_attr');

        add_settings_field('attendance_sheet_copy_dir', 'Attendance Sheet Copy Directory', array($this, 'attendance_sheet_copy_dir_callback'), 'general', 'my_settings_section', array('attendance_sheet_copy_dir'));
        register_setting('general', 'attendance_sheet_copy_dir', 'esc_attr');

        add_settings_field('salary_sheet_copy_dir', 'Salary Sheet Copy Directory', array($this, 'salary_sheet_copy_dir_callback'), 'general', 'my_settings_section', array('salary_sheet_copy_dir'));
        register_setting('general', 'salary_sheet_copy_dir', 'esc_attr');

        add_settings_field('employee_loan_copy_dir', 'Employee Advance Loan Copy Directory', array($this, 'employee_loan_copy_dir_callback'), 'general', 'my_settings_section', array('employee_loan_copy_dir'));
        register_setting('general', 'employee_loan_copy_dir', 'esc_attr');

        add_settings_field('leave_salary_copy_dir', 'Leave Salary Copy Directory', array($this, 'leave_salary_copy_dir_callback'), 'general', 'my_settings_section', array('leave_salary_copy_dir'));
        register_setting('general', 'leave_salary_copy_dir', 'esc_attr');

        add_settings_field('overtime_pay_copy_dir', 'Overtime Pay Copy Directory', array($this, 'overtime_pay_copy_dir_callback'), 'general', 'my_settings_section', array('overtime_pay_copy_dir'));
        register_setting('general', 'overtime_pay_copy_dir', 'esc_attr');

        add_settings_field('sales_report_copy_dir', 'Sales Report Copy Directory', array($this, 'sales_report_copy_dir_callback'), 'general', 'my_settings_section', array('sales_report_copy_dir'));
        register_setting('general', 'sales_report_copy_dir', 'esc_attr');

        add_settings_field('client_statement_copy_dir', 'Client Statement of Account Copy Directory', array($this, 'client_statement_copy_dir_callback'), 'general', 'my_settings_section', array('client_statement_copy_dir'));
        register_setting('general', 'client_statement_copy_dir', 'esc_attr');

        add_settings_field('customer_order_delivery_status_dir', 'Customer Order & Delivery Status Directory', array($this, 'customer_order_delivery_status_dir_callback'), 'general', 'my_settings_section', array('customer_order_delivery_status_dir'));
        register_setting('general', 'customer_order_delivery_status_dir', 'esc_attr');

        add_settings_field('confirm_order_arrival_dir', 'Confirm Order Arrival Statement Directory', array($this, 'confirm_order_arrival_dir_callback'), 'general', 'my_settings_section', array('confirm_order_arrival_dir'));
        register_setting('general', 'confirm_order_arrival_dir', 'esc_attr');

        add_settings_field('partial_order_arrival_dir', 'Partial Order Arrival Statement Directory', array($this, 'partial_order_arrival_dir_callback'), 'general', 'my_settings_section', array('partial_order_arrival_dir'));
        register_setting('general', 'partial_order_arrival_dir', 'esc_attr');

        add_settings_field('store_credit_note_dir', 'Store Credit Note Directory', array($this, 'store_credit_note_dir_callback'), 'general', 'my_settings_section', array('store_credit_note_dir'));
        register_setting('general', 'store_credit_note_dir', 'esc_attr');
    }

    public function my_settings_section_callback() { // Section Callback
        echo '<p>These are some additional options.</p>';
    }

    public function load_minify_callback($args) {
        $option = get_option($args[0]);
        echo '<label class="description" id="' . $args[0] . '-description"><input type="checkbox" id="' . $args[0] . '" name="' . $args[0] . '" value="1"' . checked(1, $option, false) . '> Enable Load Minify</label>';
    }

    public function load_phpmailer_callback($args) {
        $option = get_option($args[0]);
        echo '<label class="description" id="' . $args[0] . '-description"><input type="checkbox" id="' . $args[0] . '" name="' . $args[0] . '" value="1"' . checked(1, $option, false) . '> Enable PHP Mailer</label>';
    }

    public function debug_phpmailer_callback($args) {
        $option = get_option($args[0]);
        echo '<label class="description" id="' . $args[0] . '-description"><input type="checkbox" id="' . $args[0] . '" name="' . $args[0] . '" value="1"' . checked(1, $option, false) . '> Debug PHP Mailer</label>';
    }

    public function html_minify_callback($args) {
        $option = get_option($args[0]);
        echo '<label class="description" id="' . $args[0] . '-description"><input type="checkbox" id="' . $args[0] . '" name="' . $args[0] . '" value="1"' . checked(1, $option, false) . '> Enable HTML Minify</label>';
    }

    public function qtn_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function showroom_order_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function receipt_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function cop_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function po_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function dn_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function pjo_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function stf_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function loading_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function offloading_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function imp_decl_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function int_imp_decl_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function cost_sheet_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function single_cost_sheet_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function daily_cash_check_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function performa_invoice_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function tax_invoice_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function tax_credit_note_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function reversal_of_qtn_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function payment_voucher_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function purchase_voucher_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function supplier_payment_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function sales_bonus_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function support_bonus_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function employee_profile_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function attendance_sheet_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function salary_sheet_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function employee_loan_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function leave_salary_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function overtime_pay_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function sales_report_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function client_statement_copy_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function customer_order_delivery_status_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function confirm_order_arrival_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function partial_order_arrival_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

    public function store_credit_note_dir_callback($args) {
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" style="width:100%">';
        echo '<p class="description" id="' . $args[0] . '-description">Please enter the Copy Directory Path ending with slash(\)</p>';
    }

}

new ExtraSetting;

/**
 * 
 * @param array $column
 * @return string
 */
function new_modify_user_table($column) {
    $column['employee_id'] = 'Employee ID';
    return $column;
}

add_filter('manage_users_columns', 'new_modify_user_table');

/**
 * 
 * @param type $val
 * @param type $column_name
 * @param type $user_id
 * @return type
 */
function new_modify_user_table_row($val, $column_name, $user_id) {
    switch ($column_name) {
        case 'employee_id' :
            return get_user_meta($user_id, 'employee_id', true);
        default:
    }
    return $val;
}

add_filter('manage_users_custom_column', 'new_modify_user_table_row', 10, 3);
