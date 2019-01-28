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
		<header> <b>Course details</b>	</header>
		<a href="course.php?action=view" >View Course Details</a> or
		<a href="course.php?action=modify" > Modify Course Details</a> or
		<a href="course.php?action=add" > Add Course Details</a>
	</article> <br>
	<article>
		<header> <b>Batch details</b>	</header>
		<a href="batch.php?action=view" >View Batch Details</a> or
		<a href="batch.php?action=modify" > Modify Batch Details</a> or
		<a href="batch.php?action=add" > Add Batch Details</a>
	</article> <br>

	<article>
		<header><b>Class Room & Lecturer details</b>	</header>
		View <a href="available_res.php?res_req=11" >Class Rooms</a> or
		View <a href="available_res.php?res_req=12" >Laboratory Rooms</a> or
		View <a href="available_res.php?res_req=13" >Lecturer Details</a><br>
		View <a href="available_res.php?res_req=14" >Ext Lecturers</a> or
		Add <a href="available_res.php?action=add" >Ext Lecturer</a> or
		Modify <a href="available_res.php?action=modify" >Ext Lecturer</a>
	</article> <br>
	<article>
		<header><b>Available Slots</b> </header>
		<a href="slot.php" >Slot Avaiable</a>
	</article> <br>
	<article>
	<header><b>Schedule Booking may be done here</b> </header>
<!--		<a href="schedule.php" >Make Schedule</a>	-->
		<a href="modify_sch.php?action=make" >Make Schedule</a> or
		<a href="modify_sch.php?action=modify" >Modify Schedule</a> or
		<a href="modify_sch.php?action=view" >View Schedule</a>
	</article>

	<article> <br>
	<header><b>Misc Data & Reports</b></header>
	<a href="holiday.php" >Holiday List</a>
	</article> <br>
</section> <!-- End of mainc div -->
<aside id="aside2" class="four columns" style="display: none;"> <?php include('includes/bside.php'); ?> </aside>
</div> <!-- container -->
</div> <!--wrapper -->
<footer> <?php include('includes/footer.php'); ?></footer>
<!-- =================End Document======================= -->
</body>
</html>