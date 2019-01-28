<!DOCTYPE HTML> <!--The new DOCTYPE declaration, much easier that before -->
<html lang="en">
<head>
<title>CTTC Class Booking Scheduler</title>
<?php include('includes/head.php'); ?>
<link rel="stylesheet" href="css/normalize.css" media="print">
</head>
<body>

<header> </header>
<div id="wrapper">
<header>
<script language="javascript" type="text/javascript"> 
// accept the fomr object as a parameter 
function Myprint() { 
window.print(); 
return false; 
} 
</script>
</header>

<div class="container">
<section id="mainc" class="twelve columns disflex">
<?php
require_once("includes/bside.php");
require_once('includes/functionCall.php');

//var_dump($_POST);
$serial = $_POST["Batch_serail"];
?>
	<article>
	<header class="center"> <img src="images/ltrhd.jpg" alt="Header Image" style="width:991px;height:159px;">
	<?php $Batch_query = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `batch_details` WHERE serial=$serial"));
	echo "<b>Schedule for $Batch_query[batch_name] over $Batch_query[Course_subject]</b><br>\r\n";
	echo "Batch In-charge is $Batch_query[Incharge] and Room Alloted is $Batch_query[room_no]\r\n";
	?>
	</header>
	<form name="slot_form" action="print_sch.php" method="POST">
	<?php //function writeTableSchPrint($serial, $batch_query, $connection) {
	$sch_batch = mysqli_query($connection, "SELECT * FROM `schedule` where Batch_no = '$serial'");
	$x = 1;	?>
	<table class="data-table"> <tbody>
		<tr class="data-heading">
		<th>Date</th>
		<?php $batch_slotA = mysqli_fetch_assoc(mysqli_query($connection, "SELECT slot_name, start_time, end_time FROM `slot_details` WHERE slot_type='$Batch_query[batch_type]' AND slot_name like '%A'"));
		$batch_slotB = mysqli_fetch_assoc(mysqli_query($connection, "SELECT slot_name, start_time, end_time FROM `slot_details` WHERE slot_type='$Batch_query[batch_type]' AND slot_name like '%B'"));
		$batch_slotC = mysqli_fetch_assoc(mysqli_query($connection, "SELECT slot_name, start_time, end_time FROM `slot_details` WHERE slot_type='$Batch_query[batch_type]' AND slot_name like '%C'"));
		$batch_slotD = mysqli_fetch_assoc(mysqli_query($connection, "SELECT slot_name, start_time, end_time FROM `slot_details` WHERE slot_type='$Batch_query[batch_type]' AND slot_name like '%D'"));
		echo "<th>$batch_slotA[slot_name] ($batch_slotA[start_time] to $batch_slotA[end_time])</th>\r\n";
		echo "<th>$batch_slotB[slot_name] ($batch_slotB[start_time] to $batch_slotB[end_time])</th>\r\n";
		echo '<th><div style="display:none;">L</div></th>';
		echo "<th>$batch_slotC[slot_name] ($batch_slotC[start_time] to $batch_slotC[end_time])</th>\r\n";
		echo "<th>$batch_slotD[slot_name] ($batch_slotD[start_time] to $batch_slotD[end_time])</th>\r\n";
		?>
		</tr>
		<?php	while($sch_batchqr = mysqli_fetch_assoc($sch_batch)) {
		if($sch_batchqr[SlotA_sub]=="" && $sch_batchqr[SlotB_sub]=="" && $sch_batchqr[SlotC_sub]=="" && $sch_batchqr[SlotD_sub]=="") {
			continue;
		}	?>
		<tr>
		<td class="center"><input readonly="" type="text" class="print" name="Day<?php echo $x?>" size="12" value="<?php echo $sch_batchqr[date]?>" />
		<td class="center"><button class="print" name="slotA_Sub_day<?php echo $x?>"><?php echo ($sch_batchqr[SlotA_sub]!="")?$sch_batchqr[SlotA_sub]." (in ".$sch_batchqr[SlotA_room].")<br>".$sch_batchqr[SlotA_lec1]."+".$sch_batchqr[SlotA_lec2]:"NA";?></button>	</td>
		<td class="center"><button class="print" name="slotB_Sub_day<?php echo $x?>"><?php echo ($sch_batchqr[SlotB_sub]!="")?$sch_batchqr[SlotB_sub]." (in ".$sch_batchqr[SlotB_room].")<br>".$sch_batchqr[SlotB_lec1]."+".$sch_batchqr[SlotB_lec2]:"NA";?></button>	</td>
		<td><div style="display:none;">L</div></td>
		<td class="center"><button class="print" name="slotC_Sub_day<?php echo $x?>"><?php echo ($sch_batchqr[SlotC_sub]!="")?$sch_batchqr[SlotC_sub]." (in ".$sch_batchqr[SlotC_room].")<br>".$sch_batchqr[SlotC_lec1]."+".$sch_batchqr[SlotC_lec2]:"NA";?></button>	</td>
		<td class="center"><button class="print" name="slotD_Sub_day<?php echo $x?>"><?php echo ($sch_batchqr[SlotD_sub])?$sch_batchqr[SlotD_sub]." (in ".$sch_batchqr[SlotD_room].")<br>".$sch_batchqr[SlotD_lec1]."+".$sch_batchqr[SlotD_lec2]:"NA";?></button>	</td>
		<?php $store_details = $sch_batchqr[details]; ?>
		</tr>
		<?php	$x++;
		}	?>
		<tr><td colspan="6">
		<input readonly="" type="text" name="details" size="120" value="*<?php echo $store_details?>" />
		<input type="hidden" id="Batch_serial" name="Batch_serail" value="<?php echo $serial ?>" />
		</td>	</tr>
	</tbody></table>
	<footer> <br>
	<img src="images/footer.jpg" alt="Footer Image" style="width:963px;height:156px;"> </footer>

	<button class="no-print" onclick="javascript:window.print()"><strong>Print this form</strong></button>
	<!--<button class="no-print" onclick="Myprint()"><strong>Print this form</strong></button> -->
	</form>
	</article> </br>

</section> <!-- End of mainc div -->
</div> <!-- container -->
</div> <!--wrapper -->
<footer>
</footer>
<!-- =================End Document======================= -->
</body>
</html>