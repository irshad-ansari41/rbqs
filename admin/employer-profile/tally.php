<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<table class="form-table" border="1" style="border-collapse: collapse">
    <tr class="bg-blue">
        <td colspan="2"><strong>TALLY</strong></td>
    </tr>
    <tr>
        <td>Tally User Name</td>
        <td>
            <input type="text" name="rb_tally[username]" value="<?= $rb_employer_options['rb_tally']['username'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>Password</td>
        <td>
            <input type="text" name="rb_tally[password]" value="<?= $rb_employer_options['rb_tally']['password'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>System Name</td>
        <td>
            <input type="text" name="rb_tally[system_name]" value="<?= $rb_employer_options['rb_tally']['system_name'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>IP</td>
        <td>
            <input type="text" name="rb_tally[ip]" value="<?= $rb_employer_options['rb_tally']['ip'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>Port</td>
        <td>
            <input type="text" name="rb_tally[port]" value="<?= $rb_employer_options['rb_tally']['port'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>SERIAL NO</td>
        <td>
            <input type="text" name="rb_tally[serial_no]" value="<?= $rb_employer_options['rb_tally']['serial_no'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>KEY</td>
        <td>
            <input type="text" name="rb_tally[key]" value="<?= $rb_employer_options['rb_tally']['key'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>

</table>
