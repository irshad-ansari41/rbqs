<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$fileList = glob(ABSPATH . 'wp-content/mysql-backup/*');
?>
<table class="form-table" style="border-collapse:collapse;">
    <tr>
        <th><label>Database Backup:</label></th>
        <td><br/>
            <table border="1" style="border-collapse:collapse;">
                <tr><td>#</td><td>File</td><td>Download</td></tr>
                <?php
                $i = 1;
                foreach ($fileList as $filename) {
                    $pathinfo = pathinfo($filename);
                    $file_path = urlencode($pathinfo['dirname'] . $pathinfo['basename']);
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $pathinfo['basename'] ?></td>
                        <td style="text-align:center">
                            <a href="<?= site_url('wp-content/mysql-backup/' . $pathinfo['basename']) ?>" download>
                                <span class="dashicons dashicons-download"></span></a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>