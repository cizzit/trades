<?php
session_start();
if(!ISSET($_GET['pdf'])) {
	header("Location: index.php");
	exit();
} 

$filename = $_GET['pdf'];

if(!ISSET($_GET['ul'])){
	header("Content-type: application/pdf");
	$gof = substr($filename,5);
	$gof = str_replace("_"," ",$gof);
	header("Content-disposition: attachment;filename=\"".$gof."\"");
	readfile($filename);
}
@unlink("$filename");
header("Location: index.php");

?>
