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
<?php
require_once("includes/bside.php");
require_once('includes/functionCall.php');

if(isset($_GET[action]) && !isset($_POST[submit]) && !isset($_POST[modsubmit])) {
	if($_GET[action] == 'view') {
	//get results from database
	$result = mysqli_query($connection, "select * from course_details");
	$all_property = array();  //declare an array for saving property

	//showing property
	echo '<table class="data-table">
		<tr class="data-heading">';  //initialize table tag
	while ($property = mysqli_fetch_field($result)) {
	  echo '<td>' . $property->name . '</td>';  //get field name for header
	  array_push($all_property, $property->name);  //save those to array
	}
	echo '</tr>';  //end tr tag

	//showing all data
	while ($row = mysqli_fetch_array($result)) {
	  echo "<tr>";
	  foreach ($all_property as $item) {
	    echo '<td>' . $row[$item] . '</td>';  //get items using property value
	  }
	echo '</tr>';
	}
	echo "</table>";
	echo 'Click here if you want to
		<a href="course.php?action=modify" > Modify Course Details</a> or
		<a href="course.php?action=add" > Add Course Details</a>';
	} elseif($_GET[action] == 'modify') {
		$course_res = mysqli_query($connection, "SELECT * FROM `course_details` WHERE 1");
		echo 'Select Your Course <select name="course_details" id="course_details" onchange="oneGetValue('."'article1'".','."'showCourseModify1'".',this)">
			<option value="Course">Select Course from list</option>';
		echo "\r\n";
		while($course_resq = mysqli_fetch_assoc($course_res)) {
			echo '<option value="'.$course_resq[course_code].'">'.$course_resq[course_name].'</option>';
		echo "\r\n";
		}
		//foreach ($course_resq as $key => $value) {
		//	echo '<option value="'.$course_resq[].'">'..'</option>';
		//}
		echo "</select>\r\n";
	echo '<p>Click here if you want to
		<a href="course.php?action=view" >View Course Details</a> or
		<a href="course.php?action=add" > Add Course Details</a></p>';
	
	} elseif($_GET[action] == 'add') {
		echo '<form name="courseEntryForm" action="#" method="POST">';
		addCourseTable();
		echo "\r\n";
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
		echo 'Go Here if you want to <a href="course.php?action=view" >View Course Details</a> or
		<a href="course.php?action=modify" > Modify Course Details</a>';
	} else {
		echo "<p><b>You Should not be here.</b></p>\r\n";
		echo 'Go Here if you want to <a href="course.php?action=view" >View Course Details</a> or
		<a href="course.php?action=modify" > Modify Course Details</a> or
		<a href="course.php?action=add" > Add Course Details</a>';
	}	
} elseif(isset($_POST[submit]) || isset($_POST[modsubmit])) {
	$_POST["faculty"] = strtoupper($_POST["faculty"]);
	$_POST["course_cat"] = strtoupper($_POST["course_cat"]);
	$_POST["course_code"] = strtoupper($_POST["course_code"]);
	$_POST["course_name"] = strtoupper($_POST["course_name"]);
	$_POST["course_type"] = strtoupper($_POST["course_type"]);

	if(isset($_POST[submit])) {
		$course_insq = "INSERT INTO `course_details` values
		(NULL, '$_POST[faculty]', '$_POST[course_cat]', '$_POST[course_code]', '$_POST[course_name]', '$_POST[course_type]', '$_POST[duration]')";
		echo (mysqli_query($connection, $course_insq)) ? "Batch has been created successfully": "Error: " . $batch_insq . "<br>" . mysqli_error($connection);
	} elseif(isset($_POST[modsubmit])) {
		$course_insq = "UPDATE `course_details` SET `faculty` = '$_POST[faculty]', `course_cat` = '$_POST[course_cat]',
		`course_code` = '$_POST[course_code]', `course_name` = '$_POST[course_name]', `course_type` = '$_POST[course_type]',
		`duration` = '$_POST[duration]' WHERE `serial` = $_POST[serial];";
		echo (mysqli_query($connection, $course_insq)) ? "Batch has been Modified successfully": "Error: " . $batch_insq . "<br>" . mysqli_error($connection);		
	}
} else {
	var_dump($_POST);
}

?>
</article>
<article>
<div id="article1"></div>
<div id="course_Details"></div>
</article>
</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>
