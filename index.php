<?php
/*
	Site: 		Wholegoods Database
	Author:		cizzit (cizzit@localghost.com.au)
	Date:		August 2010
	Purpose:	To provide the sales team with a comprehensive list of stock in all branches, in regards to wholegoods stock (machines, etc).
				This way, one can look at the information displayed and know what machines we have, what status they have (sold, brand new, etc)
				and the specifications of said machine. The information is updated in real time, so if (for example) a salesman in Town A adds
				a slasher that he has just traded to the list, then one minute later a salesman in Town B can look at the list and see that
				slasher and all relevant details, as have been entered by the Gunnedah salesman.
				Obviously, such a system is only as good as the information entered. The entering form (trade_form.php) is based off the original
				wholegoods used stock sheets (used at parent company), so they are a familiar format.
				The data for a new or used piece of equipment is entered via the trade_form.php page. This data is passed to processpdf.php
				which saves the data into the database. It then adds the data to a PDF page via an fdf form, using the 32-bit program 'pdftk.exe'.
				This program MUST remain in the web root.
				Once this is saved, the user is presented with a success screen. They then have the option to view the created PDF (which is streamed
				to them) or return to the main page. When the main page option is exercised, the PDF file is deleted, as is the temporary files used
				to create it. This way, the server disk space isn't under threat, plus if the user requires the PDF file again, they can then return to
				the trade_form.php page and save the information without making a change.

	File:		index.php
	D.L.M.:		07/09/2010
	
	
	Codes:		Status codes:- these are used to show what the current status that a listing has. The current statuses are:
								0 - Brand New
								1 - Standard 
								2 - Re-Issued
								3 - Sold
								Sold was added to provide a way of keeping old information in the event that we needed it, and to prevent
								the need for rooting through years-old books. This is why there is an option to hide the Sold Equipment
								which is turned on by default.
				
*/
session_start();
require_once('db.class.php');
$tablename = 'trades';

if(ISSET($_GET['limit'])){
	switch($_GET['limit']){
		case 't':
		case 'c':
		case 'b':
		case 'm':
		case 's':
		case 'i':
		case 'o':
			$show=$_GET['limit'];
			break;
		default:
			unset($_SESSION['limit']);
	}
	if($show) $_SESSION['limit']=$show;
	header("Location:index.php");
	exit();
}
if(ISSET($_GET['doshow'])){
	if($_POST['doshower']=="on"){
		$_SESSION['showsold']="1";
		unset($_SESSION['shownewonly']);
		$fornicate=TRUE; // just temp value to see if neither are selected
	}
	if($_POST['doshownew']=="on") {
		$_SESSION['shownewonly']="1";
		unset($_SESSION['showsold']);
		$fornicate=TRUE;
	}
	if(!$fornicate) {
		unset($_SESSION['showsold']);
		unset($_SESSION['shownewonly']);
		unset($showsold);
	}
	header("Location:index.php");
	exit();
}
$current=false;
if(ISSET($_SESSION['limit'])) {
	$current=$_SESSION['limit'];
}
if(ISSET($_SESSION['showsold'])) {
	$showsold=$_SESSION['showsold'];
}
if(ISSET($_SESSION['shownewonly'])){
	$shownew=$_SESSION['shownewonly'];
}
$db = new db_class;
if(!$db->connect()) $db->print_last_error(false);

// In the below queries, I have hard-coded the ORDER BY details simply because too much choice can be bad.
// This is the best way to display the information, and I don't want people changing the sorting then crying cause they can't find something
// Some of these people get lost in Explorer, looking for files, because of sorting issues, FFS!
if(!ISSET($_SESSION['limit'])) {
	if(ISSET($showsold)) {
		$qry = "SELECT * from $tablename ORDER BY type, status, date ASC";
	} else if(ISSET($shownew)) {
		$qry = "SELECT * from $tablename WHERE status='0' ORDER BY type, status, date ASC";
	} else {
		$qry = "SELECT * FROM $tablename WHERE status<>'3' ORDER BY type, status, date ASC";
	}
} else {
	if(ISSET($showsold)) {
		$qry = "SELECT * from $tablename WHERE type='".$_SESSION['limit']."' ORDER BY type, status, date ASC";
	} else if(ISSET($shownew)) {
		$qry = "SELECT * from $tablename WHERE status='0' AND type='".$_SESSION['limit']."' ORDER BY type, status, date ASC";
	} else { 
		$qry = "SELECT * from $tablename WHERE type='".$_SESSION['limit']."' AND status<>'3' ORDER BY type, status, date ASC";
	}
}
$sql = $db->select($qry);

function type_translate($var) {
	if(!$var) $var='o';
	switch($var){
		case 't':	$ret='Tractor';		break;
		case 'c':	$ret='Combine';		break;
		case 'b':	$ret='Baler';		break;
		case 'm':	$ret='Mower';		break;
		case 's':	$ret='Sprayer';		break;
		case 'i':	$ret='Implement';	break;
		default:	$ret='Other';
	}
	return $ret;
}
// <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
//        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Wholegoods Database</title>
<link rel="stylesheet" href="styles.css" />
<script type="text/javascript">
function instruct(eleid) {
	var divstyle = new String();
	divstyle = document.getElementById(eleid).style.display;
	if(divstyle.toLowerCase()=="block") {
		document.getElementById(eleid).style.display = "none";
		document.getElementById('toggleInst').innerHTML="show";
	} else {
		document.getElementById(eleid).style.display = "block";
		document.getElementById('toggleInst').innerHTML="hide";
	}
	return false;
}
</script>
</head>
<body>
<div id="content">
<h1 class="big">Wholegoods Database</h1>
<p class="big"><strong>Instructions</strong> 
<span class="showhide"><a onClick="instruct('instructions')">
	<u><sup id="toggleInst"><?php if(ISSET($_SESSION['checkd'])){echo'show';}else{echo'hide';} ?></sup></u>
</a></span>
</p>
<div id="instructions" style="display:<?php if(ISSET($_SESSION['checkd'])){echo'none';}else{echo'block';} ?>">
<p>Below are the machines listed in the Trade database.<br />
<span class="hover">Fields under a coloured heading like this (ie. Price, Description, Comments) 
have been truncated to allow for a less-cluttered screen. You can hover your mouse pointer over 
these fields to see the entire data they contain.</span><br /><br />
You can show or hide the sold equipment by ticking or un-ticking the "Show Sold Equipment" box in the 
category selection pane, above the trade listings.<br /><br />
To edit information about a trade (ie. to add additional information or change the status), click 
the [edit] link at the left hand side. This will take you to a form where you can fill in the 
information you desire. When you are done, click the 'Create PDF & Save' button at the bottom of the
 form.<br /><br />
<strong>If you just want to create a PDF of the trade, use the [edit] link but don't change 
anything. Click the 'Create PDF & Save' button at the top or bottom 
of the form  to create the PDF.</strong></p>
<p><sup>[BETA]</sup> You can search for a particular trade by typing your search terms in the box and clicking 'Search'. Search tips are available on the search page.</p>
<p><sup>UPDATE: 07/09/2010</sup><strong><u>CHANGES</u></strong>:<br />
'Normal' status has been renamed to 'Standard'.<br />
There is now a new status for all types of machinery: BRAND NEW. This is for new equipment, previously un-owned, etc. Any machines marked as 'Brand New' will be displayed in bold and blue and at the top of each section.<br />
You can mark a machine as Brand New in the Trades window, where you would normally select 'Standard', 'Re-Issued' or 'Sold'.<br />
You can now select to view 'Brand New Equipment only' by checking the small box in the selection area.<br />
To make the Sold Equipment more noticable, all machines marked as Sold will now be coloured in a red colour.</p>
</div>
<?php $_SESSION['checkd']='lalala'; ?>
<div id="addnewtrade"><input type="button" class="button" value="Add New Listing" 
onClick="javascript:window.location='trade_form.php';return false"/><br /><br />
</div>
<table width="100%">
<tr>
<th colspan="18" style="padding-bottom:10px">
<p class="center description">Click on a box below to limit the display by type.<br /><br />
<a href="index.php?limit=t" <?php if($current=='t')echo'class="current"';?>>Tractors only</a>
<a href="index.php?limit=c" <?php if($current=='c')echo'class="current"';?>>Combines only</a>
<a href="index.php?limit=b" <?php if($current=='b')echo'class="current"';?>>Balers/Haytools only</a>
<a href="index.php?limit=m" <?php if($current=='m')echo'class="current"';?>>Mowers only</a>
<a href="index.php?limit=s" <?php if($current=='s')echo'class="current"';?>>Sprayers only</a>
<a href="index.php?limit=i" <?php if($current=='i')echo'class="current"';?>>Implements only</a>
<a href="index.php?limit=o" <?php if($current=='o')echo'class="current"';?>>Other only</a>
<a href="index.php?limit=clear">Show All</a>
<form name="doShowChange" method="POST" action="index.php?doshow" style="display:inline">
	<input class="checkbox" type="checkbox" name="doshower" onClick="document.doShowChange.submit();" title="Show Sold Equipment in the Trade Listings"
		<?php if(ISSET($showsold)) echo'checked="checked"'; ?>
	/> Show Sold Equipment
	<input class="checkbox" type="checkbox" name="doshownew" onClick="document.doShowChange.submit();" title="Show Brand New Equipment in the Trade Listings"
		<?php if(ISSET($shownew)) echo 'checked="checked"'; ?>
	/> Show Brand New Equipment Only
</form>
<br />
<br />
<form method="GET" action="search.php" name="searchSEARCH"  style="display:inline">
	<input type="text" class="search" name="s" width="100"/>
</form> &nbsp; <a href="" onClick="javascript:document.searchSEARCH.submit();return false;">Search</a>
</p>
</th>
</tr>
<tr>
<th>Action</th><th>Type</th><th>Date</th>
<th class="hover">Price</th><th class="hover">Make</th><th class="hover">Model</th>
<th>T No.</th><th>C No.</th><th class="hover">Serial No.</th>
<th class="hover">Eng No.</th><th class="hover">Description</th>
<th>Hours @ Date</th><th class="hover">Front Tyres</th><th class="hover">Rear Tyres</th>
<th class="hover">Comments</th><th class="hover">[C]onsignment/[T]rade/[N]ew</th><th>Status</th>
</tr>
<?php
$rowoff=1;
if($db->row_count($sql)<1){
	// no results found
	echo '<tr><td colspan="18"><p class="center alert">No trades found for your criteria.</p></td></tr>';
} else {
while($r = $db->get_row($sql)){
	// get vars
	$id=$r['id'];
	$type=$r['type'];
	$typetext=type_translate($type);
	$date=$r['date'];
	$price=$r['price'];
		$pprice = explode('=',$price);
		$pcount = count($pprice);
		$pprice = $pprice[$pcount-1];
	$make=$r['make'];
	$model = $r['model'];
	$tno = $r['tno'];
	$cno = $r['cno'];
	$serialno = $r['serialno'];
	$engineno = $r['engineno'];
	$enghp = $r['enghp'];
	$ptohp = $r['ptohp'];
	$description = str_replace("\"","&quot;",$r['description']);
	$hours = $r['hours'];
	$hoursdate = $r['hoursdate'];
	$fronttyresize = $r['fronttyresize'];
	$fronttyreply = $r['fronttyreply'];
	$fronttyrecon = $r['fronttyrecon'];
	$reartyresize = $r['reartyresize'];
	$reartyreply = $r['reartyreply'];
	$reartyrecon = $r['reartyrecon'];
	$comments = str_replace("\"","&quot;",$r['comments']);
	$consignname = $r['consignname'];
	$contra = substr($consignname,0,1);
	switch($contra){
		case "c":
			$contra = "[C]";
			break;
		case "t":
			$contra = "[T]";
			break;
		case "n":
			$contra = "[N]";
			break;
	}
	$consignname = substr($consignname,1);
	$consignphone = $r['consignphone'];
	$consignfax = $r['consignfax'];
	$consignmobile = $r['consignmobile'];
	$consignabn = $r['consignabn'];
	$status = $r['status'];
	switch($status){
		case "0":
			// New Machine
			$status="Brand New";
			break;
		case "1":
			// Standard Used Trade
			$status="Standard";
			break;
		case "2":
			// Re-Issued Trade
			$status="Re-Issued";
			break;
		case "3":
			// Sold Trade
			$status="Sold";
			break;
	}
	if($rowoff==2) {$rowoff=1;} else {$rowoff++;}
	switch ($rowoff) {
		case "2":
			echo "<tr class=\"".$type."two";
			if($r['status']=="0")echo"  bold";
			if($r['status']=="3")echo" sold";
			echo "\">";
			break;
		default:
			echo "<tr class=\"".$type."one";
			if($r['status']=="0")echo"  bold";
			if($r['status']=="3")echo" sold";
			echo "\">";
			$rowoff=1;
	}
		echo "<td class=\"cent\"><a class=\"editbox\" href=\"trade_form.php?cmd=edit&id=$id\">edit</a></td>";
		echo "<td>$typetext</td>";
		echo "<td>$date &nbsp;</td>\r\n";
		echo "<td title=\"$price\">$pprice &nbsp;</td>\r\n";
		echo "<td title=\"$make\" class=\"make\">";
			echo mb_strtoupper(substr($make,0,10));
			if(strlen($make)>10)echo'...';
		echo "&nbsp;</td>\r\n";
		echo "<td title=\"$model\">";
			echo substr($model,0,10);
			if(strlen($model)>10)echo'...';
		echo "&nbsp;</td>\r\n";
		echo "<td>$tno &nbsp;</td>\r\n";
		echo "<td>$cno &nbsp;</td>\r\n";
		echo "<td title=\"$serialno\">".substr($serialno,0,10);
			if(strlen($serialno)>10)echo"...";
		echo "&nbsp;</td>\r\n";
		echo "<td title=\"$engineno\">".substr($engineno,0,10);
			if(strlen($engineno)>10)echo"...";
		echo "&nbsp;</td>\r\n";
		//echo "<td>$enghp &nbsp;</td>\r\n";
		echo "<td title=\"$description\">";
			echo str_replace("\"","&quot;",substr($r['description'],0,15)); // presents the '&quot;' from getting cut off as part of the 15 char limit
			if(strlen($description)>15)echo"...";
		echo "</td>\r\n";
		echo "<td>$hours@$hoursdate</td>\r\n";
		echo "<td title=\"$fronttyresize - $fronttyreply($fronttyrecon%)\">";
	 		echo substr($fronttyresize,0,10);
			if(strlen($fronttyresize)>10)echo"...";
		echo "&nbsp;</td>\r\n";
		echo "<td title=\"$reartyresize - $reartyreply($reartyrecon%)\">";
			echo substr($reartyresize,0,10);
			if(strlen($reartyresize)>10)echo"...";
		echo "&nbsp;</td>\r\n";
		echo "<td title=\"$comments\">";
		echo str_replace("\"","&quot;",substr($r['comments'],0,15)); // prevents the '&quot;' string from getting cut off as part of the 15 char limit
			if(strlen($comments)>15)echo"...";
		echo "&nbsp;</td>\r\n";
		echo "<td title=\"$consignname\">$contra ";
			echo substr($consignname,0,20);
			if(strlen($consignname)>20)echo"...";
		echo "&nbsp;</td>\r\n";
		if($status!='') { echo "<td>$status</td>\r\n";
		} else {echo "<td>Normal</td>\r\n"; }
		echo "</tr>";
}
}
?>
</tr>
<tr><th colspan="17"><?php echo $db->row_count($sql); ?> result(s).</th></tr>
</table>
</div>
<div id="footer">
<p>Copyright &copy; 2010<?php if(date('Y')>2010) echo '-'.date('Y'); ?> - LG Coding (www.localghost.com.au). Authored by cizzit (cizzit@localghost.com.au)</p>
</div>
</body>
</html>
