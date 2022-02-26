<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<table class="form-table">
    <tr>
        <td colspan="2"><label>ROCHE BOBOIS DUBAI PROFILE</label></td>
    </tr>
    <tr>
        <td>Trade Name</td>
        <td>
            <input type="text" name="rb_profile[trade_name]" 
                   value="<?= $rb_employer_options['rb_profile']['trade_name'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>Legal Type</td>
        <td>
            <input type="text" name="rb_profile[legal_type]" value="<?= $rb_employer_options['rb_profile']['legal_type'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>License Number</td>
        <td>
            <input type="text" name="rb_profile[license_number]" value="<?= $rb_employer_options['rb_profile']['license_number'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Membership No</td>
        <td>
            <input type="text" name="rb_profile[membership_no]" value="<?= $rb_employer_options['rb_profile']['membership_no'] ?? '' ?>" style="width:100%"  />
        </td>
    </tr>
    <tr><td>Registration No</td>
        <td>
            <input type="text" name="rb_profile[registration_no]" value="<?= $rb_employer_options['rb_profile']['registration_no'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Registration Date</td>
        <td>
            <input type="date" name="rb_profile[registration_date]" value="<?= $rb_employer_options['rb_profile']['registration_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Address</td>
        <td>
            <textarea type="text" name="rb_profile[address]" style="width:100%" rows="4" ><?= $rb_employer_options['rb_profile']['address'] ?? '' ?></textarea>
        </td>
    </tr>
    <tr><td>TRN</td>
        <td>
            <input type="text" name="rb_profile[trn]" value="<?= $rb_employer_options['rb_profile']['trn'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Import Code</td>
        <td>
            <input type="text" name="rb_profile[import_code]" value="<?= $rb_employer_options['rb_profile']['import_code'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Export Code</td>
        <td>
            <input type="text" name="rb_profile[export_code]" value="<?= $rb_employer_options['rb_profile']['export_code'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>MOL</td>
        <td>
            <input type="text" name="rb_profile[mol]" value="<?= $rb_employer_options['rb_profile']['mol'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>License Activities</td>
        <td>
            <textarea type="text" name="rb_profile[license_activities]" style="width:100%" rows="10" ><?= $rb_employer_options['rb_profile']['license_activities'] ?? '' ?></textarea>
        </td>
    </tr>
    <tr><td>Phone Number</td>
        <td>
            <input type="text" name="rb_profile[phone_number]" value="<?= $rb_employer_options['rb_profile']['phone_number'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Mobile Number</td>
        <td>
            <input type="text" name="rb_profile[mobile_number]" value="<?= $rb_employer_options['rb_profile']['mobile_number'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Sole Proprietor</td>
        <td>
            <input type="text" name="rb_profile[sole_proprietor]" value="<?= $rb_employer_options['rb_profile']['sole_proprietor'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td>Nationality</td>
        <td>
            <input type="text" name="rb_profile[nationality]" value="<?= $rb_employer_options['rb_profile']['nationality'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
</table>
