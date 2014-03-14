<?php
session_start();
require_once('db.class.php');
$tablename = 'trades';
if(!ISSET($_GET['s']) || ($_GET['s']=='')){
	header("Location: index.php");
	exit();
}
$error = array();
$result=false;
$db = new db_class;
if(!$db->connect()) $db->print_last_error(false);

$searchTERM = strip_tags(trim($_GET['s']));
if (strlen($searchTERM) < 3) {
	$error[] = "Search terms must be longer than 3 characters.";
} else {
	$searchTERM = mysql_real_escape_string($searchTERM); // prevent sql injection.
}



if(count($error)<1){
	// no errors
	$qry = "SELECT * FROM $tablename WHERE ";
	// fields we want to search through are: make, model, serialno, engineno, description, comments, consignname
	$typesar = array(
		'make',
		'model',
		'serialno',
		'engineno',
		'description',
		'comments',
		'consignname'
	);
	for($i=0;$i<count($typesar);$i++){
		$qry.= "`{$typesar[$i]}` LIKE '%{$searchTERM}%'";
		if($i<(count($typesar)-1)) $qry.=' OR ';
	}
	$qry .= " ORDER BY type, status, date ASC";
	$sql = $db->select($qry);
}
if(count($error)>0){
	for($o=0;$o<count($error);$o++){
		$result .= $error[$o];
		if($o<(count($error)-1)) echo ', ';
	}
}


function type_translate($var) {
	if(!$var) $var='o';
	switch($var){
		case 't':
			$ret='Tractor';		break;
		case 'c':
			$ret='Combine'; 	break;
		case 'b':
			$ret='Baler'; 		break;
		case 'm':
			$ret='Mower'; 		break;
		case 's':
			$ret='Sprayer';		break;
		case 'i':
			$ret='Implement'; 	break;
		default:
			$ret='Other';
	}
	return $ret;
}

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
<h1 class="big">Wholegoods Database - Search</h1>
<p class="big"><strong>Search Help</strong> 
<span class="showhide"><a onClick="instruct('instructions')">
	<u><sup id="toggleInst">hide</sup></u>
</a></span>
</p>
<div id="instructions" style="display:block">
<p>Enter your search terms in the box provided. Please be aware of the following rules with searching:
<p><strong>1.</strong> if you type two words (e.g.: super steer), you will only see trades where those words appear, in the order you wrote them. In the example, you wouldn't see a result that just had the word 'super' or 'steer', or had 'steer super'.<br />
<strong>2.</strong> CaSe doesn't matter. <em>SUpeRsteEr</em> is the same to the search feature as <em>suPErSTEeR</em><br />
<strong>3.</strong> Testing words that can have an adverse affect on databases (such as <em>DROP</em>, <em>INSERT</em>) are flagged for review, then ignored, to protect against intrusion, so please dont use them (if in doubt, contact IT).<br />
<strong>4.</strong> Searches must be at least '3' characters long. But please don't write a novel.
</p>
</div>
<div id="addnewtrade"><input type="button" class="button" value="Back to Main" 
onClick="javascript:window.location='index.php';return false"/><br /><br />
</div>
<table width="100%">
<tr>
<th colspan="18">
<p class="center description">
<strong>Search term: </strong><?php echo $searchTERM; ?> <strong>[<?php 
	$sc = @$db->row_count($sql);
	if(is_numeric($sc)) { echo $sc; }
	else { echo '0'; }
?> result(s) found]</strong><br /><br />
<form method="GET" action="search.php" name="searchSEARCH"  style="display:inline">
	<input type="text" class="search" name="s" width="100" value="<?php echo $_GET['s']; ?>"/>
</form> &nbsp;&nbsp; <a href="" onClick="javascript:document.searchSEARCH.submit();return false;">Search</a><br />
<br />
</p>
</th>
</tr>
<tr>
<th>Action</th><th>Type</th><th>Date</th>
<th class="hover">Price</th><th class="hover">Make</th><th class="hover">Model</th>
<th>T No.</th><th>C No.</th><th class="hover">Serial No.</th>
<th class="hover">Eng No.</th><!--<th>Eng HP</th>--><th class="hover">Description</th>
<th>Hours @ Date</th><th class="hover">Front Tyres</th><th class="hover">Rear Tyres</th>
<th class="hover">Comments</th><th class="hover">[C]onsignment/[T]rade</th><th>Status</th>
</tr>
<?php
$rowoff=1;
if(count($error)>0){
	echo '<tr><td colspan="18"><p class="center alert">'.$result.'</p></td></tr>';
?>
</tr>
</table>
</div>
<div id="footer">
<p>Copyright &copy; 2010<?php if(date('Y')>2010) echo '-'.date('Y'); ?> - LG Coding (www.localghost.com.au). Authored by cizzit (cizzit@localghost.com.au)</p>
</div>
</body>
</html>
<?php
	exit();
} 
if($db->row_count($sql)<1){
	// no results found
	echo '<tr><td colspan="18"><p class="center alert">Nothing found for that search.</p></td></tr>';
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
				$contra = "[C]";	break;
			case "t":
				$contra = "[T]";	break;
			case "n":
				$contra = "[N]"; 	break;
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
				echo "\">";
				break;
			default:
				echo "<tr class=\"".$type."one";
				if($r['status']=="0")echo"  bold";
				echo "\">";
				$rowoff=1;
		}
			echo "<td class=\"cent\"><a class=\"editbox\" href=\"trade_form.php?cmd=edit&id=$id\">edit</a></td>";
			echo "<td>$typetext</td>";
			echo "<td>$date &nbsp;</td>\r\n";
			echo "<td title=\"$price\">$pprice&nbsp;</td>\r\n";
			echo "<td title=\"$make\" class=\"make\">";
				echo substr($make,0,10);
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
			if($status!='') {
				echo "<td>$status</td>\r\n";
			} else {
				echo "<td>Normal</td>\r\n";
			}
			echo "</tr>";
	}
}
?>
</tr>
</table>
</div>
<div id="footer">
<p>Copyright &copy; 2010<?php if(date('Y')>2010) echo '-'.date('Y'); ?> - LG Coding (www.localghost.com.au). Authored by cizzit (cizzit@localghost.com.au)</p>
</div>
</body>
</html>
