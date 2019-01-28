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
	<header> <b>List of Holidays</b>	</header>
	Select Year <select name="year_batch" id="year_batch" onchange="oneGetValue('article1','showHolidays',this)">
	<option value="Year">Select Year from list</option>
	<?php	for ($x = 2018; $x <= 2020; $x++) {
		echo '<option value="'.$x.'"'.($x == $year_batch ? " selected" : "").'>'.$x.'</option>';
		echo "\r\n";
		}	?>
	</select>
	<div id="article1"> <b>Select a Batch from here</b>	</div>
	<div id="batch_Details"></div> <br>

	<header><b>Make Batch Schedule</b>
	<div id="Scheduller"></div>
</article> </br>

</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>