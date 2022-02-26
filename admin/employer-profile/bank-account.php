<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<table class="form-table">
    <tr>
        <td colspan="3"><strong>BANK ACCOUNT DETAILS</strong><br/><br/></td>
    </tr>
    <?php for ($i = 0; $i <= count($rb_employer_options['rb_bank_account']); $i++) { ?>
        <tr>
            <td rowspan="7" style="vertical-align: top">
                <span style="padding: 5px 10px; border: 1px solid #000; border-radius: 50%;"><?= $i + 1 ?></span>
            </td>
            <td>Bank</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][bank]" placeholder="Bank"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['bank'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Account Name</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][account_name]" placeholder="Account Name"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['account_name'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Account Number</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][account_number]" placeholder="Account Number"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['account_number'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>IBAN</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][iban]" placeholder="IBAN"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['iban'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Currency</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][currency]" placeholder="Currency"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['currency'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>SWIFT Code</td>
            <td>
                <input type="text" name="rb_bank_account[<?= $i ?>][swift_code]" placeholder="SWIFT Code"
                       value="<?= $rb_employer_options['rb_bank_account'][$i]['swift_code'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr><td colspan="3"><br/></td></tr>
    <?php } ?>

</table>
