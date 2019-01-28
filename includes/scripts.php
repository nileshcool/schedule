<?php
/************************ Example for checking Query *************************************
global $DB;
$DB->query("UPDATE exp_members SET group_id = '$group_id' WHERE member_id = '$member_id'");
******************************************************************************************/
require_once("includes/bside.php");
require_once('functionCall.php');

$case_val = $_GET['case_val'];
if (isset($case_val) && !isset($_POST[serial])) {
switch ($case_val) {
	case article1:	//Case -1
?>	<b>Select a Batch from here</b>
	<select name="batchData" id="batchData" onchange="showBatchDetails('batch_Details', 'batch_Details', action)">
	<option value="Select">Select Batch from list</option>
<?php	$month_batch = $_GET[month_batch];
	$year_batch = $_GET[year_batch];
	$result = mysqli_query($connection, "select * from batch_details where MONTH(dur_from)=$month_batch and YEAR(dur_from)=$year_batch");
	$all_property = array();  //declare an array for saving property

	while ($property = mysqli_fetch_field($result)) {
		array_push($all_property, $property->name);  //save those to array
	}

	//showing all data
	while ($row = mysqli_fetch_array($result)) {
  		echo "\r\n";
  		echo '<option value="'.$row[serial].'">'.$row[batch_name].' on '.$row[Course_subject].' for '.$row[duration].' '.$row[dur_unit].'</option>';  //get items using property value
	}
	echo "\r\n";
	echo '	</select>';
	break;
	
	case batch_Details:	//Case -2
	$serial = $_GET["serial"];
	//get results from database
	$result2 = mysqli_query($connection, "select * from batch_details where serial=$serial");
	$all_property = array();  //declare an array for saving property
	$all_property_value = array();  //declare an Associative array for saving property
	
	//Error message to be displayed is Schedule is already prepared for Batch select
	$checkForBatch = mysqli_query($connection, "select * from schedule where Batch_no=$serial");
	if($_GET[action] == "make" && mysqli_fetch_row($checkForBatch) > 0) {
		die("<p><b>Warning: Schedule already done for this batch</b></p>
		Click here if you want to <a href='modify_sch.php?action=view&serial=".$serial."'>view</a> or <a href='modify_sch.php?action=modify&serial=".$serial."'>modify</a> the schedule");
		//to die in case schedule already available.
	} elseif($_GET[action] == "modify" && mysqli_fetch_row($checkForBatch)== 0) {
		die("<p><b>Error: Schedule not done for this batch</b></p>
		Click here if you want to <a href='modify_sch.php?action=make&serial=".$serial."'>Make</a> the schedule");
		//to die in case schedule not already available.
	} elseif($_GET[action] == "view" && mysqli_fetch_row($checkForBatch)== 0) {
		die("<p><b>Error: Schedule not done for this batch</b></p>
		Click here if you want to <a href='modify_sch.php?action=make&serial=".$serial."'>Make</a> or <a href='modify_sch.php?action=modify&serial=".$serial."'>modify</a> the schedule");
		//to die in case schedule not already available.
	}
	mysqli_free_result($checkForBatch);	//free result set

	//showing property
	echo '<form method="post">
		<table class="data-table">
		<tr class="data-heading">';  //initialize table tag
	while ($property = mysqli_fetch_field($result2)) {
		echo '<td>' . $property->name . '</td>';  //get field name for header
		array_push($all_property, $property->name);  //save those to array
	}
	echo '</tr>';  //end tr tag

	//showing all data
	while ($row = mysqli_fetch_array($result2)) {
		echo "<tr>";
		foreach ($all_property as $item) {
		echo '<td>' . $row[$item] . '</td>';  //get items using property value
		}
	echo '</tr>';
	}
	echo "</table>\r\n";
		if($_GET[action] == "make") {
			$get_action = "schedullerMake";
			//echo '<input type="button" value="Proceed" onclick="showScheduleMake('.$serial.')">';
		} elseif($_GET[action] == "modify") {
			$get_action = "schedullerModify";
			//echo '<input type="button" value="Proceed" onclick="showScheduleModify('.$serial.')">';
		} elseif($_GET[action] == "view") {
			$get_action = "schedullerView";
			//echo '<input type="button" value="Proceed" onclick="showScheduleView('.$serial.')">';
		}	?>
	<input type="button" value="Proceed" onclick="showSchedule('<?php echo $get_action?>','<?php echo $serial?>')">
	</form>
<?php	break;
	
	case schedullerMake:	//Case -3
	//print_r($obj) & var_dump($obj)
	$serial = $_GET["serial"];
	$result2 = mysqli_query($connection, "select * from batch_details where serial=$serial");
	$all_property = mysqli_fetch_assoc($result2);  //declare & save Associative array
	
	echo "for $all_property[batch_name] on $all_property[Course_subject] for $all_property[duration] $all_property[dur_unit] of Batch No. $all_property[serial]";
?>
	<div class="success_box" style="Display: none;">All of the fields were successfully validated!</div>
   <div class="error_box" style="Display: none;"></div>
	<form name="slot_form" action="#" method="POST">
<?php writeTableEmpty($serial, $all_property, $connection);	//call the function to display data
?> <!--call the function to display data-->
	<button class="button gray" type="submit" name="submit">Submit</button>
	</form>
<?php	break;

	case schedullerModify:	//Case -4
	$serial = $_GET["serial"];
	$result2 = mysqli_query($connection, "select * from batch_details where serial=$serial");
	$all_property = mysqli_fetch_assoc($result2);  //declare & save Associative array
	
	echo "for $all_property[batch_name] on $all_property[Course_subject] for $all_property[duration] $all_property[dur_unit] of Batch No. $all_property[serial]";
?>
	<div class="success_box" style="Display: none;">All of the fields were successfully validated!</div>
   <div class="error_box" style="Display: none;"></div>
   <form name="slot_form" action="#" method="POST">
<?php writeTableSchdule($serial, $connection);	//call the function to display data
?> <!--call the function to display data-->
	<input type="hidden" id="modify" name="modify" value="modify" />
	<button class="button gray" type="submit" name="submit">Submit</button>
	</form>
<?php	break;
	
	case schedullerView:	//Case -5
	$serial = $_GET["serial"];
	$result2 = mysqli_query($connection, "select * from batch_details where serial=$serial");
	$all_property = mysqli_fetch_assoc($result2);  //declare & save Associative array
	
	echo "for $all_property[batch_name] on $all_property[Course_subject] for $all_property[duration] $all_property[dur_unit] of Batch No. $all_property[serial]";
?>
	<div class="success_box" style="Display: none;">All of the fields were successfully validated!</div>
   <div class="error_box" style="Display: none;"></div>
   <form name="slot_form" action="print_sch.php" method="POST">
<?php writeTableSchView($serial, $all_property, $connection);	//call the function to display data
?> <!--call the function to display data-->
	<button class="button gray no-print" type="submit" name="submit" target="_blank" rel="noopener noreferrer">Proceed to Print</button>
	</form>
<?php	break;

	case SlotXLect2:	//Case -6
	if($_GET[lectsel] != 'EXT') {
	  $lec_query = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='$_GET[type]' and name_id !='$_GET[lectsel]' and available='Y'");
	} else {
	  $lec_query = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='14' and name_id !='$_GET[lectsel]' and available='Y'");
	}
	echo '<option value="NIL">SlotA Lect2</option>';
	echo "\r\n";
	while ($lec_query_res = mysqli_fetch_assoc($lec_query)) {
		echo '<option value="'.$lec_query_res[name_id].'"'.(($lec_query_res[name_id] == $_GET[lect2])?" selected":"").'>'.$lec_query_res[name].'</option>';
		echo "\r\n";
	}
	break;
	
	case showBatchView:	//case -7
	//get results from database
	$result = mysqli_query($connection, "SELECT * FROM `batch_details` WHERE MONTH(dur_from)=$_GET[month_batch] and YEAR(dur_from)=$_GET[year_batch]");
	$all_property = array();  //declare an array for saving property

	//showing property
	echo '<table class="data-table">
	<tr class="data-heading">';  //initialize table tag
	echo "\r\n";
	while ($property = mysqli_fetch_field($result)) {
	 echo '<td>' . $property->name . '</td>';  //get field name for header
	 echo "\r\n";
	 array_push($all_property, $property->name);  //save those to array
	}
	echo '</tr>';  //end tr tag

	//showing all data
	while ($row = mysqli_fetch_array($result)) {
	 echo "<tr>";
	 foreach ($all_property as $item) {
		echo '<td>' . $row[$item] . '</td>';  //get items using property value
		echo "\r\n";
  	 }
	echo "</tr>\r\n";
	}
	echo "</table>";
	break;
	
	case showBatchModify1:	//case -8
	echo 'Select a Batch from Drop Down
	<select name="batchData" id="batchData" onchange="oneGetValue('."'batch_Details','showBatchModify2',".'this)">
	<option value="Select">Select Batch from list</option>';
	$month_batch = $_GET[month_batch]; 
	$year_batch = $_GET[year_batch];
	$result = mysqli_query($connection, "select * from batch_details where MONTH(dur_from)=$month_batch and YEAR(dur_from)=$year_batch");
	$all_property = array();  //declare an array for saving property

	while ($property = mysqli_fetch_field($result)) {
		array_push($all_property, $property->name);  //save those to array
	}

	//showing all data
	while ($row = mysqli_fetch_array($result)) {
  		echo "\r\n";
  		echo '<option value="'.$row[serial].'">'.$row[batch_name].' on '.$row[Course_subject].' for '.$row[duration].' '.$row[dur_unit].'</option>';  //get items using property value
	}
	echo "\r\n";
	echo '	</select>';
	break;

	case showBatchModify2:	//Case -9
		$batch_serial = $_GET[arg11];
		$batch_resq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `batch_details` WHERE serial=$batch_serial"));
		//var_dump($batch_resq);
		echo '<form name="batchEntryForm" action="#" method="POST">';
		addBatchTable($batch_resq, $connection);	//call function to populate table to modify
		echo '<input type="hidden" id="batch_serial" name="batch_serial" value="'.$batch_serial.'">';
		echo "\r\n";
		echo '<button class="button gray" type="submit" name="modsubmit">Submit</button>
		</form>';
	break;
	
	case showBatchAdd:	//case -7 Not in use, is done from batch.php directly
		echo '<form name="batchEntryForm" action="#" method="POST">';
		//addBatchTable();	//call the function to populated table for entry
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
	break;

	case showCourseModify1:	//case -10
		$course_resq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `course_details` WHERE course_code='$_GET[arg11]'"));
		echo '<form name="courseEntryForm" action="#" method="POST">';
		addCourseTable($course_resq);	//call the function to populated table for entry
		echo '<input type="hidden" id="serial" name="serial" value="'.$course_resq[serial].'">';
		echo "\r\n";
		echo '<button class="button gray" type="submit" name="modsubmit">Submit</button>
		</form>';
	break;
	
	case showLectDetail:	//case -11
		$lect_resq = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `resource` WHERE name_id=14 and name='$_GET[arg11]'"));
		echo '<form name="lectEntryForm" action="#" method="POST">';
		addLectDet($lect_resq);	//call the function to populated table for entry
		echo "\r\n";
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
	break;

	case checkForBooking:	//case -12
	//https://stackoverflow.com/questions/195951/change-an-elements-class-with-javascript
		$result_var = "success_box";
		$get_date = $_GET[day];
		$get_value = $_GET[elem_val];

		$check_query = mysqli_query($connection, "SELECT * FROM `schedule` WHERE date='$get_date';");
		if($checkRoomType[type] == "12") {
			$result_var = "warrning_box";
		} else {
		//In DB at Lect1 matching values will be error, for Lect2 in DB matching values will be warning
		//echo "Date is '$get_date', slot to check is '$get_slot[0]' and values to check is '$get_value'";
		if(preg_match ( "/^slot._room/i" , $_GET[elemID]) == 1) {	//http://php.net/manual/en/function.preg-match.php	- preg_match for regex explanation
		//preg_match() returns 1 if the pattern matches given subject, 0 if it does not, or FALSE if an error occurred.
		 $get_slot = str_split($_GET[elemID], 10);	//http://php.net/manual/en/function.str-split.php
		 while($check_query_res = mysqli_fetch_assoc($check_query)) {
			foreach ($check_query_res as $key => $value) {
			  if(strtolower($key) == strtolower($get_slot[0])) {
			  	if($value == $get_value) {
			  		$result_var = "error_box";
			  		//https://stackoverflow.com/questions/195951/change-an-elements-class-with-javascript
			  	}
			  } else {
			  	continue;
			  }
			}
		 }
		} else {
			$get_slot = preg_split ( "/[_]+/" , $_GET[elemID]);
			//print_r($get_slot);
			while($check_query_res = mysqli_fetch_assoc($check_query)) {
			 foreach ($check_query_res as $key => $value) {
			  $value_db = preg_split("/&/", $value);
			  $get_key = preg_split("/_+/", $key);
			  if(strtolower($get_key[0]) == strtolower($get_slot[0])) {
			  	if($get_key[1] == "room") {
			  		$checkRoomType = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `resource` WHERE name_id=$value"));
			  		$result_room = ($checkRoomType[type] == "12")? "LAB":"CLASS";
			  	} elseif($get_key[1] == "lec1" && ($value_db[0] == $get_value || $value_db[1] == $get_value)) {
			  		$result_var = "error_box";
			  		//https://stackoverflow.com/questions/195951/change-an-elements-class-with-javascript
			  	} elseif($get_key[1] == "lec2" && ($value_db[0] == $get_value || $value_db[1] == $get_value)) {
			  		if($result_room == "LAB") { $result_var = "error_box"; } else { $result_var = "warrning_box"; }
			  	}
			  } else {
			  	continue;
			  }
			 }
			}
		}
		}
		echo $result_var;
	break;

	case roomTypeCheck:	//case -13
	$get_value = $_GET[arg11];
	$check_query = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `resource` WHERE name_id='$get_value';"));
	if($check_query[type] == 12) {
		echo "LAB";
	} else {
		echo "Class";
	}
	break;

	case showHolidays:	//case -14
	$get_year = $_GET[arg11];
	$holiday_list = mysqli_query($connection, "SELECT * FROM `holidays` WHERE year(date)='$get_year'");
	$all_property = array();  //declare an array for saving property

	//showing property
	echo '<table class="data-table">
	<tr class="data-heading">';  //initialize table tag
	while ($property = mysqli_fetch_field($holiday_list)) {
	  echo '<td>' . $property->name . '</td>';  //get field name for header
	  array_push($all_property, $property->name);  //save those to array
	}
	echo '</tr>';  //end tr tag

	//showing all data
	while ($row = mysqli_fetch_array($holiday_list)) {
	  echo "<tr>";
	  foreach ($all_property as $item) {
		echo '<td>' . $row[$item] . '</td>';  //get items using property value
	  }
	  echo '</tr>';
	}
	echo "</table>";
	break;

	default:		//Default case, if no value is passed for Switch statement
		echo "No case selected, their must be some spelling mistake";
		//code to be executed if case value is different from all labels;
}
}
?>