<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<table class="form-table">
    <tr>
        <td colspan="2"><strong>FOR RENEWALS</strong><br/><br/></td>
    </tr>
    <tr><td colspan="2">COMMERCIAL LICENSE</td></tr>
    <tr>
        <td>Issue Date</td>
        <td>
            <input type="date" name="rb_renewal[commercial_license][issue_date]"
                   value="<?= $rb_employer_options['rb_renewal']['commercial_license']['issue_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input type="date" name="rb_renewal[commercial_license][expiry_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['commercial_license']['expiry_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>

    <tr><td colspan="2">ESTABLISHMENT CARD</td></tr>
    <tr>
        <td>Issue Date</td>
        <td>
            <input type="date" name="rb_renewal[establishment_card][issue_date]"
                   value="<?= $rb_employer_options['rb_renewal']['establishment_card']['issue_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input type="date" name="rb_renewal[establishment_card][expiry_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['establishment_card']['expiry_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>

    <tr><td colspan="2">CUSTOMS CODE</td></tr>
    <tr>
        <td>Issue Date</td>
        <td>
            <input type="date" name="rb_renewal[customs_code][issue_date]"
                   value="<?= $rb_employer_options['rb_renewal']['customs_code']['issue_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input type="date" name="rb_renewal[customs_code][expiry_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['customs_code']['expiry_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>

    <tr><td colspan="2">LEASE CONTRACT</td></tr>
    <tr>
        <td>Issue Date</td>
        <td>
            <input type="date" name="rb_renewal[lease_contract][start_date]"
                   value="<?= $rb_employer_options['rb_renewal']['lease_contract']['start_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input type="date" name="rb_renewal[lease_contract][end_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['lease_contract']['end_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>

    <tr><td colspan="2">VAT RETURNS FILING</td></tr>
    <tr>
        <td>From Date</td>
        <td>
            <input type="date" name="rb_renewal[vat_returns_filing][from_date]"
                   value="<?= $rb_employer_options['rb_renewal']['vat_returns_filing']['from_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>To Date</td>
        <td>
            <input type="date" name="rb_renewal[vat_returns_filing][to_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['vat_returns_filing']['to_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>Deadline</td>
        <td>
            <input type="date" name="rb_renewal[vat_returns_filing][deadline_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['vat_returns_filing']['deadline_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>
    
    <tr><td colspan="2">ANNUAL MAINTAINANCE CONTRACT</td></tr>
    <tr>
        <td>Start Date</td>
        <td>
            <input type="date" name="rb_renewal[amc][start_date]"
                   value="<?= $rb_employer_options['rb_renewal']['amc']['start_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>
            <input type="date" name="rb_renewal[amc][end_date]" 
                   value="<?= $rb_employer_options['rb_renewal']['amc']['end_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>
    
    <tr><td colspan="2">CONTAINER ARRIVAL</td></tr>
    <tr>
        <td>E. T. A.</td>
        <td>
            <input type="date" name="rb_renewal[container_arrival][arrival_date]"
                   value="<?= $rb_employer_options['rb_renewal']['container_arrival']['arrival_date'] ?? '' ?>" style="width:100%" />
        </td>
    </tr>
    <tr><td colspan="2"><br/></td></tr>

</table>
