<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<table class="form-table">
    <tr>
        <td colspan="3"><strong>VEHICLE REGISTRY</strong><br/><br/></td>
    </tr>
    <?php for ($i = 0; $i <= count($rb_employer_options['rb_vehicle']); $i++) { ?>
        <tr>
            <td rowspan="10" style="vertical-align: top">
                <span style="padding: 5px 10px; border: 1px solid #000; border-radius: 50%;"><?= $i + 1 ?></span>
            </td>
            <td>Model</td>
            <td>
                <input type="text" name="rb_vehicle[<?= $i ?>][model]" placeholder="Model"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['model'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Plate #</td>
            <td>
                <input type="text" name="rb_vehicle[<?= $i ?>][plate]" placeholder="Plate #"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['plate'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Mulkiya</td>
            <td>
                <input type="date" name="rb_vehicle[<?= $i ?>][mulkiya]"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['mulkiya'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Insurance Policy #</td>
            <td>
                <input type="text" name="rb_vehicle[<?= $i ?>][policy_no]" placeholder="Insurance Policy #"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['policy_no'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>From</td>
            <td>
                <input type="date" name="rb_vehicle[<?= $i ?>][from_date]"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['from_date'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>To</td>
            <td>
                <input type="date" name="rb_vehicle[<?= $i ?>][to_date]" 
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['to_date'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Insurance Company</td>
            <td>
                <input type="text" name="rb_vehicle[<?= $i ?>][company]" placeholder="Insurance Company"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['company'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Ads. Permit Ref #</td>
            <td>
                <input type="text" name="rb_vehicle[<?= $i ?>][permit_no]" placeholder="Ads. Permit Ref #"
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['permit_no'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>Start Date</td>
            <td>
                <input type="date" name="rb_vehicle[<?= $i ?>][start_date]" 
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['start_date'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr>
            <td>End Date</td>
            <td>
                <input type="date" name="rb_vehicle[<?= $i ?>][end_date]" 
                       value="<?= $rb_employer_options['rb_vehicle'][$i]['end_date'] ?? '' ?>" style="width:100%" />
            </td>
        </tr>
        <tr><td colspan="3"><br/></td></tr>
    <?php } ?>

</table>
