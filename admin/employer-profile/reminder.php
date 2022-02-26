<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<table class="form-table" border="1" style="border-collapse: collapse">
    <tr class="bg-blue">
        <td colspan="2"><strong>REMINDER DAYS</strong></td>
    </tr>
    <tr class="bg-blue">
        <td><strong>Type</strong></td><td><strong>Days</strong></td>
    </tr>
    <tr>
        <td>COMMERCIAL LICENSE</td>
        <td>
            <input type="number" name="rb_reminder[commercial_license]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['commercial_license'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>ESTABLISHMENT CARD</td>
        <td>
            <input type="number" name="rb_reminder[establishment_card]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['establishment_card'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>CUSTOMS CODE</td>
        <td>
            <input type="number" name="rb_reminder[customs_code]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['customs_code'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>LEASE CONTRACT</td>
        <td>
            <input type="number" name="rb_reminder[lease_contract]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['lease_contract'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>VAT RETURNS FILING</td>
        <td>
            <input type="number" name="rb_reminder[vat_returns_filing]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['vat_returns_filing'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>ANNUAL MAINTAINANCE CONTRACT</td>
        <td>
            <input type="number" name="rb_reminder[amc]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['amc'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>CONTAINER ARRIVAL</td>
        <td>
            <input type="number" name="rb_reminder[container_arrival]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['container_arrival'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Vehicle Insurance Policy</td>
        <td>
            <input type="number" name="rb_reminder[vehicle_insurance]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['vehicle_insurance'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Vehicle Ads. Permit Ref</td>
        <td>
            <input type="number" name="rb_reminder[vehicle_permit_no]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['vehicle_permit_no'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Visa</td>
        <td>
            <input type="number" name="rb_reminder[visa]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['visa'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Passport</td>
        <td>
            <input type="number" name="rb_reminder[passport]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['passport'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Labor Card</td>
        <td>
            <input type="number" name="rb_reminder[labor_card]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['labor_card'] ?? '' ?>" />
        </td>
    </tr>
    <tr>
        <td>Insurance</td>
        <td>
            <input type="number" name="rb_reminder[insurance]" min="1" max=90
                   value="<?= $rb_employer_options['rb_reminder']['insurance'] ?? '' ?>" />
        </td>
    </tr>


</table>
