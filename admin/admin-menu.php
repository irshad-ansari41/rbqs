<?php

include_once('re-order-popup.php');

include_once('extra-option/options.php');
include_once('location-list.php');
include_once('client-list.php');

include_once('suppliers/supplier-list.php');
include_once('suppliers/supplier-add.php');
include_once('suppliers/supplier-edit.php');

include_once('item-list.php');
include_once('item-category-list.php');

include_once('project-job-order/project-job-order-list.php');
include_once('project-job-order/project-job-order-add.php');
include_once('project-job-order/project-job-order-edit.php');
include_once('project-job-order/project-job-order-view.php');

include_once('quality-control-report/quality-control-report-list.php');
include_once('quality-control-report/quality-control-report-add.php');
include_once('quality-control-report/quality-control-report-edit.php');
include_once('quality-control-report/quality-control-report-view.php');

include_once('stock-inventory/stock-inventory-list.php');
include_once('stock-inventory/stock-inventory-add.php');
include_once('stock-inventory/stock-inventory-edit.php');
include_once('stock-inventory/client-order-status.php');

include_once('sold-item/sold-items-list.php');
include_once('sold-item/sold-item-statistics.php');

include_once('pre-delivery/pre-delivery-list.php');
include_once('pre-delivery/pre-delivery-view.php');

include_once('stock-transfer/stock-transfer-list.php');
include_once('stock-transfer/stock-transfer-view.php');
include_once('stock-transfer/stf-packaging-label.php');

include_once('report/sales-report.php');
include_once('report/account-report.php');
include_once('report/qtn-report.php');
include_once('report/po-report.php');
include_once('report/dn-report.php');
include_once('report/ol-report.php');

include_once('upcoming-renewal.php');
include_once('daily-schedule.php');

include_once('order-tracker/order-tracker-list.php');
include_once('order-tracker/order-tracker-add.php');
include_once('order-tracker/order-tracker-edit.php');
include_once('order-tracker/order-tracker-view.php');
include_once('order-tracker/client-order-delivery-status.php');

include_once('offloading/offloading-list.php');
include_once('offloading/offloading-preview.php');
include_once('offloading/package-label.php');

include_once('import-declaration/import-declaration-list.php');
include_once('import-declaration/import-declaration-view.php');
include_once('import-declaration/import-declaration-internal-view.php');

include_once('cost-sheet/cost-sheet.php');
include_once('cost-sheet/cost-sheet-view.php');

include_once('container-loading/container-loading-list.php');
include_once('container-loading/container-loading-preview.php');

include_once('showroom-order/showroom-order.php');
include_once('showroom-order/add-showroom-order.php');
include_once('showroom-order/edit-showroom-order.php');
include_once('showroom-order/view-showroom-order.php');

include_once('delivery-note/delivery-note-list.php');
include_once('delivery-note/packaging-label.php');
include_once('delivery-note/packaging-list.php');
include_once('delivery-note/stock-delivery-note.php');
include_once('delivery-note/project-delivery-note.php');
include_once('delivery-note/order-delivery-note.php');

include_once('receipt/receipt-list.php');
include_once('receipt/receipt-create.php');
include_once('receipt/receipt-edit.php');
include_once('receipt/receipt-view.php');
include_once('receipt/receipt-settle.php');

include_once('receipt/store-credit-note/store-credit-note-list.php');
include_once('receipt/store-credit-note/store-credit-note-view.php');

include_once('confirm-order/confirm-order-list.php');
include_once('confirm-order/confirm-order-items.php');
include_once('confirm-order/confirm-order-view.php');

include_once('quotation/quotations.php');
include_once('quotation/quotation-create.php');
include_once('quotation/quotation-edit.php');
include_once('quotation/quotation-view.php');

include_once('purchase-order/purchase-order-list.php');
include_once('purchase-order/purchase-order-view.php');
include_once('purchase-order/purchase-order-items-project.php');
include_once('purchase-order/purchase-order-items.php');

include_once('my-account/daily-cash-check.php');
include_once('my-account/cash-on-hold.php');

include_once('my-account/bank-deposit/bank-deposit-list.php');
include_once('my-account/bank-deposit/bank-deposit-edit.php');
include_once('my-account/bank-deposit/bank-deposit-settle.php');
include_once('my-account/bank-deposit/bank-deposit-registry.php');

include_once('my-account/bank-account/bank-account-list.php');
include_once('my-account/bank-account/bank-account-add.php');
include_once('my-account/bank-account/bank-account-edit.php');
include_once('my-account/bank-account/bank-account-registry.php');

include_once('my-account/proforma-invoice/proforma-invoice-list.php');
include_once('my-account/proforma-invoice/proforma-invoice-view.php');

include_once('my-account/tax-invoice/tax-invoice-list.php');
include_once('my-account/tax-invoice/tax-invoice-view.php');

include_once('my-account/tax-credit-note/tax-credit-note-list.php');
include_once('my-account/tax-credit-note/tax-credit-note-view.php');

include_once('my-account/sales-reversal/sales-reversal-list.php');
include_once('my-account/sales-reversal/sales-reversal-edit.php');
include_once('my-account/sales-reversal/sales-reversal-view.php');

include_once('my-account/payment-voucher/payment-voucher-list.php');
include_once('my-account/payment-voucher/payment-voucher-create.php');
include_once('my-account/payment-voucher/payment-voucher-edit.php');
include_once('my-account/payment-voucher/payment-voucher-view.php');
include_once('my-account/payment-voucher/payment-registry.php');
include_once('my-account/payment-voucher/payment-options.php');

include_once('my-account/purchase-voucher/purchase-voucher-list.php');
include_once('my-account/purchase-voucher/purchase-voucher-create.php');
include_once('my-account/purchase-voucher/purchase-voucher-edit.php');
include_once('my-account/purchase-voucher/purchase-voucher-view.php');
include_once('my-account/purchase-voucher/purchase-registry.php');

include_once('my-account/petty-cash/petty-cash-list.php');
include_once('my-account/petty-cash/petty-cash-add.php');
include_once('my-account/petty-cash/petty-cash-edit.php');
include_once('my-account/petty-cash/petty-cash-view.php');
include_once('my-account/petty-cash/petty-cash-registry.php');

include_once('my-account/vat-calculator.php');

include_once('my-account/supplier-purchase/supplier-purchase-list.php');
include_once('my-account/supplier-purchase/supplier-purchase-registry.php');
include_once('my-account/monthly-sales-bonus.php');
include_once('my-account/monthly-support-bonus.php');

include_once('my-account/order-arrival/order-arrival-list.php');
include_once('my-account/order-arrival/confirm-order-arrival-statement.php');
include_once('my-account/order-arrival/partial-order-arrival-statement.php');

include_once('hr/employee/employee.php');
include_once('hr/employee/employee-master.php');
include_once('hr/employee/employee-add.php');
include_once('hr/employee/employee-edit.php');
include_once('hr/employee/employee-view.php');

include_once('hr/attendance/attendance.php');
include_once('hr/attendance/attendance-add.php');
include_once('hr/attendance/attendance-edit.php');

include_once('hr/leave-salery/leave-salery.php');
include_once('hr/leave-salery/leave-salery-view.php');

include_once('hr/emp-adv-loan/emp-adv-loan.php');
include_once('hr/emp-adv-loan/emp-adv-loan-view.php');

include_once('hr/leave-salary-eligibility.php');

include_once('hr/salary-sheet/salary-sheet.php');
include_once('hr/salary-sheet/salary-sheet-add.php');
include_once('hr/salary-sheet/salary-sheet-edit.php');

include_once('hr/overtime-pay/overtime-pay.php');
include_once('hr/overtime-pay/overtime-pay-add.php');
include_once('hr/overtime-pay/overtime-pay-edit.php');
include_once('hr/overtime-pay/overtime-pay-view.php');

include_once('employer-profile/options.php');

/**
 * 
 */
if (is_super_admin()) {
    add_action('admin_menu', 'register_intranet_menu_page');
}

/**
 * 
 */
function register_intranet_menu_page() {

    $setting_page = add_menu_page('Roche Bobois', 'Back Office', 'rb_admin_menu', 'roche-bobois', 'rb_options_page', 'dashicons-menu-alt3', 3);
    add_submenu_page('roche-bobois', 'RB Options', 'RB Options', 'rb_options_menu', 'roche-bobois', 'rb_options_page');
    add_submenu_page('roche-bobois', 'Quotation Structure', 'Quotation Structure', 'rb_location_menu', 'rw-locations', 'admin_ctm_locations_list');
    add_submenu_page('roche-bobois', 'Client Master', 'Client Master', 'rb_client_menu', 'rw-clients', 'admin_ctm_client_list');

    add_submenu_page('roche-bobois', 'Supplier Master', 'Supplier Master', 'rb_supplier_menu', 'rw-suppliers', 'admin_ctm_supplier_list');
    add_submenu_page('rw-suppliers', 'Add Supplier', 'Add Supplier', 'rb_supplier_menu', 'rw-suppliers-add', 'admin_ctm_supplier_add');
    add_submenu_page('rw-suppliers', 'Edit Supplier', 'Edit Supplier', 'rb_supplier_menu', 'rw-suppliers-edit', 'admin_ctm_supplier_edit');

    add_submenu_page('roche-bobois', 'Product Master', 'Product Master', 'rb_product_menu', 'rw-items', 'admin_ctm_item_list');
    add_submenu_page('roche-bobois', 'Product Category', 'Product Category', 'rb_category_menu', 'rw-item-category', 'admin_ctm_item_category_list');
    add_submenu_page('roche-bobois', 'Import Product', 'Import Product', 'rb_import_product', 'import-product', 'import_product_page');

    /* Daily Operation Schdule */
    add_menu_page('Renewal Reminder', 'Renewal Reminder', 'rb_renewal', 'renewal', 'admin_ctm_renewal_page', 'dashicons-bell', 6);
    /* Daily Operation Schdule */
    add_menu_page('Daily Operation Schdule', 'Daily Operation Schedule', 'rb_daily_schedule', 'daily-schedule', 'admin_ctm_daily_schedule_page', 'dashicons-calendar-alt', 6);

    /* Quotation Order */
    add_menu_page('MyQuotation', 'MyQuotation', 'rb_quotation', 'quotation', 'admin_ctm_quotation_page', 'dashicons-tag', 6);
    add_submenu_page('quotation', 'Create Quotation', 'Create Quotation', 'rb_create_quotation', 'quotation-create', 'admin_ctm_quotation_create_page');
    add_submenu_page('quotation', 'Quotation Edit', 'Quotation Edit', 'rb_edit_quotation', 'quotation-edit', 'admin_ctm_quotation_edit_page');
    add_submenu_page('quotation', 'Quotation View', 'Quotation View', 'rb_view_quotation', 'quotation-view', 'admin_ctm_quotation_view_page');

    /* Receipt Order */
    add_menu_page('MyReceipt', 'MyReceipt', 'rb_receipt', 'receipt', 'admin_ctm_receipt_list_page', 'dashicons-calculator', 7);
    add_submenu_page('receipt_page', 'Create Receipt', 'Create Receipt', 'rb_create_receipt', 'receipt-create', 'admin_ctm_receipt_create_page');
    add_submenu_page('receipt_page', 'Edit Receipt', 'Edit Receipt', 'rb_edit_receipt', 'receipt-edit', 'admin_ctm_receipt_edit_page');
    add_submenu_page('receipt_page', 'Show Receipt', 'Show Receipt', 'rb_view_receipt', 'receipt-view', 'admin_ctm_receipt_view_page');
    add_submenu_page('receipt_page', 'Settle Receipt', 'Settle Receipt', 'rb_settle_receipt', 'receipt-settle', 'admin_ctm_receipt_settle_page');
    
    add_submenu_page('receipt', 'Store Credit Note', 'Store Credit Note', 'rb_store_credit_note', 'store-credit-note', 'admin_ctm_store_credit_note_list_page');
    add_submenu_page('store-credit', 'Store Credit Note', 'Store Credit Note', 'rb_store_credit_note', 'store-credit-note-view', 'admin_ctm_store_credit_note_view_page');

    /* Delivery Order */
    add_menu_page('Showroom Order', 'Showroom Order', 'rb_showroom_order', 'showroom-order', 'admin_ctm_showroom_order_page', 'dashicons-visibility', 7);
    add_submenu_page('showroom-order', 'Add', 'Add', 'rb_showroom_order_add', 'add-showroom-order', 'admin_ctm_add_showroon_order_page');
    add_submenu_page('showroom-order', 'Edit', 'Edit', 'rb_showroom_order_edit', 'edit-showroom-order', 'admin_ctm_edit_showroon_order_page');
    add_submenu_page('showroom-order', 'View', 'View', 'rb_showroom_order_view', 'view-showroom-order', 'admin_ctm_view_showroon_order_page');

    /* Confirm Order */
    add_menu_page('Client Confirmed Order', 'Client Confirmed Order', 'rb_confirm_order', 'confirm-order', 'admin_ctm_confirm_order_list_page', 'dashicons-saved', 8);
    add_submenu_page('confirm-order', 'Confirmed Order Items', 'Confirm Order Items', 'rb_confirm_order_items', 'confirm-order-items', 'admin_ctm_confirm_order_items_page');
    add_submenu_page('confirm-order', 'Confirmed Order View', 'Confirm Order View', 'rb_confirm_order_view', 'confirm-order-view', 'admin_ctm_confirm_order_view_page');

    /* Purchase Order */
    add_menu_page('Purchase Order', 'Purchase Order', 'rb_purchase_order', 'purchase-order', 'admin_ctm_purchase_order_list_page', 'dashicons-money-alt', 9);
    add_submenu_page('purchase-order', 'Purchase Order Items', 'Purchase Order Items', 'rb_purchase_order_items', 'purchase-order-items', 'purchase_order_items_page');
    add_submenu_page('purchase-order', 'Purchase Order View', 'Purchase Order View', 'rb_purchase_order_view', 'purchase-order-view', 'admin_ctm_purchase_order_view_page');

    /* Order Tracker */
    add_menu_page('Order Tracker', 'Order Tracker', 'rb_order_tracker', 'order-tracker', 'admin_ctm_order_tracker_list_page', 'dashicons-location-alt', 9);
    add_submenu_page('order-tracker', 'Add Order Tracker', 'Add Order Tracker', 'rb_order_tracker_add', 'order-tracker-add', 'admin_ctm_order_tracker_add_page');
    add_submenu_page('order-tracker', 'Edit Order Tracker', 'Edit Order Tracker', 'rb_order_tracker_edit', 'order-tracker-edit', 'admin_ctm_order_tracker_edit_page');
    add_submenu_page('order-tracker-edit', 'View Order Tracker', 'View Order Tracker', 'rb_order_tracker_view', 'order-tracker-view', 'admin_ctm_order_tracker_view_page');
    add_submenu_page('order-tracker', 'Customer Order & Delivery Status', 'Customer Order & Delivery Status', 'rb_client_order_delivery_status', 'client-order-delivery-status', 'admin_ctm_client_order_delivery_status_page');

    /* Container Loading */
    add_menu_page('DELIVERED FF - LOADING', 'DELIVERED FF - LOADING', 'rb_container_loading', 'container-loading', 'admin_ctm_container_loading_list_page', 'dashicons-table-col-after', 9);
    add_submenu_page('container-loading', 'Container Loading List', 'Container Loading List', 'rb_container_loading_preview', 'container-loading-preview', 'admin_ctm_container_loading_preview_page');

    /* Offloading */
    add_menu_page('Offloading List', 'Offloading List', 'rb_offloading', 'offloading', 'admin_ctm_offloading_list_page', 'dashicons-arrow-up-alt', 11);
    add_submenu_page('offloading', 'Preview Offloading List', 'Preview Offloading List', 'rb_offloading_preview', 'offloading-preview', 'admin_ctm_offloading_preview_page');
    add_submenu_page('offloading', 'Package Label', 'Package Label', 'rb_offloading_packaging_label', 'package-label', 'admin_ctm_package_label_page');

    /* Import Declaration */
    add_menu_page('Import Declaration', 'Import Declaration List', 'rb_import_declaration', 'import-declaration', 'admin_ctm_import_declaration_page', 'dashicons-feedback', 11);
    add_submenu_page('import-declaration', 'Import Declaration', 'Import Declaration', 'rb_import_declaration', 'import-declaration-view', 'admin_ctm_import_declaration_view_page');
    add_submenu_page('import-declaration', 'Internal Import Declaration', 'Internal Import Declaration', 'rb_import_declaration', 'import-declaration-internal-view', 'admin_ctm_import_declaration_internal_view_page');
    add_submenu_page('import-declaration', 'Cost Sheet', 'Cost Sheet', 'rb_cost_sheet', 'cost-sheet', 'admin_ctm_cost_sheet_page');
    add_submenu_page('cost-sheet', 'View Cost Sheet', 'View Cost Sheet', 'rb_cost_sheet', 'cost-sheet-view', 'admin_ctm_cost_sheet_view_page');

    /* Stock Inventory */
    add_menu_page('MyStock', 'MyStock', 'rb_stock_inventory', 'stock-inventory', 'admin_ctm_stock_inventory_list_page', 'dashicons-building', 12);
    add_submenu_page('stock-inventory', 'Add Stock Inventory', 'Add Stock Inventory', 'rb_add_stock_transfer', 'stock-inventory-add', 'admin_ctm_stock_inventory_add_page');
    add_submenu_page('stock-inventory-edit', 'edit Stock Inventory Transfer', 'Edit Stock Inventory', 'rb_edit_stock_transfer', 'stock-inventory-edit', 'admin_ctm_stock_inventory_edit_page');

    add_submenu_page('stock-inventory', 'Client Order Status', 'Client Order Status', 'rb_client_order_status', 'client-order-status', 'admin_ctm_client_order_status_page');

    add_submenu_page('stock-inventory', 'Sold Items', 'Sold Items', 'rb_sold_items', 'sold-items', 'admin_ctm_sold_items_list_page');
    add_submenu_page('sold-items', 'Sold Items Statistics', 'Sold Items Statistics', 'rb_sold_items', 'sold-items-statistics', 'admin_ctm_sold_items_statistics_page');

    add_submenu_page('stock-inventory', 'Stock Transfer', 'Stock Transfer', 'rb_stock_transfer', 'stock-trasfer', 'admin_ctm_stock_transfer_list_page');
    add_submenu_page('stock-trasfer', 'View Stock Transfer', 'View Stock Transfer', 'rb_stock_transfer', 'stock-trasfer-view', 'admin_ctm_stock_transfer_view_page');
    add_submenu_page('stock-trasfer', 'Package Label', 'Package Label', 'rb_stf_packaging_label', 'stf-packaging-label', 'admin_ctm_stf_packaging_label_page');

    add_submenu_page('stock-inventory', 'Pre Delivery', 'Pre Delivery   ', 'rb_pre_delivery', 'pre-delivery', 'admin_ctm_pre_delivery_list_page');
    add_submenu_page('pre-delivery', 'View Pre Delivery', 'View Pre Delivery', 'rb_pre_delivery', 'pre-delivery-view', 'admin_ctm_pre_delivery_view_page');

    /* Delivery Order */
    add_menu_page('Delivery Note', 'Delivery Note', 'rb_stock_project_dn_list', 'delivery-note-list', 'admin_ctm_sp_delivery_note_list_page', 'dashicons-groups', 13);
    add_submenu_page('delivery-note-list', 'Stock Delivery Note', 'Stock Delivery Note', 'rb_stock_dn', 'stock-delivery-note', 'admin_ctm_stock_delivery_note_page');
    add_submenu_page('delivery-note-list', 'Project Delivery Note', 'Project Delivery Note', 'rb_project_dn', 'project-delivery-note', 'admin_ctm_project_delivery_note_page');
    add_submenu_page('delivery-note-list', 'Order Delivery Note', 'Order Delivery Note', 'rb_order_dn', 'order-delivery-note', 'admin_ctm_order_delivery_note_page');
    add_submenu_page('delivery-note-list', 'Packaging List', 'Packaging List', 'rb_packaging_list', 'packaging-list', 'admin_ctm_packaging_list_page');
    add_submenu_page('delivery-note-list', 'Packaging Label', 'Packaging Label', 'rb_dn_packaging_label', 'packaging-label', 'admin_ctm_packaging_label_page');

    /* Delivery Order */
    add_menu_page('Project Job Order', 'Project Job Order', 'rb_project_job_order', 'project-job-order', 'admin_ctm_project_job_order_list_page', 'dashicons-admin-tools', 14);
    add_submenu_page('project-job-order', 'PJO Add', 'PJO Add', 'rb_project_job_order_add', 'project-job-order-add', 'admin_ctm_project_job_order_add_page');
    add_submenu_page('project-job-order', 'PJO Edit', 'PJO Edit', 'rb_project_job_order_edit', 'project-job-order-edit', 'admin_ctm_project_job_order_edit_page');
    add_submenu_page('project-job-order', 'PJO View', 'PJO View', 'rb_project_job_order_view', 'project-job-order-view', 'admin_ctm_project_job_order_view_page');

    /* Delivery Order */
    add_menu_page('Quality Control Report', 'Quality Control Report', 'rb_quality_control_report', 'quality-control-report', 'admin_ctm_quality_control_report_list_page', 'dashicons-backup', 14);
    add_submenu_page('quality-control-report', 'QCR Add', 'QCR Add', 'rb_quality_control_report', 'quality-control-report-add', 'admin_ctm_quality_control_report_add_page');
    add_submenu_page('quality-control-report', 'QCR Edit', 'QCR Edit', 'rb_quality_control_report', 'quality-control-report-edit', 'admin_ctm_quality_control_report_edit_page');
    add_submenu_page('quality-control-report', 'QCR View', 'QCR View', 'rb_quality_control_report', 'quality-control-report-view', 'admin_ctm_quality_control_report_view_page');

    /* Generate Report */
    add_menu_page('Generate Report', 'Generate Report', 'rb_generate_report', 'generate-report', 'admin_ctm_sales_report_page', 'dashicons-printer', 16);
    add_submenu_page('generate-report', 'Sales Report', 'Sales Report', 'rb_sales_report', 'sales-report', 'admin_ctm_sales_report_page');
    add_submenu_page('generate-report', 'Client Statement of Account', 'Client Statement of Account', 'rb_account_report', 'account-report', 'admin_ctm_account_report_page');
    add_submenu_page('generate-report', 'Quotation Report', 'Quotation Report', 'rb_qtn_report', 'qtn-report', 'admin_ctm_qtn_report_page');
    add_submenu_page('generate-report', 'Purchase Order Report', 'Purchase Order Report', 'rb_po_report', 'po-report', 'admin_ctm_po_report_page');
    add_submenu_page('generate-report', 'Delivery Note Report', 'Delivery Note Report', 'rb_dn_report', 'dn-report', 'admin_ctm_dn_report_page');
    add_submenu_page('generate-report', 'Offloading List Report', 'Offloading List Report', 'rb_ol_report', 'ol-report', 'admin_ctm_ol_report_page');

    /* Generate Report */
    add_menu_page('MyAccount', 'MyAccount', 'rb_account', 'my-account', 'admin_ctm_bank_deposit_list_page', 'dashicons-text-page', 17);
    add_submenu_page('my-account', 'Verified Customer Receipt', 'Verified Customer Receipt', 'rb_bank_deposit', 'bank-deposit', 'admin_ctm_bank_deposit_list_page');
    add_submenu_page('my-account', 'Bank Deposit Slip', 'Bank Deposit Slip', 'rb_daily_cash_check', 'daily-cash-check', 'admin_ctm_daily_cash_check_page');

    add_submenu_page('my-account', 'Bank Account', 'Bank Account', 'rb_bank_account', 'bank-account', 'admin_ctm_bank_account_list_page');
    add_submenu_page('bank-account', 'Bank Account Registry', 'Bank Account Registry', 'rb_bank_account', 'bank-account-registry', 'admin_ctm_bank_account_registry_page');
    add_submenu_page('bank-account', 'Bank Account add', 'Add Account Deposit', 'rb_bank_account', 'bank-account-add', 'admin_ctm_bank_account_add_page');
    add_submenu_page('bank-account', 'Bank Account Edit', 'Edit Account Deposit', 'rb_bank_account', 'bank-account-edit', 'admin_ctm_bank_account_edit_page');

    add_submenu_page('daily-cash-check', 'Cash On Hold', 'Cash On Hold', 'rb_daily_cash_check', 'cash-on-hold', 'admin_ctm_cash_on_hold_page');
    add_submenu_page('bank-deposit', 'Bank Deposit Registry', 'Bank Deposit Registry', 'rb_bank_deposit', 'bank-deposit-registry', 'admin_ctm_bank_deposit_registry_page');
    add_submenu_page('bank-deposit', 'Edit Bank Deposit', 'Edit Bank Deposit', 'rb_bank_deposit', 'bank-deposit-edit', 'admin_ctm_bank_deposit_edit_page');
    add_submenu_page('bank-deposit', 'Settle Bank Deposit', 'Settle Bank Deposit', 'rb_bank_deposit', 'bank-deposit-settle', 'admin_ctm_bank_deposit_settle_page');

    add_submenu_page('my-account', 'Proforma Invoice', 'Proforma Invoice', 'rb_proforma_invoice', 'proforma-invoice', 'admin_ctm_proforma_invoice_page');
    add_submenu_page('proforma-invoice', 'View Proforma Invoice', 'View Proforma Invoice', 'rb_proforma_invoice', 'proforma-invoice-view', 'admin_ctm_proforma_invoice_view_page');

    add_submenu_page('my-account', 'Tax Invoice', 'Tax Invoice', 'rb_tax_invoice', 'tax-invoice', 'admin_ctm_tax_invoice_page');
    add_submenu_page('tax-invoice', 'View Tax Invoice', 'View Tax Invoice', 'rb_tax_invoice', 'tax-invoice-view', 'admin_ctm_tax_invoice_view_page');

    add_submenu_page('my-account', 'Tax Credit Note', 'Tax Credit Note', 'rb_tax_credit_note', 'tax-credit-note', 'admin_ctm_tax_credit_note_page');
    add_submenu_page('tax-credit-note', 'View Tax Credit Note', 'View Tax Credit Note', 'rb_tax_credit_note', 'tax-credit-note-view', 'admin_ctm_tax_credit_note_view_page');

    add_submenu_page('my-account', 'Reversal of confirmed QTN', 'Reversal of confirmed QTN', 'rb_sales_reversal', 'sales-reversal', 'admin_ctm_sales_reversal_page');
    add_submenu_page('sales-reversal', 'Edit Reversal of confirmed QTN', 'Edit Reversal of confirmed QTN', 'rb_sales_reversal', 'sales-reversal-edit', 'admin_ctm_sales_reversal_edit_page');
    add_submenu_page('sales-reversal', 'View Reversal of confirmed QTN', 'View Reversal of confirmed QTN', 'rb_sales_reversal', 'sales-reversal-view', 'admin_ctm_sales_reversal_view_page');

    add_submenu_page('my-account', 'Payment Voucher', 'Payment Voucher', 'rb_payment_voucher', 'payment-voucher', 'admin_ctm_payment_voucher_page');
    add_submenu_page('payment-voucher', 'Create Payment Voucher', 'Create Payment Voucher', 'rb_payment_voucher', 'payment-voucher-create', 'admin_ctm_payment_voucher_create_page');
    add_submenu_page('payment-voucher', 'Edit Payment Voucher', 'Edit Payment Voucher', 'rb_payment_voucher', 'payment-voucher-edit', 'admin_ctm_payment_voucher_edit_page');
    add_submenu_page('payment-voucher', 'View Payment Voucher', 'View Payment Voucher', 'rb_payment_voucher', 'payment-voucher-view', 'admin_ctm_payment_voucher_view_page');
    add_submenu_page('payment-voucher', 'Payment Registry', 'Payment Registry', 'rb_payment_voucher', 'payment-registry', 'admin_ctm_payment_registry_page');
    $payment_page = add_submenu_page('payment-voucher', 'Payment Option', 'Payment Option', 'rb_payment_voucher', 'payment-options', 'admin_ctm_payment_option_page');

    add_submenu_page('my-account', 'Local Supper Payment', 'Local Supper Payment', 'rb_purchase_voucher', 'purchase-voucher', 'admin_ctm_purchase_voucher_page');
    add_submenu_page('purchase-voucher', 'Create Purchase Voucher', 'Create Purchase Voucher', 'rb_purchase_voucher', 'purchase-voucher-create', 'admin_ctm_purchase_voucher_create_page');
    add_submenu_page('purchase-voucher', 'Edit Purchase Voucher', 'Edit Purchase Voucher', 'rb_purchase_voucher', 'purchase-voucher-edit', 'admin_ctm_purchase_voucher_edit_page');
    add_submenu_page('purchase-voucher', 'View Purchase Voucher', 'View Purchase Voucher', 'rb_purchase_voucher', 'purchase-voucher-view', 'admin_ctm_purchase_voucher_view_page');
    add_submenu_page('purchase-voucher', 'Purchase Registry', 'Purchase Registry', 'rb_purchase_voucher', 'purchase-registry', 'admin_ctm_purchase_registry_page');

    add_submenu_page('my-account', 'Int\'l Supplier Payment', 'Int\'l Supplier Payment', 'rb_supplier_purchase', 'supplier-purchase', 'admin_ctm_supplier_purchase_page');
    add_submenu_page('supplier-purchase', 'Supplier Purchase Registry', 'Supplier Purchase Registry', 'rb_supplier_purchase', 'supplier-purchase-registry', 'admin_ctm_supplier_purchase_registry_page');

//    add_submenu_page('my-account', 'Petty Cash', 'Petty Cash', 'rb_petty_cash', 'petty-cash', 'admin_ctm_petty_cash_page');
//    add_submenu_page('petty-cash', 'Add Petty Cash', 'Add Petty Cash', 'rb_petty_cash', 'petty-cash-create', 'admin_ctm_petty_cash_add_page');
//    add_submenu_page('petty-cash', 'Edit Petty Cash', 'Edit Petty Cash', 'rb_petty_cash', 'petty-cash-edit', 'admin_ctm_petty_cash_edit_page');
//    add_submenu_page('petty-cash', 'View Petty Cash', 'View Petty Cash', 'rb_petty_cash', 'petty-cash-view', 'admin_ctm_petty_cash_view_page');
//    add_submenu_page('petty-cash', 'Petty Cash Registry', 'Petty Cash Registry', 'rb_petty_cash', 'petty-cash-registry', 'admin_ctm_petty_cash_registry_page');



    add_submenu_page('my-account', 'Sales Monthly Bonus', 'Sales Monthly Bonus', 'rb_sales_monthly_bonus', 'sales-monthly-bonus', 'admin_sales_monthly_bonus_page');
    add_submenu_page('my-account', 'Support Monthly Bonus', 'Support Monthly Bonus', 'rb_support_monthly_bonus', 'support-monthly-bonus', 'admin_suppoort_monthly_bonus_page');

    add_submenu_page('my-account', 'Order Arrival List', 'Order Arrival List', 'rb_confirm_order_arrival', 'order-arrival-list', 'admin_order_arival_list_page');
    add_submenu_page('my-account', 'Confirm Order Arrival Statment', 'Confirm Order Arrival Statment', 'rb_confirm_order_arrival', 'confirm-order-arrival', 'admin_confirm_order_arival_page');
    add_submenu_page('my-account', 'Partial Order Arrival Statment', 'Partial Order Arrival Statment', 'rb_partial_order_arrival', 'partial-order-arrival', 'admin_partial_order_arival_page');

    add_submenu_page('my-account', 'Vat Calculator', 'Vat Calculator', 'rb_vat_calculator', 'vat-calculator', 'admin_ctm_vat_calculator_page');

    /* Human Resource */
    add_menu_page('Human Resource', 'Human Resource', 'rb_human_resource', 'human-resource', 'admin_ctm_employee_page', 'dashicons-buddicons-buddypress-logo', 17);
    add_submenu_page('human-resource', 'Employee Master', 'Employee Master', 'rb_employee', 'employee', 'admin_ctm_employee_page');
    add_submenu_page('employee-master', 'Employee Master', 'Employee Master', 'rb_employee', 'employee-master', 'admin_ctm_employee_master_page');
    add_submenu_page('employee-master', 'Add Employee', 'Add Employee Master', 'rb_employee', 'employee-add', 'admin_ctm_employee_add_page');
    add_submenu_page('employee-master', 'Edit Employee', 'Edit Employee Master', 'rb_employee', 'employee-edit', 'admin_ctm_employee_edit_page');
    add_submenu_page('employee-master', 'View Employee', 'View Employee Master', 'rb_employee', 'employee-view', 'admin_ctm_employee_view_page');

    add_submenu_page('human-resource', 'Attendance', 'Attendance', 'rb_attendance', 'attendance', 'admin_ctm_attendance_page');
    add_submenu_page('attendance', 'Attendance', 'Attendance', 'rb_attendance', 'attendance-add', 'admin_ctm_attendance_add_page');
    add_submenu_page('attendance', 'Attendance', 'Attendance', 'rb_attendance', 'attendance-edit', 'admin_ctm_attendance_edit_page');

    add_submenu_page('human-resource', 'Salary Sheet', 'Salary Sheet', 'rb_salary_sheet', 'salary-sheet', 'admin_ctm_salary_sheet_page');
    add_submenu_page('salary-sheet', 'Add Salary Sheet', 'Add Salary Sheet', 'rb_salary_sheet', 'salary-sheet-add', 'admin_ctm_salary_sheet_add_page');
    add_submenu_page('salary-sheet', 'Edit Salary Sheet', 'Edit Salary Sheet', 'rb_salary_sheet', 'salary-sheet-edit', 'admin_ctm_salary_sheet_edit_page');

    add_submenu_page('human-resource', 'Leave Salary Eligibility', 'Leave Salary Eligibility', 'rb_leave_salary_eligibility', 'leave-salary-eligibility', 'admin_leave_salary_eligibility_page');

    add_submenu_page('human-resource', 'Employee Adv Loan', 'Employee Adv Loan', 'rb_emp_adv_loan', 'adv-loan', 'admin_ctm_emp_adv_loan_page');
    add_submenu_page('adv-loan', 'Create Emp. Adv. Loan', 'Create Emp. Adv. Loan', 'rb_emp_adv_loan', 'adv-loan-create', 'admin_ctm_emp_adv_loan_create_page');
    add_submenu_page('adv-loan', 'Edit Emp. Adv. Loan', 'Edit Emp. Adv. Loan', 'rb_emp_adv_loan', 'adv-loan-edit', 'admin_ctm_emp_adv_loan_edit_page');
    add_submenu_page('adv-loan', 'View Emp. Adv. Loan', 'View Emp. Adv. Loan', 'rb_emp_adv_loan', 'adv-loan-view', 'admin_ctm_emp_adv_loan_view_page');

    add_submenu_page('human-resource', 'Leave Salary', 'Leave Salary', 'rb_leave_salary', 'leave-salary', 'admin_ctm_leave_salary_page');
    add_submenu_page('leave-salary', 'View Leave Salary', 'View Leave Salary', 'rb_leave_salary', 'leave-salary-view', 'admin_ctm_leave_salary_view_page');

    add_submenu_page('human-resource', 'Overtime Pay', 'Overtime Pay', 'rb_overtime_pay', 'overtime-pay', 'admin_ctm_overtime_pay_page');
    add_submenu_page('overtime-pay', 'Add Overtime Pay', 'Add Overtime Pay', 'rb_overtime_pay', 'overtime-pay-add', 'admin_ctm_overtime_pay_add_page');
    add_submenu_page('overtime-pay', 'Edit Overtime Pay', 'Edit Overtime Pay', 'rb_overtime_pay', 'overtime-pay-edit', 'admin_ctm_overtime_pay_edit_page');
    add_submenu_page('overtime-pay', 'View Overtime Pay', 'View Overtime Pay', 'rb_overtime_pay', 'overtime-pay-view', 'admin_ctm_overtime_pay_view_page');

    $employer_page = add_menu_page('Employer Profile', 'Employer Profile', 'rb_employer_profile', 'employer-options', 'rb_empoyer_profile_page', 'dashicons-universal-access-alt', 18);

    //remove_menu_page('edit.php');
    add_action("load-{$setting_page}", 'rb_load_options_page');
    add_action("load-{$employer_page}", 'rb_load_employer_profile_page');
    add_action("load-{$payment_page}", 'rb_load_payment_option_page');
}

function purchase_order_items_page() {
    if (filter_input(INPUT_GET, 'type') == 'Project') {
        admin_ctm_purchase_order_items_project_page();
    } else {
        admin_ctm_purchase_order_items_page();
    }
}
