<?php
session_start();
require_once('db.class.php');
$tablename = 'trades';
$db = new db_class;
if(!$db->connect()) $db->print_last_error(false);

$workwith=FALSE;
if(ISSET($_GET['cmd'])) {
	switch($_GET['cmd']) {
		case 'edit':
			if($_GET['id'] && is_numeric($_GET['id'])) $workwith=$_GET['id'];
			break;
		default:
			// this is either 'add' or anything else.
			$workwith=FALSE;
	}
}
if($workwith!==FALSE) {
	// we have something here, need to get the info from the database to fill out the form
	$qry = "SELECT id, type, date, price, make, model, tno, cno, serialno, engineno, enghp, ptohp, description, hours, hoursdate, fronttyresize, fronttyreply,
fronttyrecon, reartyresize, reartyreply, reartyrecon, comments, consignname, consignphone, consignfax, consignmobile, consignabn, status FROM trades WHERE id='".$workwith."'";
	$sql = $db->select($qry);
	while($r=$db->get_row($sql)){
		$id=$r['id'];$type=$r['type'];$date=$r['date'];$price=$r['price'];$make=$r['make'];$model = $r['model'];$tno = $r['tno'];$cno = $r['cno'];
		$serialno = $r['serialno'];$engineno = $r['engineno'];$enghp = $r['enghp'];$ptohp = $r['ptohp'];$description = $r['description'];
		$hours = $r['hours'];$hoursdate = $r['hoursdate'];$fronttyresize = $r['fronttyresize'];$fronttyreply = $r['fronttyreply'];$fronttyrecon = $r['fronttyrecon'];$reartyresize = $r['reartyresize'];
		$reartyrecon = $r['reartyrecon'];$reartyreply=$r['reartyreply'];$comments = $r['comments'];$consignname = $r['consignname'];$consignphone = $r['consignphone'];
		$consignfax = $r['consignfax'];$consignmobile = $r['consignmobile'];$consignabn = $r['consignabn'];$status = $r['status'];
	}
	while(substr($fronttyrecon,-1)=='%'){
		$fronttyrecon=substr($fronttyrecon,0,-1);
	}
	while(substr($reartyrecon,-1)=='%'){
		$reartyrecon=substr($reartyrecon,0,-1);
	}
	$contra = substr($consignname,0,1);
	$consignname = substr($consignname,1);
}
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Wholegoods Database - Add/Edit Information</title>
<!--<link rel="stylesheet" href="styles.css" />-->
<style type="text/css">
body {
	background-color:#37a;
	text-align:center;
	font-family:Arial, tahoma, Verdana, sans-serif;
}

table {
	border:1px solid #000;
	margin:10px auto;
	width:650px;
	text-align:left;
	background:#7ae;
	padding:2px;
}
table td {
	font-size:1em;
	font-weight:bold;
	border:1px solid #000;
	border-left:1px solid #999;
	border-top:1px solid #999;
	background:#fff;
	padding:1 2;
}
form .exceeded{color:#e00;}
p {
	font-size:0.9em;
	font-weight:normal;
	padding:1 5;
}
h1 {
	text-align:center;
}
.sclt {
	font-size:1.3em;
	font-weight:bold;
	background:#e2e2e2;
	color:#37a;
}

form .counter{
	font-size:0.8em;
	font-weight:bold;
	color:#37a;
	padding:0 3 0 3;
	margin-left:5px;
	border:1px solid #37a;
	width:30px;
	}
form .warning{color:#600;}	
input, textarea, select {
	border:1px solid #37a;
	background:#e2e2e2;
}
.radio {
	border:1px solid #fff;
	background:none;
}
.button {
	border:1px solid #555;
	background:#37a;
	color:#fff;
	font-weight:bold;
	padding:0 -10;
}
.red {
	background:#f33;
}
textarea {
	padding:2px;
}
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/charCount.js"></script>
<script type="text/javascript"> 
	$(document).ready(function(){	
		$("#price1").charCount({ allowed:40, warning:5 });
		$("#descfirst1").charCount({ allowed:450, warning:30 });
		$("#commentsfirst1").charCount({ allowed:450, warning:30 });
	});
</script>

</head>
<body>
<form method="POST" action="processpdf.php<?php	if($workwith) echo "?edit=$id"; ?>">
<table>
<tr>
	<td colspan="4">
	<h1>Enter Machine Information</h1>
	</td>
</tr>
<tr>
	<td colspan="4">
	<p>Enter the details below.<br />
	If a field has a number next to it, this is the maximum allowable size that your text can be.
	It should count down as you type, and change colour as you approach the limit.</p>
<p align="center"><input type="button" class="button red" onClick="javascript:window.location='index.php';return false;" value="Don't Save" /> or <input type="submit" name="ohnoes" class="button" value="Create PDF & Save"/></p>
	</td>
</tr>
<tr>
<td colspan="4" align="center">Type: 
<select name="type1" class="sclt">
<option value="t" <?php if($type=='t') echo 'selected="selected"'; ?>/>Tractor
<option value="c" <?php if($type=='c') echo 'selected="selected"'; ?>/>Combine
<option value="b" <?php if($type=='b') echo 'selected="selected"'; ?>/>Baler/Haytool
<option value="m" <?php if($type=='m') echo 'selected="selected"'; ?>/>Mower
<option value="s" <?php if($type=='s') echo 'selected="selected"'; ?>/>Sprayer
<option value="i" <?php if($type=='i') echo 'selected="selected"'; ?>/>Implement
<option value="o" <?php if($type=='o') echo 'selected="selected"'; ?>/>Other
</select>
</td>
</tr>
  <tr> 
    <td width="118">Date</td>
	<td width="181"><input type="text" id="date1" name="date1" <?php if($workwith) echo 'value="'.$date.'"'; ?> title="FORMAT: dd.mm.yyyy"/></td>
    <td width="78">Price</td>
    <td width="242"><input type="text" id="price1" name="price1" size="30" <?php if($workwith) echo 'value="'.$price.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Make</td>
    <td width="181"><input type="text" id="make1" name="make1"  <?php if($workwith) echo 'value="'.$make.'"'; ?>/></td>
    <td width="78">T/No</td>
    <td width="242"><input type="text" id="tno1" name="tno1"  <?php if($workwith) echo 'value="'.$tno.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Model</td>
    <td width="181"><input type="text" id="model1" name="model1"  <?php if($workwith) echo 'value="'.$model.'"'; ?>/></td>
    <td width="78">C/No</td>
    <td width="242"><input type="text" id="cno1" name="cno1"  <?php if($workwith) echo 'value="'.$cno.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Serial No</td>
    <td width="181"><input type="text" id="serialno1" name="serialno1"  <?php if($workwith) echo 'value="'.$serialno.'"'; ?>/></td>
    <td width="78">ENG HP</td>
    <td width="242"><input type="text" id="enghp1" name="enghp1"  <?php if($workwith) echo 'value="'.$enghp.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Engine No</td>
    <td width="181"><input type="text" id="engineno1" name="engineno1"  <?php if($workwith) echo 'value="'.$engineno.'"'; ?>/></td>
    <td width="78">PTO HP</td>
    <td width="242"><input type="text" id="ptohp1" name="ptohp1"  <?php if($workwith) echo 'value="'.$ptohp.'"'; ?>/></td>
  </tr>
  <tr>
	<td colspan="4">
	<p>Enter the description below. The counter is located at the bottom right of the input box - this is just a guide and doesn't guarantee that what you enter will fit into the space on the PDF.</p>
	</td>
  </tr>
  <tr> 
    <td width="118">Description</td>
    <td width="501" colspan="3">
	<textarea name="descfirst1" id="descfirst1" rows="10" cols="55"><?php if($workwith) echo $description; ?></textarea>
  </tr>
  <tr> 
    <td width="118">Hours</td>
    <td width="181"><input type="text" id="hours1" name="hours1"  <?php if($workwith) echo 'value="'.$hours.'"'; ?>/></td>
    <td width="78">Date</td>
    <td width="242"><input type="text" id="hoursdate1" name="hoursdate1"  <?php if($workwith) echo 'value="'.$hoursdate.'"'; ?> title="FORMAT: dd.mm.yyyy"/></td>
  </tr>
  <tr>
  <td colspan="4">
	<table>
	<tr>
		<td>Tyres</td>
		<td>Size</td>
		<td>Ply</td>
		<td>%</td>
	</tr>
	<tr>
		<td>Front</td>
		<td><input type="text" id="tyresfrontsize1" size="50" name="tyresfrontsize1" <?php if($workwith) echo 'value="'.$fronttyresize.'"'; ?>/></td>
		<td><input type="text" id="tyresfrontply1" size="5" name="tyresfrontply1" <?php if($workwith) echo 'value="'.$fronttyreply.'"';?>/></td>
		<td><input type="text" id="tyresfrontpercent1" size="10" name="tyresfrontpercent1" <?php if($workwith) echo 'value="'.$fronttyrecon.'"'; ?>/></td>
	</tr>
	<tr>
		<td>Rear</td>
		<td><input type="text" id="tyresrearsize1" size="50" name="tyresrearsize1" <?php if($workwith) echo 'value="'.$reartyresize.'"'; ?>/></td>
		<td><input type="text" id="tyresrearply1" size="5" name="tyresrearply1" <?php if($workwith) echo 'value="'.$reartyreply.'"';?>/></td>
		<td><input type="text" id="tyresrearpercent1" size="10" name="tyresrearpercent1" <?php if($workwith) echo 'value="'.$reartyrecon.'"'; ?>/></td>
	</tr>
	</table>
	</td>
  </tr>
  
  <tr>
	<td colspan="4">
	<p>Below is the text box for any comments about this trade. As above with the Description, the counter is not a guarantee that your text will fit into the space provided - it is only a guide.</p>
	</td>
  </tr>
  <tr> 
    <td width="118">Comments</td>
    <td width="501" colspan="3">
	<textarea id="commentsfirst1" name="commentsfirst1" rows="8" cols="55"><?php if($workwith) echo $comments; ?></textarea>
	<!--<input type="text" name="commentsfirst1" id="commentsfirst1" size="70"<?php if($workwith) echo 'value="'.$comments[0].'"'; ?>/>--></td>
  </tr>
  <tr> 
    <td width="377" colspan="3">
	<input class="radio" type="radio" name="contra" value="Consignment" <?php if(($workwith) && ($contra=='c')) echo 'checked'; ?> /> Consignment 
	<input class="radio" type="radio" name="contra" value="Trade" <?php if(($workwith) && ($contra=='t')) echo 'checked'; ?> /> Trade
	<input class="radio" type="radio" name="contra" value="New" <?php if(($workwith) && ($contra=='n')) echo 'checked'; ?>/> New
	</td>
    <td width="242" colspan="1"><input type="text" name="consignment1" id="consignment1" size="35" <?php if($workwith) echo 'value="'.$consignname.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Phone</td>
    <td width="181"><input type="text" name="phone1" id="phone1"  <?php if($workwith) echo 'value="'.$consignphone.'"'; ?>/></td>
    <td width="78">Fax</td>
    <td width="242"><input type="text" name="fax1" id="fax1"  <?php if($workwith) echo 'value="'.$consignfax.'"'; ?>/></td>
  </tr>
  <tr> 
    <td width="118">Mobile</td>
    <td width="181"><input type="text" name="mobile1" id="mobile1"  <?php if($workwith) echo 'value="'.$consignmobile.'"'; ?>/></td>
    <td width="78">ABN</td>
    <td width="242"><input type="text" name="abn1" id="abn1"  <?php if($workwith) echo 'value="'.$consignabn.'"'; ?>/></td>
  </tr>
  <tr>
  <td colspan="4">
  <p align="center">Below is the Status Selector. Choosing a different option than 'Standard' (the default) will result in your selection being displayed at the side of the PDF (ie. for re-issued trades, etc).<br />
Status: <select name="status1" class="sclt">
<option value="Brand New" <?php if(($workwith) && ($status=="0") ) echo 'selected="selected"'; ?>/>Brand New
<option value="Standard" <?php if(($workwith) && ($status=="1") ) echo 'selected="selected"'; ?>/>Standard
<option value="Re-Issued"<?php if(($workwith) && $status=="2") echo 'selected="selected"'; ?>/>Re-Issued
<option value="Sold"<?php if(($workwith) && $status=="3") echo 'selected="selected"'; ?>/>Sold
</select></p></td></tr>
<tr>
<td colspan="4" width="619" align="center">
<br />
<p>When you have finished, click 'Create PDF & Save', or click 'Don't Save' to abandon all changes and return to the front page.</p>
<p align="center"><input type="button" class="button red" onClick="javascript:window.location='index.php';return false;" value="Don't Save" /> or <input type="submit" name="ohnoes" class="button" value="Create PDF & Save"/></p>
</tr>
</table>
</form>
</body></html>
