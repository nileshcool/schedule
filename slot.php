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

//get results from database
$result = mysqli_query($connection, "select * from slot_details");
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
?>
	</article> <br>
	<article>
		<header> <b><!--Batch details --></b>	</header>
	</article> <br>

</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>
