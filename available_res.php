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

if(!isset($_GET[action]) && $_SERVER['REQUEST_METHOD'] != 'POST') {
$res_reg = $_GET["res_req"];

//get results from database
$result = mysqli_query($connection, "select * from resource where type=$res_reg");
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
} elseif($_GET[action] == "add" && $_SERVER['REQUEST_METHOD'] != 'POST') {
	//echo $_SERVER['REQUEST_METHOD'];
	echo '<form name="lectEntryForm" action="#" method="POST">';
		echo "\r\n";
		addLectDet();	//call the function to populated table
		echo "\r\n";
		echo '<button class="button gray" type="submit" name="submit">Submit</button>
		</form>';
} elseif($_GET[action] == "modify" && $_SERVER['REQUEST_METHOD'] != 'POST') {
	$lec_modq = mysqli_query($connection, "select * from resources where name_id='14';");	?>
	Select External Lecturer you want to modify Details of
	<select name="lect_details" id="lect_details" onblur="oneGetValue('article1','showLectDetail',this)">
	  <option value="EXT">Select Lecturer</option>
	  <?php while($lec_modqr = mysqli_fetch_assoc($lec_modq)) {
	  	echo '<option value="'.$lec_modqr[name_id].'">'.$lec_modqr[name].'</option>';
	  }	?>
	</select>
<?php	} elseif(isset($_POST[submit])) {
	$_POST[name_id] = strtoupper($_POST[name_id]);
	$_POST[capacity] = strtoupper($_POST[capacity]);
	
	$ins_query_ch = mysqli_query($connection, "select * from `resource` where name_id='E$_POST[name_id]' and name='E$_POST[name]';");
	if(mysqli_fetch_row($ins_query_ch) > 0) {
		echo "You already have $_POST[name] avialable as External Lecturer, you can <a href='available_res.php?action=modify'>Modify</a> details here";
	} else {
	$ins_query = mysqli_query($connection, "insert into `resource` values ('14','E$_POST[name_id]', '$_POST[name]',
	'$_POST[per_no]', '$_POST[capacity]', '$_POST[specialization]', '$_POST[Mobile]', '$_POST[email]', '$_POST[available]');");
	echo (mysqli_multi_query($connection, $ins_query)) ? "Lecturer Details created successfully": "Error: " . $ins_query . "<br>" . mysqli_error($connection);
	}
} else {
	echo '<meta http-equiv="Refresh" content="0; url=/cttc">';
}
?>
	</article> <br>
	<article id="article1">
	</article>
</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>