<?php
include"top.php";
include"header.php";
include "../../classes/admin_class.php";
$sort_fieldname = 'a.id desc';
$table = "application_table a";
$table2="personal_details b";
$table3 = "user c";
$admin_pagelist = 10;
$search_para = "Show All Records";
$action = $_REQUEST['action'];
//
$update = new admin_class();
if(isset($_SESSION['adminid'])){
    
    if($_SESSION['adminid'] == "1"){
if ($action == "delete") {
    $inputId = $_REQUEST['id'];
    $query = "UPDATE `application_table` SET `flag`=8 where `id`='" . $inputId . "'";
    mysql_query($query)or die(mysql_error());
    header("location:" . $_SERVER['PHP_SELF']);
    exit();
}
if ($action == "changeFlag") {
    $inputId = $_REQUEST['id'];
    $inputFlag = $_REQUEST['flag'];
//$inputId = filter_input(INPUT_GET, 'id');
    $query = "UPDATE `application_table` SET `flag`='" . $inputFlag . "' where `id`='" . $inputId . "'";
    mysql_query($query)or die(mysql_error());
	if($inputFlag == 5)
	{
	$query = "UPDATE `application_rank_status` SET `admit_flag`= 1 "
	. " where application_no =(select application_no from application_table where `id`='" . $inputId . "')"
	. " and rank_category = (select p.Category from application_table a,personal_details p where a.`id`='" . $inputId . "' AND a.user_id = p.user_id )";
	mysql_query($query)or die(mysql_error());
	if(mysql_affected_rows()<1)
	{
		$query = "UPDATE `application_rank_status` SET `admit_flag`= 1 " .
		" where application_no =(select application_no from application_table where `id`='" . $inputId . "')" .
		" AND rank_category = 'GEN'";
		mysql_query($query)or die(mysql_error());
	}
	}    //q("DELETE FROM dt_epin_request WHERE id='$id'");
    header("location:" . $_SERVER['PHP_SELF']);
    $update->sendMail($inputId, 5, null, null, null, null);
    exit();
}


/* Search Block */

$where_field.=' and 1 ';
if ($action == 'search') {
    //print_r(extract($_POST));

    if (isset($_POST['start_dt']) && $_POST['start_dt'] <> "") {
        $start_dt = $_POST['start_dt'];
        $where_field.=" AND a.submit_date>='" . date("Y-m-d", strtotime(($_POST['start_dt']))) . "'";
    }
    if (isset($_POST['to_dt']) && $_POST['to_dt']) {
        $to_dt = $_POST['to_dt'];
        $where_field.=" AND a.submit_date<='" . date("Y-m-d", strtotime(($_POST['to_dt']))) . "'";
    }

    if (isset($_POST['appNo']) && $_POST['appNo']) {
        //echo "fg";
        $appNo = $_POST['appNo'];
        $where_field.=" AND a.Application_No like '%" . $_POST['appNo'] . "%'";
    }
}
/* Make Where Clause */

$where_field = " WHERE 1 and a.flag=3" . $where_field;
?>
<script>
    function changeFlag(inputFlag, id) {
        //Call Ajax
        alert("CCCC" + inputFlag);
        alert(id);

        $("#button_Panel").load("ajax/change_Application_Status.php?flag=" + inputFlag + "&id=" + id, function(responseTxt, statusTxt, xhr) {
            if (statusTxt == "success") {
                alert(responseTxt);
                document.getElementById("button_Panel").innerHTML = responseTxt;

            } else if (statusTxt == "error") {
                alert("Error: " + xhr.status + ": " + xhr.statusText);
            }
        });

    }
</script>

<table cellpadding="0" cellspacing="0" width="100%" align="center" border="0" >	
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" width="40%" align="left">
                <td width="90%" class="caption">: Advance Search</td>
                <td class="endcap"></td>
            </table>
        </td>
    </tr>
    <tr>
        <td class="mainarea">		
            <table cellpadding="0" cellspacing="0" align="left" width="100%" >
                <form action="<?= $_SERVER['PHP_SELF'] ?>" style="margin:0px; padding:0px " id="f1" name="f1" method="post" >				
                    <input type="hidden" name="action" value="search" />
                    <tr>

                        <td>
                            <table width="100%">
                                <tr>
                                    <td>
                                        Application No : <input type="text" name="appNo" id="appNo" value="<?= $appNo ?>"/>			
                                    </td><td>
                                        From Date : <input type="text" name="start_dt" id="start_dt" value="<?= $start_dt ?>" size="10" readonly="readonly" /> <img src="images/img.gif" id="date1" alt="Click to pickup date"  border="0"/>
                                    </td><td>To Date : <input type="text" name="to_dt" id="to_dt" value="<?= $to_dt ?>" size="10" readonly="readonly" /> <img src="images/img.gif" id="date2" alt="Click to pickup date"  border="0"/>
                                    </td><td>
                                        <input class="form_button" type="submit" name="submit" value="Search">
                                    </td>
                                <tr>
                            </table>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
</table>
<br />
<div id="button_Panel">
<table cellpadding="0" cellspacing="0" width="100%" align="center" border="0" >	
    <?
    //PAGINATION  SCRIPT HERE*********************************
    $page_num=$_GET["page_num"];
    if($page_num==""){
    $page_num=1;
    }
    if(!isset($from))
    {
    $from=0;
    }
//$sql1 = " SELECT * FROM $table,$table2,$table3 WHERE a.user_id=b.user_id AND a.user_id=c.user_id order by $sort_fieldname ";
    $sql=q(" SELECT * FROM $table,$table2,$table3 WHERE a.user_id=b.user_id AND a.user_id=c.user_id order by $sort_fieldname ");
//echo $sql1;
    //$sql=q(" SELECT * FROM dt_epin_request order by user_id desc ");

    $rec_count=(int)nr($sql);
    ?>			
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" width="100%" align="left">
                <td width="40%" class="caption">: Admission List </td>
                <td class="endcap"></td>
                <td style="padding-left:10px;" align="right"></td>
                <td align="left"></td>
            </table>
        </td>
    </tr>    
    <tr>
        <td class="mainarea" align="Left">		
            <table cellpadding="5" cellspacing="5" align="center" width="98%" border="1" style="border-collapse:collapse;" bordercolor="#cacaca" >

                <?	
                $page_count = 0;
                $show_rec_per_page=$admin_pagelist;//$config["listings_per_page"];

                if($show_rec_per_page >= $rec_count)
                {
                $search_query = "SELECT * FROM $table,$table2,$table3  ".$where_field." AND a.user_id=b.user_id AND a.user_id=c.user_id ORDER BY $sort_fieldname";
                }
                else
                {
                $page_count = $rec_count / $show_rec_per_page;
                $page_count = ceil($page_count);
                //settype($page_num, "int");
                //if(!$page_num){$page_num++;}	

                if($page_num > $page_count)
                {
                $page_num = $page_count;		
                }	
                $from = ($page_num - 1) * $show_rec_per_page;
                $search_query = "SELECT * FROM $table,$table2,$table3  ".$where_field." AND a.user_id=b.user_id AND a.user_id=c.user_id ORDER BY $sort_fieldname limit $from, $show_rec_per_page";

                }	
              //  echo $search_query;
                $tot_rec=(int)nr(q($search_query));
                if($tot_rec<>0)
                {

                ?>

                <tr>
                    <td width="2%" class="colhead">#</td>
                    <td width="10%" class="colhead"> Name </td>
                    <td width="5%" class="colhead">Mobile</td>	
                    <td width="8%" class="colhead">Application Fee </td>
                   <td width="8%" class="colhead">Admission Fee </td>		
                    <td width="8%" class="colhead">Roll-Index No</td>
                    <td width="8%" class="colhead">Pin</td>
                    <td width="8%" class="colhead">Application No</td>	
                    <td width="8%" class="colhead">Submit Date</td>
                    <td width="8%" align="center"><strong>STATUS</strong></td>
                    <td width="8%" align="center"><strong>ACTION</strong></td>
                </tr>

                <? $q_arr=q($search_query);
                //echo $page_num;
                if($page_num==1){
                $slno=1;
                }else{
                echo $slno=1+($admin_pagelist*($page_num-1));
                }
                while($f_arr=f($q_arr))
                {

                 
                ?>
     <?php // echo 'ID:'.$f_arr['id'];?>
                <tr bgcolor="#ffffff" onMouseOver=bgColor = "#EFF7FF" onMouseOut=bgColor = "#ffffff">
                    <td style="padding-left:5px;"><? echo $slno;?></td>

                    <td style="padding-left:5px;"><?php echo stripslashes($f_arr['fname']) . " " . stripslashes($f_arr['lname']); ?></td>
                    <td style="padding-left:5px;"><? echo stripslashes($f_arr['mobile']);?></td>
                    <td style="padding-left:5px;"><? echo stripslashes($f_arr['Application_Fee']);?></td>
                   <td style="padding-left:5px;"><? echo stripslashes($f_arr['Admission_Fee']);?></td>
                    <td style="padding-left:5px;"><?php
$appNoTest = $f_arr['Application_No'];
$user_id =  $f_arr['user_id'];
$fetch_app_no = mysql_fetch_array(mysql_query("select * from academic_details  where User_id='" . $user_id . "' limit 1"));
echo stripslashes($fetch_app_no['Roll_Index_No']);
?></td>
                    <td style="padding-left:5px;"><? echo stripslashes($f_arr['ZIP_PIN']);?></td>
                    <td style="padding-left:5px;"><? echo stripslashes($f_arr['Application_No']);?></td>
                    <td style="padding-left:5px;"><? echo stripslashes(date("d-M-Y",strtotime($f_arr['submit_date'])));?></td>
                    <td style="padding-left:5px;" align="center" >
<?php
//Flag 1 - DRAFT
if ($f_arr['flag'] == 1) {
    ?>
                            WAITING                              
<?php } else if ($f_arr['flag'] == 2) { ?>
                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>?action=changeFlag&id=<?php echo $f_arr['id']; ?>&flag=9" onclick="return confirm('Want to Accept?');">Accept</a>

                        <?php } else if ($f_arr['flag'] == 3) { ?>
                            RANKED


                        <?php } else if ($f_arr['flag'] == 4) { ?>
                                   <?php  if($f_arr['Admission_Fee'] ==""){?>
                                          ADMISSION FEE DUE
                                  <?php  }else{?>

                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>?action=changeFlag&id=<?php echo $f_arr['id']; ?>&flag=5" onclick="return confirm('Want to Admit?');">CONFIRM ADMISSION</a>
  <?php } ?>
                        <?php }else if ($f_arr['flag'] == 8) { ?>
                                DELETED
                        <?php } else if ($f_arr['flag'] == 9) { ?>
                            READY


                        <?php } else if ($f_arr['flag'] == 7) { ?>
                            CANCELLED


                        <?php } else if ($f_arr['flag'] == 5) { ?>
                            ADMITTED


                        <?php } 
                        
                        ?>

                    </td>
                    <td align="center">
                    
                     <?php  if($f_arr['Admission_Fee'] ==""){?>
                       ADMISSION FEE DUE
                <?php  }else{?>
<a href="<?php echo $_SERVER['PHP_SELF'] ?>?action=changeFlag&id=<?php echo $f_arr['id']; ?>&flag=5" onclick="return confirm('Want to Admit?');">CONFIRM ADMISSION</a>
         <?php } ?>
                    <!-- <a href="view.php?action=view&id=<?php echo $f_arr['id']; ?>">View</a> -->
                        
                    <?php if ($f_arr['flag'] != 8){ ?>
                        ||<a href="<?php echo $_SERVER['PHP_SELF'] ?>?action=delete&id=<?php echo $f_arr['id']; ?>" onclick="return confirm('Cancel this Applicatiopn?');">Cancel</a>
                
                    <?php }?>       </td></tr>
                <tr id="tr_<?php echo $f_arr['id']; ?>" style="display:none">
                    <td colspan="16" align="right" style="padding-right:50px;" id="td_<?php echo $f_arr['id']; ?>" ><span><strong>Generated E-PIN:&nbsp;</strong></span><span id="sp_pin_<?php echo $f_arr['id']; ?>" style="color:#FF0000; font-weight:bold; font-size:12px;"></span></td>
                </tr>
                <?  $slno++; }?>

                <tr>
                    <td align="center" colspan="16">
                        <table align="center" width="100%" class="page_link_table" >	
                            <tr><td align=center> 
                                    <? 
                                    $npages = $page_count;
                                    $p = $page_num;
                                    if ($npages > 1)
                                    {
                                    $params = "";
                                    $pages = "";
                                    foreach($_REQUEST as $k => $v)
                                    {
                                    if($k == "page_num") continue;
                                    $params .= "$k=$v&";
                                    }

                                    $pages .= "Page: ";
                                    if ($p != 1) $pages .= '<a href="'.$_SERVER['PHP_SELF'].'?'.$params.'page_num='.($p-1).'"><b>Previous</b></a>&nbsp;&nbsp;';

                                    $lend = floor($p/10)*10;
                                    if ($lend < 1) $lend = 1;
                                    if ($npages > 19)
                                    {
                                    $hend = $lend + 19;
                                    if ($hend > $npages) $hend = $npages;
                                    }
                                    else $hend = $npages;				
                                    for ($i = $lend; $i <= $hend; $i++)
                                    {
                                    if ($i == $p) $pages .= $i."&nbsp;";
                                    else $pages .= '<a href="'.$_SERVER['PHP_SELF'].'?'.$params.'page_num='.$i.'">'.$i.'</a>&nbsp;';
                                    }				
                                    if ($p != $npages) $pages .= '&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?'.$params.'page_num='.($p+1).'"><b>Next</b></a>&nbsp;&nbsp;';
                                    }
                                    ?>
                                </td></tr>
                            <? if($pages) 
                            { ?>				 
                            <tr>
                                <td   align="center" colspan="5"><?= $pages ?></td>
                            </tr>
                            <tr>
                                <td  align="center" colspan="5"></td>
                            </tr>
                            <? }?>
                        </table>		
                        <input type="hidden" name="pageno" value="<? echo $p;?>">
                    </td>
                </tr>  
                <? }else{
                $ERmsg="No record found!";
                }?>   
            </table>
        </td>
    </tr>
</table>
</div>
<script type="text/javascript">
    function generate_epin(user_id)
    {
        if (confirm('Generate E PIN now?'))
        {
            $("#tr_" + user_id).show();
            $('#sp_pin_' + user_id).load('<?php echo "ajx_generate_epin.php?user_id="; ?>' + user_id);


        }

    }

    Calendar.setup({
        inputField: "start_dt",
        ifFormat: "<?= CAL_DF ?>",
        button: "date1",
        align: "",
        singleClick: true
    });

    Calendar.setup({
        inputField: "to_dt",
        ifFormat: "<?= CAL_DF ?>",
        button: "date2",
        align: "",
        singleClick: true
    });


</script>
<script src="../../jquery-ui-1.11.3/external/jquery/jquery.js"></script>
<script>


function changeUserFlag(inputFlag, id){
    //Call Ajax
    alert(inputFlag);
    //alert(id);
    
    $("#button_Panel").load("ajax/change_Application_Status.php?flag="+inputFlag+"&id="+id,function(responseTxt,statusTxt,xhr){
		  if(statusTxt=="success"){
                        //alert(responseTxt);
			document.getElementById("button_Panel").innerHTML=responseTxt;
			
			}else if(statusTxt=="error"){
				alert("Error: "+xhr.status+": "+xhr.statusText);
			}
		});

}
</script>
<? include "footer.php";?>	
<?php        
    }
}else {
    echo("Invalid Session");
    
}
?>
