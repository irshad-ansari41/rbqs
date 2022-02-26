<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<table class="form-table" border="1" style="border-collapse: collapse">
    <tr class="bg-blue">
        <td colspan="3"><strong>PC PASSWORDS</strong></td>
    </tr>
    <tr class="bg-blue">
        <td><strong>USER ID</strong></td>
        <td><strong>PASSWORD</strong></td>
        <td><strong>USER</strong></td>
    </tr>
    <?php for ($i = 0; $i <= count($rb_employer_options['rb_pc_password']); $i++) { ?>
        <tr>
            <td>
                <input type="text" name="rb_pc_password[<?= $i ?>][user_name]" placeholder="User ID"
                       value="<?= $rb_employer_options['rb_pc_password'][$i]['user_name'] ?? '' ?>" style="width:100%" />
            </td>
            <td>
                <input type="text" name="rb_pc_password[<?= $i ?>][password]"  placeholder="Password"
                       value="<?= $rb_employer_options['rb_pc_password'][$i]['password'] ?? '' ?>" style="width:100%" />
            </td>
            <td>
                <input type="text" name="rb_pc_password[<?= $i ?>][user]"  placeholder="User"
                       value="<?= $rb_employer_options['rb_pc_password'][$i]['user'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
    <?php } ?>
</table>
