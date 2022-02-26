<?php
include_once "SimpleXLSX.php";

function import_client() {

    global $wpdb;
    if (isset($_POST['import'])) {

        $xlsx = SimpleXLSX::parse($_POST['file_path']);
        if (!empty($xlsx)) {
            $i = 0;
            foreach ($xlsx->rows() as $elt) {
                if ($i > 0) {
                    $name = $elt[0];
                    $email = $elt[1];
                    $email2 = $elt[2];
                    $phone = $elt[3];
                    $phone2 = $elt[4];
                    $address = $elt[5];
                    $trn = $elt[6];
                    $city = $elt[7];
                    $country = $elt[8];
                    $created_at = current_time('mysql');
                    $updated_at = current_time('mysql');

                    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_clients WHERE email='{$email}'");

                    $data = ['name' => $name, 'email' => $email, 'email2' => $email2, 'phone' => $phone, 'phone2' => $phone2, 'address' => $address, 'trn' => $trn,
                        'city' => $city, 'country' => $country, 'created_at' => $created_at, 'updated_at' => $updated_at];

                    if (empty($exist)) {
                        $wpdb->insert("{$wpdb->prefix}ctm_clients}", $data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',]);
                    } else {
                        
                    }
                }
                $i++;
            }
            $msg = 'Client import has been done successfully';
        }
    }
    ?>
    <style type="text/css">
        #tbl-excel td,th {
            border: 1px solid rgb(190, 190, 190);
            padding: 10px;
            overflow: hidden; 
            text-overflow: ellipsis; 
            word-wrap: break-word;
        }

        #tbl-excel td {
            text-align: center;
        }

        #tbl-excel tr:nth-child(even) {
            background-color: #eee;
        }

        table#tbl-excel {
            table-layout:fixed;
            border-collapse: collapse;
            border: 2px solid rgb(200, 200, 200);
            letter-spacing: 1px;
            font-family: sans-serif;
            font-size:.8rem;
        }
        span{color:#ff0000;}
        .error{background: red;color:#fff;}
    </style>
    <div class='wrap'>
        <div id="icon-users" class="icon32"></div><h1 class="wp-heading-inline"><?php _e(' Import Client'); ?></h1>
        <a href="<?= site_url() ?>/wp-cache/excel/sample.xlsx" class="page-title-action" download>Sample Excel</a><br />
    </div>
    <br/><br/>
    <?= !empty($msg) ? '<div style="color:green;">' . $msg . '</div>' : ''; ?>
    <div>
        <form action="" name="client" method="POST" id="client" enctype="multipart/form-data">
            <table>
                <tr>
                    <td><input type="file" name="import_file" required="required" ></td>
                    <td><button type="submit" class="button-primary" style="width:100%">Upload File</button></td>
                    <td><a href="<?= admin_url('admin.php?page=import-client') ?>" class="button-secondary" style="width:100%">Reset</a></td>
                </tr>
            </table>
        </form>

        <?php
        if (!empty($_FILES['import_file'])) {

            $targetPath = ABSPATH . "wp-cache/excel/" . $_FILES['import_file']['name'];
            move_uploaded_file($_FILES['import_file']['tmp_name'], $targetPath);

            if ($xlsx = SimpleXLSX::parse($targetPath)) {
                echo '<hr/><table id="tbl-excel"><tbody>';
                $i = 0;
                foreach ($xlsx->rows() as $elt) {
                    if ($i == 0) {
                        echo "<tr>"
                        . "<th>Name</th>"
                        . "<th>Email</th>"
                        . "<th>Email2</th>"
                        . "<th>Phone</th>"
                        . "<th>Phone2</th>"
                        . "<th>Address</th>"
                        . "<th>TRN</th>"
                        . "<th>City</th>"
                        . "<th>Country</th>"
                        . "</tr>";
                    } else {
                        $name = $elt[0];
                        $email = $elt[1];
                        $email2 = $elt[2];
                        $phone = $elt[3];
                        $phone2 = $elt[4];
                        $address = $elt[5];
                        $trn = $elt[6];
                        $city = $elt[7];
                        $country = $elt[8];

                        $tds = "<tr>"
                                . "<td>{$name}</td>"
                                . "<td>{$email}</td>"
                                . "<td>{$email2}</td>"
                                . "<td>{$phone}</td>"
                                . "<td>{$phone2}</td>"
                                . "<td>{$address}</td>"
                                . "<td>{$trn}</td>"
                                . "<td>{$city}</td>"
                                . "<td>{$country}</td>"
                                . "</tr>";
                    }
                    $i++;
                }
                echo "</tbody></table>";
            } else {
                echo SimpleXLSX::parseError();
            }
            ?>
            <hr/>
            <?php if (empty($error)) { ?>
                <form method="post">
                    <input type="hidden" name="file_path" value="<?= $targetPath ?>" />
                    <button type="submit" name="import" class="button-primary" <?= !empty($error) ? 'disabled' : '' ?>>Import</button>
                </form>
                <?php
            }
        }
        ?>
    </div>
    <?php
}
