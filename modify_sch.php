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
<?php
require_once("includes/bside.php");
require_once('includes/functionCall.php');

if($_SERVER['REQUEST_METHOD'] != 'POST') {
//if( !empty($_POST) ) {} -alternate method could be	?>
<article class="no-print">
	<header> <?php yearMonthShow();	//call to the function to show Year and Month ?>
	</header>
	<div id="article1"> <b>Select a Batch from here</b>	</div>
	<div id="batch_Details"></div> <br>

	<header><b>Make Batch Schedule</b>
	<div id="Scheduller"></div>
	</header>
</article> <br>
<article class="no-print">
	<header>
	<?php	if($_GET[action] == "make") {	?>
		<script type="text/javascript">
			var action = "make";
		</script>
<?php } elseif($_GET[action] == "modify") {	?>
		<script type="text/javascript">
			var action = "modify";
		</script>
<?php	} elseif($_GET[action]  == "view") {	?>
		<script type="text/javascript">
			var action = "view";
		</script>
<?php	} else {
		//echo '<meta http-equiv="Refresh" content="0; url='.$_SERVER['DOCUMENT_ROOT'].'/cttc">'; --cannot be used as gives absolute path
		echo '<meta http-equiv="Refresh" content="0; url=/cttc">';
		}	//end of else for $_GET options
	} elseif(isset($_POST[submit]) || isset($_POST[owsubmit])) {	//end of if $_SERVER['REQUEST_METHOD'] != 'POST'
	$month_batch = $_POST["Month"];
	$year_batch = $_POST["Year"];
	$batch_serial = $_POST["Batch_serail"];

	yearMonthShow($year_batch, $month_batch, $connection);	//call to the function to show Year and Month
?>
	</header>
	<div id="article1"> <b>Selected Batch for which Schedule has been Done</b>
	<select name="batchData" id="batchData">
	<?php $result = mysqli_query($connection, "select * from batch_details where serial=$batch_serial");
	$all_property = array();  //declare an array for saving property

	//showing all data
	while ($row = mysqli_fetch_array($result)) {
		echo '<option value="'.$row[serial].'">'.$row[batch_name].' on '.$row[Course_subject].' for '.$row[duration].' '.$row[dur_unit].'</option>';  //get items using property value
  		echo "\r\n";
	}
	mysqli_free_result($result);	// Free result set
	?>
	</select>
	</div>
	<div id="batch_Details">	<table class="data-table">
	<tr class="data-heading">  <!--initialize table tag	-->
	<?php	$result = mysqli_query($connection, "select * from batch_details where serial=$batch_serial");
	while ($property = mysqli_fetch_field($result)) {
		echo '<td>' . $property->name . '</td>';  //get field name for header
		echo "\r\n";
		array_push($all_property, $property->name);  //save those to array
	}
	echo "</tr>\r\n";  //end tr tag

	//showing all data
	while ($row2 = mysqli_fetch_array($result)) {
		echo "<tr>";
		foreach ($all_property as $item) {
		echo '<td>' . $row2[$item] . '</td>';  //get items using property value
		echo "\r\n";
		}
	echo "</tr>\r\n";
	}
	mysqli_free_result($result);	// Free result set
	?>
	</table>	</div>
</article> <br>

<article>
	<header>	<div id="Scheduller"><b>Batch Schedule</b><br>
	<?php	$result = mysqli_query($connection, "select * from batch_details where serial=$batch_serial");
	$all_property = mysqli_fetch_assoc($result);
	$checkForBatch = mysqli_query($connection, "select * from schedule where Batch_no=$batch_serial");
	if(!isset($_POST[modify]) && mysqli_fetch_row($checkForBatch) > 0) {
		echo "<b> already done for this batch</b><br>\r\n";
		echo "Click here if you want to <a href='modify_sch.php?action=view&serial=".$batch_serial."'>view</a> or <a href='modify_sch.php?action=modify&serial=".$batch_serial."'>modify</a> the schedule";
	} elseif($_POST[Day1] != $all_property[dur_from]) {
		echo "<b>NOT</b> done as their was some error. If this message repeats contact Administrator";
		writeTableEmpty($batch_serial, $all_property, $connection, $_POST);	//call the function to display data
	} else {
		list($insert_warning, $insert_error, $insert_stmt) = checkForSch($all_property, $_POST, $connection);
		//https://stackoverflow.com/questions/3451906/multiple-returns-from-function
		//https://stackoverflow.com/questions/12491840/how-to-return-multiple-values-from-a-function
		
		if(!empty($insert_error)) {
			echo "\r\n<p><b>You have Errors in your Schedule, you cannot proceed with these errors</b><br>";
			foreach ($insert_error as $key => $value) {
				echo $value."\r\n<br>";
			}
			echo "</p>";
			//print_r($insert_error);
			if(!empty($insert_warning)) {
			  echo "\r\n<p><b>You also have Warnings in your Schedule, you proceed with these warnings though</b><br>";
			  foreach ($insert_warning as $key => $value) {
				echo $value."\r\n<br>";
			  }
			  echo "</p>";
			}
			echo '	<form name="slot_form" action="modify_sch.php" method="POST">'."\r\n";
			writeTableEmpty($batch_serial, $all_property, $connection, $_POST);	//call the function to display data
			echo '	<button class="button gray" type="submit" name="submit">Submit</button>
			</form>';
		} /*elseif(!empty($insert_warning) && !isset($_POST[owsubmit])) {	//code omitted as per cell check is done
			foreach ($insert_warning as $key => $value) {
			 echo $value."<br>\r\n";
			}
			//print_r($insert_warning);
			echo "<p><b>You have warnings in you Schedule</b></p>\r\n";
			echo '	<form name="slot_form" action="#" method="POST">';
			writeTableEmpty($batch_serial, $all_property, $connection, $_POST);	//call the function to display data
			echo '	<button class="button gray" type="submit" name="submit">Submit</button>
			<button class="button gray" type="submit" name="owsubmit">Over Write Submit</button>
			</form>';
		}*/ else {
			if(!empty($insert_warning)){
				echo "<p><b>You have warnings in you Schedule</b></p>\r\n";
				foreach ($insert_warning as $key => $value) {
				 echo $value."<br>\r\n";
				}
				echo (mysqli_multi_query($connection, $insert_stmt)) ? "Schedule has been created successfully": "Error: " . $insert_stmt . "<br>" . mysqli_error($connection);
				mysqli_free_result($insert_stmt);	// Free result set for next query
			} else {
				echo (mysqli_multi_query($connection, $insert_stmt)) ? "Schedule has been created successfully": "Error: " . $insert_stmt . "<br>" . mysqli_error($connection);
				mysqli_free_result($insert_stmt);	// Free result set for next query
			}
		}
	}
	?>
	</div>
	</header>
</article> <br>
<?php
} else {	//end of elseif $_POST[wsubmit]
	echo '<meta http-equiv="Refresh" content="0; url=/cttc">';
}	//end of else $_POST[wsubmit]
?>

</section> <!-- End of mainc div -->
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>