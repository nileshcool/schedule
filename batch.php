<!DOCTYPE HTML> <!--The new DOCTYPE declaration, much easier that before -->
<html lang="en">
<head>
<title>CTTC Class Booking Scheduler</title>
<?php include('includes/head.php'); ?>
</head>
<body>
<!-- Primary Page Layout  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
  <div class="row">
   <div class="one-half column" style="margin-top: 2em">
	<header> <?php include('includes/header.php'); ?> </header>
   </div>
  </div>
</div>
<!-- End Document  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

<div id="wrapper">
<header> <?php include('includes/wrapheader.php'); ?> </header>

<div class="container">
<aside id="aside1" class="three columns" style="display: none;"> <?php include('includes/aside.php'); ?> </aside>

<section id="mainc" class="twelve columns disflex">
<article>
<header>
<?php
require_once("includes/bside.php");
require_once('includes/functionCall.php');

if(isset($_GET[action]) && !isset($_POST[submit]) && !isset($_POST[modsubmit])) {
	if($_GET[action] == 'view') {
		echo 'Select Year <select name="year_batch" id="year_batch">
			<option value="Year">Select Year from list</option>';
		echo "\r\n";
		for ($x = 2018; $x <= 2020; $x++) {
			echo '<option value="'.$x.'"'.($x == $year_batch ? " selected" : "").'>'.$x.'</option>';
			echo "\r\n";
		}
		echo "</select>\r\n";
		$array_Month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		echo 'Select Month <select name="month_batch" id="month_batch" onchange="showBatch('."'batch_Details','showBatchView'".')">';
		echo "\r\n<option value='Month'>Select Month from list</option>\r\n";
		for ($x=1; $x <= 12; $x++) {
			echo '<option value="'.$x.'"'.($x == $month_batch ? " selected" : "").'>'.$array_Month[$x-1].'</option>';
			echo "\r\n";
		}
		echo "</select> <br>\r\n";
	} elseif($_GET[action] == 'modify') {
		echo 'Select Year <select name="year_batch" id="year_batch">
		<option value="Year">Select Year from list</option>';
		echo "\r\n";
		for ($x = 2018; $x <= 2020; $x++) {
			echo '<option value="'.$x.'"'.($x == $year_batch ? " selected" : "").'>'.$x.'</option>';
			echo "\r\n";
		}
		echo "</select>\r\n";
		$array_Month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		echo 'Select Month <select name="month_batch" id="month_batch" onchange="showBatch('."'article1','showBatchModify1'".')">';
		echo "\r\n";
		echo "<option value='Month'>Select Month from list</option>\r\n";
		for ($x=1; $x <= 12; $x++) {
			echo '<option value="'.$x.'"'.($x == $month_batch ? " selected" : "").'>'.$array_Month[$x-1].'</option>';
			echo "\r\n";
		}
		echo "</select> <br>\r\n";
	} elseif($_GET[action] == 'add') {
		echo '<form name="batchEntryForm" action="#" method="POST">';
		$array_dummy = array();	//dummy array to be passed on
		addBatchTable($array_dummy, $connection);
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
	} else {
		die("<b>Sorry wrong page selected, you have to go Back</b><br>");
	}
} elseif(isset($_POST[submit]) || isset($_POST[modsubmit])) {
	$_POST["batch_no"] = strtoupper($_POST["batch_no"]); 
	$_POST["batch_name"] = strtoupper($_POST["batch_name"]);
	$_POST["Incharge"] = strtoupper($_POST["Incharge"]);
	$_POST["batch_type"] = strtoupper($_POST["batch_type"]);
	$_POST["Course_id"] = strtoupper($_POST["Course_id"]);
	$_POST["dur_unit"] = strtoupper($_POST["dur_unit"]);
//	$_POST["dur_from"]
//	$_POST["dur_to"]
	$batch_resq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `resource` WHERE type='13' AND name_id='$_POST[Incharge]'"));
	$batch_couq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `course_details` WHERE course_code='$_POST[Course_id]'"));
	$batch_typq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `slot_details` WHERE slot_name in ('SA', 'VA') AND slot_type='$_POST[batch_type]'"));
	//$batc_resq_result = mysqli_fetch_assoc();
	if($batch_resq[name_id] == $_POST[Incharge] && $batch_couq[course_code] == $_POST[Course_id] && $batch_typq[slot_type] == $_POST[batch_type]) {
		if(isset($_POST[submit])) {
		$batch_insq = "INSERT INTO `batch_details` (`serial`, `batch_no`, `batch_name`, `Incharge`, `batch_type`,`room_no`,`ext_lect`,
		`Course_id`, `Course_subject`, `duration`, `dur_unit`, `dur_from`, `dur_to`, `stud_cnt`) VALUES
		(NULL, '$_POST[batch_no]', '$_POST[batch_name]', '$_POST[Incharge]', '$_POST[batch_type]', '$_POST[room_no]', '$_POST[ext_lect]',
		'$_POST[Course_id]', '$batch_couq[course_name]', '$_POST[duration]', '$_POST[dur_unit]', '$_POST[dur_from]', '$_POST[dur_to]', '$_POST[stud_cnt]');";
		echo (mysqli_query($connection, $batch_insq)) ? "Batch has been created successfully": "Error: " . $batch_insq . "<br>" . mysqli_error($connection);
		} elseif(isset($_POST[modsubmit])) {
		$batch_insq = "UPDATE `batch_details` SET `batch_no` = '$_POST[batch_no]', `batch_name` = '$_POST[batch_name]',
		`Incharge` = '$_POST[Incharge]', `batch_type` = '$_POST[batch_type]', `room_no` = '$_POST[room_no]', `ext_lect` = '$_POST[ext_lect]',
		`Course_id` = '$_POST[Course_id]', `Course_subject` = '$batch_couq[course_name]', `duration` = '$_POST[duration]',
		`dur_unit` = '$_POST[dur_unit]', `dur_from` = '$_POST[dur_from]', `dur_to` = '$_POST[dur_to]', `stud_cnt` = '$_POST[stud_cnt]'
		WHERE `serial` = $_POST[batch_serial];";
		echo (mysqli_query($connection, $batch_insq)) ? "Batch has been Modified successfully": "Error: " . $batch_insq . "<br>" . mysqli_error($connection);
		}
	} else {
		echo "<p><b>Please Select the correct values for Batch Incharge & Batch type & Course ID</b></p>\r\n";
		echo '<form name="batchEntryForm" action="#" method="POST">';
		addBatchTable();
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
	}
}	else {
	var_dump($_POST);
}
?>
</header>
<div id="article1"></div>
<div id="batch_Details"></div>
</article> </br>
Go Here if you want to 
<?php if(!isset($_GET) || ($_GET[action] != 'view' && $_GET[action] != 'modify' && $_GET[action] != 'add')) {
	echo '<p><b>You have landed on wrong Page. Go to <a href="batch.php?action=view" > View Batch Details</a>
	 <a href="batch.php?action=add" > Add Batch Details</a> or  <a href="batch.php?action=modify" > Modify Batch Details</a>';
} elseif($_GET[action] == 'view') {
	echo ' <a href="batch.php?action=modify" > Modify Batch Details</a> or  <a href="batch.php?action=add" > Add Batch Details</a>';
} elseif($_GET[action] == 'modify') {
	echo ' <a href="batch.php?action=view" > View Batch Details</a> or <a href="batch.php?action=add" > Add Batch Details</a>';
} elseif($_GET[action] == 'add') {
	echo ' <a href="batch.php?action=view" > View Batch Details</a> or <a href="batch.php?action=modify" > Modify Batch Details</a>';
} else {
	echo '<p><b>You have landed on wrong Page. Go to <a href="batch.php?action=view" > View Batch Details</a>
	 <a href="batch.php?action=add" > Add Batch Details</a> or  <a href="batch.php?action=modify" > Modify Batch Details</a>';
}
?>
</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>
