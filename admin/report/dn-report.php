<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_dn_report_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-01');
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-t');


    $whr_from_date = !empty($from_date) ? " created_at >='{$from_date}'" : " created_at=" . date('Y-m-01');
    $whr_to_date = !empty($to_date) ? " created_at <='{$to_date}'" : " created_at=" . date('Y-m-t');
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn WHERE $whr_from_date AND $whr_to_date GROUP BY client_id");
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
        #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
        #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
        #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
        #tbl-filter{background: #fff; border: 20px solid #fff;}
    </style>
    <div class="wrap">
        <h1 class="wp-heading-inline">Delivery Note Report</h1>
        <br/>
        <form method="get">
            <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
            <table id="tbl-filter" cellpadding="5" cellspacing="5" >
                <tr>
                    <td>From Date: <br/>
                        <input type="date" name="from_date"  value="<?= $from_date ?>" style="width:175px"  required />
                    </td>
                    <td>To Date:  <br/>
                        <input type="date" name="to_date" value="<?= $to_date ?>" style="width:175px"  required />

                    </td>

                    <td><br/>
                        <button type="submit" name="filter" value="1" class="btn btn-sm btn-primary ">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="admin.php?page=<?= $getdata['page'] ?>" class="btn btn-sm btn-secondary text-white">RESET</a>
                    </td>
                </tr>
            </table>
        </form>
        <br/>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="page-inner-content" class="postbox">


                            <?php
                            $html = "
                                    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                                        h6 span{text-transform:uppercase;}
                                        p{margin-bottom: 0;}
                                        table{empty-cells:hidden;}
                                        table tr td{font-size:12px;font-family:'Tahoma'}
                                        table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                                        .text-center{text-align:center;}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .bg-light{background:##eaedf1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .attachment-large{width:50px;height: auto;}
                                        ul{margin: auto;padding: 0 15px;}
                                        ul#terms{ padding: 0; margin: 0;} 
                                        ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
                                    </style>
                                    
                                    <div id='welcome-to-aquila' class='postbox'>
                                        <div class='inside' style='max-width:800px;margin:auto'>
                                            <br/>";
                            $table = "<table width='800'>
                                                <tr valign='top'>
                                                    <td colspan=2 style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' style='text-align:center'><br/><br/>
                                                        <h6><span style='font-size:22px;'>DELIVERY NOTE REPORT</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                    <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>";

                            $table .= "<tr class=bg-blue>
                                      <td colspan='6'><b>Date</b></td></td>
                                      </tr>
                                      <tr>
                                        <td class='text-center'><b>From Date</b></td>
                                        <td class='text-center' colspan=2><b>" . rb_date($from_date) . "</b></td>
                                        <td class='text-center'><b>To Date</b></td>
                                        <td class='text-center' colspan=2><b>" . rb_date($to_date) . "</b></td>
                                      </tr>
                                      ";


                            $table .= "<tr><td  colspan='6'><br/></td></tr>
                                      <tr class='text-center bg-blue'>
                                        <td class='text-center'><b>DN ID</b></td>
                                        <td class='text-center'><b>QTN</b></td>
                                        <td class='text-center'><b>Type</b></td>
                                        <td class='text-center'><b>Client Name</b></td>
                                        <td class='text-center' style='width:75px'><b>DN Date</b></td>
                                        <td class='text-center' style='width:150px'><b>Date</b></td>
                                      </tr>
                                      <tr><td colspan='6'><br/></td></tr>";


                            foreach ($results as $value) {
                                $qtn = get_revised_no($value->quotation_id);
                                $type = get_quotation($value->quotation_id, 'type');
                                $table .= "<tr class='text-center'>
                                        <td class='text-center'>{$value->id}</td>
                                        <td class='text-center'>{$qtn}</td>
                                        <td class='text-center'>" . get_client($value->client_id, 'name') . "</td>
                                        <td class='text-center'>$type</td>
                                        <td class='text-center'>" . rb_date($value->delivery_date) . "</td>
                                        <td class='text-center'>" . rb_datetime($value->updated_at) . "</td>
                                      </tr>";
                            }

                            $table .= "<tr><td colspan='6'><br/></td></tr></table>";

                            $html .= $table;

                            $html .= "<br/><br/><br/><br/>


                                    </div>
                                    </div>";
                            echo $html;
                            $pdf_file = make_pdf_file_name("delivery_note_report_{$from_date}_{$to_date}.pdf")['path'];
                            ?>

                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>v";
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'delivery_note_report', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {

        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
}
