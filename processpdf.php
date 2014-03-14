<?php
session_start();
/*
createFDF()

Takes values submitted via an HTML form and fills in the corresponding
fields into an FDF file for use with a PDF file with form fields.

@param  $file   The pdf file that this form is meant for. Can be either
                a url or a file path.
@param  $info   The submitted values in key/value pairs. (eg. $_POST)
@result Returns the FDF file contents for further processing.
*/
function createFDF($file,$info){
    $data="%FDF-1.2\n%âãÏÓ\n1 0 obj\n<< \n/FDF << /Fields [ ";
    foreach($info as $field => $val){
    	if(is_array($val)){
        	$data.='<</T('.$field.')/V[';
        	foreach($val as $opt)
        		$data.='('.trim($opt).')';
        	$data.=']>>';
    	}else{
        	$data.='<</T('.$field.')/V('.trim($val).')>>';
    	}
    }
    $data.="\n] >>".
        " \n>> \nendobj\ntrailer\n".
        "<<\n/Root 1 0 R \n\n>>\n%%EOF\n";
    return $data;
}

$fdfname = time();
$fdf_file='fdfs/'.$fdfname.'.fdf';
$pdf_file='_tradesheetform.pdf';

if(ISSET($_POST['make1'])){
	
	if((strlen($_POST['make1'])<2) || (strlen($_POST['model1'])<2)) {
		header("Location: index.php");
		exit();
	}
	// form has been posted
	$fdf = createFDF($pdf_file,$_POST);
	
	if($fp=fopen($fdf_file,'w')) {
		fwrite($fp,$fdf,strlen($fdf));
		$created=TRUE;
	} else {
		echo 'Could not create the Form File ('.$fdf_file.').';
		$created=FALSE;
	}
	@fclose($fp);

	
	if($created==TRUE) {
	// call Save Information features
		
		require_once('db.class.php');
		$db = new db_class;
		if(!$db->connect()) $db->print_last_error(false);
			
		foreach($_POST as $key => $val) {
			$$key = mysql_real_escape_string(trim($val));
		}
		switch($contra){
			case 'Consignment':
				$consignment1='c'.$consignment1;
				break;
			case 'Trade':
				$consignment1='t'.$consignment1;
				break;
			case 'New':
				$consignment1='n'.$consignment1;
				break;
		}
		switch($status1){
			// reverse text->number
			case "Brand New":
				$status1="0";
				break;
			case "Standard":
				$status1="1";
				break;
			case "Re-Issued":
				$status1="2";
				break;
			case "Sold":
				$status1="3";
				break;
		}
		
		// build query
		if(ISSET($_GET['edit']) && is_numeric($_GET['edit'])) {
			// query is an update, not insert
			$qry = "UPDATE trades SET date='$date1',type='$type1',price='$price1',make='$make1',model='$model1',tno='$tno1',
			cno='$cno1',serialno='$serialno1',engineno='$engineno1',enghp='$enghp1',ptohp='$ptohp1',
			description='$descfirst1',
			hours='$hours1',hoursdate='$hoursdate1',fronttyresize='$tyresfrontsize1',fronttyreply='$tyresfrontply1',
			fronttyrecon='$tyresfrontpercent1',reartyresize='$tyresrearsize1',reartyreply='$tyresrearply1',
			reartyrecon='$tyresrearpercent1',
			comments='$commentsfirst1',
			consignname='$consignment1',consignphone='$phone1',consignfax='$fax1',consignmobile='$mobile1',
			consignabn='$abn1',status='$status1' 
			WHERE id='".$_GET['edit']."'";
		} else {
		// assume insert
			$qry = "INSERT INTO trades (date, type, price, make, model, tno, cno, serialno, engineno,
			enghp, ptohp, description, hours, hoursdate, fronttyresize, fronttyreply, fronttyrecon, reartyresize, reartyreply, 
			reartyrecon, comments, consignname,consignphone, consignfax, consignmobile, consignabn, status) 
			VALUES(
			'".$date1."',
			'".$type1."',
			'".$price1."',
			'".$make1."',
			'".$model1."',
			'".$tno1."',
			'".$cno1."',
			'".$serialno1."',
			'".$engineno1."',
			'".$enghp1."',
			'".$ptohp1."',
			'".$descfirst1."',
			'".$hours1."',
			'".$hoursdate1."',
			'".$tyresfrontsize1."',
			'".$tyresfrontply1."',
			'".$tyresfrontpercent1."',
			'".$tyresrearsize1."',
			'".$tyresrearply1."',
			'".$tyresrearpercent1."',
			'".$commentsfirst1."',
			'".$consignment1."',
			'".$phone1."',
			'".$fax1."',
			'".$mobile1."',
			'".$abn1."',
			'".$status1."'
			)";
		}
		$sql = $db->insert_sql($qry);
		
		if(!$sql) {
				echo "Couldn't save details, please try again.<br />";
			$db->print_last_error(false);
		} else {
			$fname = 'pdfs/'.$_POST['make1'].' '.$_POST['model1'].' Trade Sheet - '. date('d-m-Y').'-'.time().'.pdf';
			$fname = str_replace(' ','_',$fname);
			$fname = str_replace('&','',$fname);
		
			passthru('pdftk '.$pdf_file.' fill_form '.$fdf_file.' output "'.$fname.'" flatten');
			unlink($fdf_file); // delete the form data file
			
			echo '
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Wholegoods Database - PDF Created and Saved</title>
<link rel="stylesheet" href="styles.css"/>
</head>
<body>
<div id="content">
<h1 class="big">Wholegoods Database</h1>
<div id="instructions">
<p class="big center">The information has been saved and the PDF has been created.<br /><br /></p>
<p class="big center"><a href="servepdf.php?pdf='.$fname.'" class="button">VIEW the PDF</a><br />for printing and external save (if required, not necessary)</p>
<p class="center">Please note that saving is not necessary; if you require the PDF for further printing,
you can edit the entry again, and without making any changes, click \'Create PDF & Save\'
which will re-create the PDF for you.<br /><br /></p>
<p class="big center"><a href="servepdf.php?ul=lol&pdf='.$fname.'" class="button">Return to the front page</a></p>
</div>
</div>
</body>
</html>';
		}
	}
} else {
	header("Location:index.php");
}
?>
