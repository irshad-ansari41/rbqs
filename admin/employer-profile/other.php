<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<table class="form-table" border="1" style="border-collapse: collapse">
    <tr class="bg-blue">
        <td colspan="3"><strong>OTHERS</strong></td>
    </tr>
    <tr class="bg-blue">
        <td><strong>ACCOUNT</strong></td>
        <td><strong>USER NAME</strong></td>
        <td><strong>PASSWORD</strong></td>
    </tr>
    <?php for ($i = 0; $i <= count($rb_employer_options['rb_other']); $i++) { ?>
        <tr>
            <td>
                <input type="text" name="rb_other[<?= $i ?>][account]" placeholder="Account"
                       value="<?= $rb_employer_options['rb_other'][$i]['account'] ?? '' ?>" style="width:100%" />
            </td>
            <td>
                <input type="text" name="rb_other[<?= $i ?>][user_name]"  placeholder="User Name"
                       value="<?= $rb_employer_options['rb_other'][$i]['user_name'] ?? '' ?>" style="width:100%" />
            </td>
            <td>
                <input type="text" name="rb_other[<?= $i ?>][password]"  placeholder="Password"
                       value="<?= $rb_employer_options['rb_other'][$i]['password'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
    <?php } ?>
</table>
