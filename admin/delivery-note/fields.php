<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<table cellspacing='5' cellpadding='5' style="width: 100%">
    <tr>
        <td colspan="4">Delivery Address<br/>
            <textarea type="text" name='address' class="form-control"><?= !empty($dn->address) ? $dn->address : $client->address ?></textarea>
        </td>
    </tr>
    <tr>
        <td>Receiver's Name<br/><input type="text" name='receiver_name' class="form-control" required="required" value="<?= $dn->receiver_name??$client->name ?>"></td>
        <td>Delivered By<br/><input type="text" name='delivered_by' class="form-control" required="required" value="<?= $dn->delivered_by ?>"></td>
        <td colspan="2">Delivery Date<br/><input type="date" name='delivery_date' class="form-control" required="required" value="<?= $dn->delivery_date ?>"></td>
    </tr>
    <tr><td colspan="4">Delivery Time On Site</td></tr>
    <tr>
        <td>From<br/><?= get_schedule_from('delivery_time_from', $dn->delivery_time_from); ?></td>
        <td>To<br/><?= get_schedule_to('delivery_time_to', $dn->delivery_time_to); ?></td>
        <td style="width:150px">
            Delivery Status:<br/>
            <select name='status' class="form-control" <?= $dn->status == 'DELIVERED' || $dn->status == 'RETURNED' ? 'disabled' : '' ?>>
                <option value="Pending">Pending</option>
                <option value="DELIVERED" <?= $dn->status == 'DELIVERED' ? 'selected' : '' ?>>DELIVERED</option>
                <option value="CANCELLED" <?= $dn->status == 'CANCELLED' ? 'selected' : '' ?>>CANCELLED</option>
                <option value="RETURNED" <?= $dn->status == 'RETURNED' ? 'selected' : '' ?>>RETURNED</option>
            </select>
        </td>
        <td><br/>
            <input type="hidden" value="<?= $dn->id ?>" name="dn_id" />
            <button type="submit" name="delivery_note" value="1" class="btn btn-primary btn-block btn-sm">Update</button>
        </td>
    </tr>
    
    <tr>
        <td colspan="2">Already Selected Slots<br/>
            <table cellspacing='5' cellpadding='5' style="width: 120px">
                <?php
                foreach (get_schedule_slots($dn->delivery_date) as $value) {
                    echo "<tr><td>$value->delivery_time_from</td><td> - </td><td>$value->delivery_time_to</td></tr>";
                }
                ?>
            </table>
        </td>
        <td colspan="2">
            <button type="submit" name="tax_invoice" value="1" class="btn btn-primary btn-block btn-sm">Modify Tax invoice</button>
        </td>
    </tr>
</table>