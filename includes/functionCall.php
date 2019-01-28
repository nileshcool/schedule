<?php
function test_input($data) {
  if(is_array($data)) {
  	foreach ($data as $key => $value) {
	 $data[$key] = trim($data[$key]);
	 $data[$key] = stripslashes($data[$key]);
	 $data[$key] = htmlspecialchars($data[$key]);
	 return $data;
  	}
  } else {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
  }
}
function writeTableEmpty($serial, $all_property, $connection, $post_data = array()) {	//for schedule to display table
	$get_day = mysqli_fetch_assoc(mysqli_query($connection, "select room_no, day(dur_from) AS day, month(dur_from) AS month, year(dur_from) AS year, DATEDIFF(dur_to,dur_from)+1 AS DateDiff FROM `batch_details` WHERE serial=$serial"));
	//echo "select room_no, day(dur_from) AS day, month(dur_from) AS month, year(dur_from) AS year, DATEDIFF(dur_to,dur_from)+1 AS DateDiff FROM `batch_details` WHERE serial=$serial";
//	var_dump($get_day);
	$get_dayMonthName = date("F", mktime(0, 0, 0, $get_day[month], 10));
	//https://stackoverflow.com/questions/18467669/convert-number-to-month-name-in-php
	//http://php.net/mktime & http://php.net/manual/en/function.time.php	-about mktime & time function
?>
	<table class="data-table"> <tbody>
	<tr class="data-heading">
	<td>Date</td>
	<td>Slot A</td>
	<td>Slot B</td>
	<td>Slot C</td>
	<td>Slot D</td>
	</tr>
<?php	//<button onclick="myFunction('Harry Potter','Wizard')">Try it</button>
	//$all_property_duration = intval($all_property[duration]);
	if(strcasecmp($all_property[dur_unit], "Month") == 0 || strcasecmp($all_property[dur_unit], "Months") == 0) {
		$all_property[duration] = $all_property[duration] * 30 - 2;
	} elseif(strcasecmp($all_property[dur_unit], "Week") == 0 || strcasecmp($all_property[dur_unit], "Weeks") == 0) {
		$all_property[duration] = $all_property[duration] * 7 - 2;
	}	//Block was for below for loop (line no. +1), but now difference of dur_from & dur_to is used
	for ($x = 1; $x <= $get_day[DateDiff]; $x++) {
	$date_result = mysqli_query($connection, "SELECT DATE_ADD(dur_from,INTERVAL $x-1 DAY) AS todayDate FROM `batch_details` WHERE serial=$all_property[serial]");
	$today_date = mysqli_fetch_assoc($date_result);
	$second_sat_mon = gmdate('Y-m-d', strtotime('second sat of '.$get_dayMonthName.' '.$get_day[year]));
	if(date('w',strtotime($today_date['todayDate'])) == 0 || $today_date['todayDate'] == $second_sat_mon) continue;
		//discussion on PHP short if statement with continue key word and ommit the else part
		//https://stackoverflow.com/questions/51781855/php-short-if-statement-with-continue-key-word-and-ommit-the-else-part
		//calender jddayofweek function at https://www.w3schools.com/php/func_cal_jddayofweek.asp
		//calendar full list of functions at https://www.w3schools.com/php/php_ref_calendar.asp
	$holiday_list = mysqli_query($connection, "SELECT * FROM `holidays` WHERE year(date)=$get_day[year]");
	while($holiday_listr = mysqli_fetch_assoc($holiday_list)) {
		if($holiday_listr[date] == $today_date['todayDate']) continue;
	}
?>
	<tr>
	<td><input readonly="" type="text" name="Day<?php echo $x?>" size="12" value="<?php echo $today_date['todayDate']?>" />
	<td><input type="text" name="slotA_Sub_day<?php echo $x?>" size="20" value="<?php echo $post_data["slotA_Sub_day".$x];?>" placeholder="Subject or Topic" maxlength="50" />
		<select name="slotA_room_day<?php echo $x?>" id="slotA_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotA_lec1A_day<?php echo $x?>', 'slotA_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
		  <option value="007">SlotA Room</option>
		  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
		  while ($roomqrA = mysqli_fetch_assoc($roomq)) {
		  	if(isset($post_data["slotA_room_day".$x])) {
		  	echo '<option value="'.$roomqrA[name_id].'"'.(($roomqrA[name_id] == $post_data["slotA_room_day".$x])? " selected":"").'>'.$roomqrA[name_id].'-'.$roomqrA[specialization].'</option>';
			} else {
			echo '<option value="'.$roomqrA[name_id].'"'.(($roomqrA[name_id] == $get_day[room_no])? " selected":"").'>'.$roomqrA[name_id].'-'.$roomqrA[specialization].'</option>';
			}
			echo "\r\n";
		  }
		  mysql_free_result($roomq);?>
		</select>	<br>
		<!-- https://stackoverflow.com/questions/35435611/call-2-functions-within-onchange-event
		<input type="text" value={this.state.text} onChange="this.props.onChange(); this.handleChange();" /> -->
		<select name="slotA_lec1_day<?php echo $x?>" id="slotA_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotA_lec2_day<?php echo $x?>', '13', this)">
		  <option value="NIL">SlotA Lect1</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrA = mysqli_fetch_assoc($lectq)) {
		  	echo '<option value="'.$lectqrA[name_id].'"'.((isset($post_data["slotA_lec1_day".$x]) && $lectqrA[name_id] == $post_data["slotA_lec1_day".$x])? " selected":"").'>'.$lectqrA[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotA_lec1A_day<?php echo $x?>" id="slotA_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotA_lec2A_day<?php echo $x?>', '13', this)" style="display: none;">
		  <option value="NIL">SlotA Lect1A</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrA = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrA[name_id].'"'.((isset($post_data["slotA_lec1A_day".$x]) && $lectqrA[name_id] == $post_data["slotA_lec1A_day".$x])? " selected":"").'>'.$lectqrA[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotA_lec2_day<?php echo $x?>" id="slotA_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
		  <option value="NIL">SlotA Lect2</option>
		  <?php if(isset($post_data["slotA_lec2_day".$x])) {
		  		echo '<option value="'.$post_data["slotA_lec2_day".$x].'" selected>'.$post_data["slotA_lec2_day".$x].'</option>';
		  	}	?>
		</select>
		<select name="slotA_lec2A_day<?php echo $x?>" id="slotA_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none">
		  <option value="NIL">SlotA Lect2A</option>
		  <?php if(isset($post_data["slotA_lec2A_day".$x])) {
		  		echo '<option value="'.$post_data["slotA_lec2A_day".$x].'" selected>'.$post_data["slotA_lec2A_day".$x].'</option>';
		  	}	?>
		</select>
<!--		<input type="text" name="slotA_room_day<?php echo $x?>" size="18" placeholder="Room No" maxlength="3" pattern="[0-9]{3}" required>
			<input type="text" name="slotA_lec1_day<?php echo $x?>" size="18" placeholder="Main Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>
			<input type="text" name="slotA_lec2_day<?php echo $x?>" size="18" placeholder="Subs Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>	-->
	</td>
	<td><input type="text" name="slotB_Sub_day<?php echo $x?>" size="20" value="<?php echo $post_data["slotB_Sub_day".$x]?>" placeholder="Subject or Topic" maxlength="50" />
		<select name="slotB_room_day<?php echo $x?>" id="slotB_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotB_lec1A_day<?php echo $x?>', 'slotB_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
		  <option value="007">SlotB Room</option>
		  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
		  while ($roomqrB = mysqli_fetch_assoc($roomq)) {
		  	if(isset($post_data["slotB_room_day".$x])) {
	  		echo '<option value="'.$roomqrB[name_id].'"'.(($roomqrB[name_id] == $post_data["slotB_room_day".$x])? " selected":"").'>'.$roomqrB[name_id].'-'.$roomqrB[specialization].'</option>';
		  	} else {
			echo '<option value="'.$roomqrB[name_id].'"'.(($roomqrB[name_id] == $get_day[room_no])? " selected":"").'>'.$roomqrB[name_id].'-'.$roomqrB[specialization].'</option>';
			}
			echo "\r\n";
		  }
		  mysql_free_result($roomq);	?>
		</select>
		<select name="slotB_lec1_day<?php echo $x?>" id="slotB_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotB_lec2_day<?php echo $x?>', '13', this)">
		  <option value="NIL">SlotB Lect1</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrB = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrB[name_id].'"'.((isset($post_data["slotB_lec1_day".$x]) && $lectqrB[name_id] == $post_data["slotB_lec1_day".$x])? " selected":"").'>'.$lectqrB[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotB_lec1A_day<?php echo $x?>" id="slotB_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotB_lec2A_day<?php echo $x?>', '13', this)" style="display: none;">
		  <option value="NIL">SlotB Lect1A</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrB = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrB[name_id].'"'.((isset($post_data["slotB_lec1A_day".$x]) && $lectqrB[name_id] == $post_data["slotB_lec1A_day".$x])? " selected":"").'>'.$lectqrB[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotB_lec2_day<?php echo $x?>" id="slotB_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
		  <option value="NIL">SlotB Lect2</option>
		  <?php if(isset($post_data["slotB_lec2_day".$x])) {
	  		echo '<option value="'.$post_data["slotB_lec2_day".$x].'" selected>'.$post_data["slotB_lec2_day".$x].'</option>';
		  	}	?>
		</select>
		<select name="slotB_lec2A_day<?php echo $x?>" id="slotB_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none">
		  <option value="NIL">SlotB Lect2A</option>
		  <?php if(isset($post_data["slotA_lec2A_day".$x])) {
		  		echo '<option value="'.$post_data["slotA_lec2A_day".$x].'" selected>'.$post_data["slotA_lec2A_day".$x].'</option>';
		  	}	?>
		</select>
<!--		<input type="text" name="slotB_room_day<?php echo $x?>" size="18" placeholder="Room No" maxlength="3" pattern="[0-9]{3}" required>
			<input type="text" name="slotB_lec1_day<?php echo $x?>" size="18" placeholder="Main Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>
			<input type="text" name="slotB_lec2_day<?php echo $x?>" size="18" placeholder="Subs Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>	-->
	</td>
	<td><input type="text" name="slotC_Sub_day<?php echo $x?>" size="20" value="<?php echo $post_data["slotC_Sub_day".$x]?>" placeholder="Subject or Topic" maxlength="50" />
		<select name="slotC_room_day<?php echo $x?>" id="slotC_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotC_lec1A_day<?php echo $x?>', 'slotC_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
		  <option value="007">SlotC Room</option>
		  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
		  while ($roomqrC = mysqli_fetch_assoc($roomq)) {
		  	if(isset($post_data["slotC_room_day".$x])) {
	  		echo '<option value="'.$roomqrC[name_id].'"'.(($roomqrC[name_id] == $post_data["slotC_room_day".$x])? " selected":"").'>'.$roomqrC[name_id].'-'.$roomqrC[specialization].'</option>';
		  	} else {
			echo '<option value="'.$roomqrC[name_id].'"'.(($roomqrC[name_id] == $get_day[room_no])? " selected":"").'>'.$roomqrC[name_id].'-'.$roomqrC[specialization].'</option>';
			}
			echo "\r\n";
		  }
		  mysql_free_result($roomq);	?>
		</select>
		<select name="slotC_lec1_day<?php echo $x?>" id="slotC_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotC_lec2_day<?php echo $x?>', '13', this)">
		  <option value="NIL">SlotC Lect1</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrC = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrC[name_id].'"'.((isset($post_data["slotC_lec1_day".$x]) && $lectqrC[name_id] == $post_data["slotC_lec1_day".$x])? " selected":"").'>'.$lectqrC[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotC_lec1A_day<?php echo $x?>" id="slotC_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotC_lec2A_day<?php echo $x?>', '13', this)" style="display: none;">
		  <option value="NIL">SlotC Lect1A</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrC = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrC[name_id].'"'.((isset($post_data["slotC_lec1A_day".$x]) && $lectqrC[name_id] == $post_data["slotC_lec1A_day".$x])? " selected":"").'>'.$lectqrC[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotC_lec2_day<?php echo $x?>" id="slotC_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
		  <option value="NIL">SlotC Lect2</option>
		  <?php 	  	if(isset($post_data["slotC_lec2_day".$x])) {
	  		echo '<option value="'.$post_data["slotC_lec2_day".$x].'" selected>'.$post_data["slotC_lec2_day".$x].'</option>';
		  	} ?>
		</select>
		<select name="slotC_lec2A_day<?php echo $x?>" id="slotC_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
		  <option value="NIL">SlotC Lect2A</option>
		  <?php 	  	if(isset($post_data["slotC_lec2A_day".$x])) {
	  		echo '<option value="'.$post_data["slotC_lec2A_day".$x].'" selected>'.$post_data["slotC_lec2A_day".$x].'</option>';
		  	} ?>
		</select>
<!--		<input type="text" name="slotC_room_day<?php echo $x?>" size="18" placeholder="Room No" maxlength="3" pattern="[0-9]{3}" required>
			<input type="text" name="slotC_lec1_day<?php echo $x?>" size="18" placeholder="Main Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>
			<input type="text" name="slotC_lec2_day<?php echo $x?>" size="18" placeholder="Subs Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>	-->
	</td>
	<td><input type="text" name="slotD_Sub_day<?php echo $x?>" size="20" value="<?php echo $post_data["slotD_Sub_day".$x] ?>" placeholder="Subject or Topic" maxlength="50" />
		<select name="slotD_room_day<?php echo $x?>" id="slotD_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotD_lec1A_day<?php echo $x?>', 'slotD_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
		  <option value="007">SlotD Room</option>
		  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
		  while ($roomqrD = mysqli_fetch_assoc($roomq)) {
		  	if(isset($post_data["slotD_room_day".$x])) {
		  		echo '<option value="'.$roomqrD[name_id].'"'.(($roomqrD[name_id] == $post_data["slotD_room_day".$x])? " selected":"").'>'.$roomqrD[name_id].'-'.$roomqrD[specialization].'</option>';
		  	} else {
				echo '<option value="'.$roomqrD[name_id].'"'.(($roomqrD[name_id] == $get_day[room_no])? " selected":"").'>'.$roomqrD[name_id].'-'.$roomqrD[specialization].'</option>';
			}
			echo "\r\n";
		  }
		  mysql_free_result($roomq);	?>
		</select>
		<select name="slotD_lec1_day<?php echo $x?>" id="slotD_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotD_lec2_day<?php echo $x?>', '13', this)">
		  <option value="NIL">SlotD Lect1</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrD = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrD[name_id].'"'.((isset($post_data["slotD_lec1_day".$x]) && $lectqrD[name_id] == $post_data["slotD_lec1_day".$x])? " selected":"").'>'.$lectqrD[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotD_lec1A_day<?php echo $x?>" id="slotD_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotD_lec2A_day<?php echo $x?>', '13', this)" style="display: none;">
		  <option value="NIL">SlotD Lect1A</option>
		  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
		  while($lectqrD = mysqli_fetch_assoc($lectq)) {
	  		echo '<option value="'.$lectqrD[name_id].'"'.((isset($post_data["slotD_lec1A_day".$x]) && $lectqrD[name_id] == $post_data["slotD_lec1A_day".$x])? " selected":"").'>'.$lectqrD[name].'</option>';
			echo "\r\n";
		  }
		  mysql_free_result($lectq);  ?>
  		  <option value="EXT">External Lecturer</option>
		</select>
		<select name="slotD_lec2_day<?php echo $x?>" id="slotD_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
		  <option value="NIL">SlotD Lect2</option>
		  <?php if(isset($post_data["slotD_lec2_day".$x])) {
		  		echo '<option value="'.$post_data["slotD_lec2_day".$x].'" selected>'.$post_data["slotD_lec2_day".$x].'</option>';
			}	?>
		</select>
		<select name="slotD_lec2A_day<?php echo $x?>" id="slotD_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
		  <option value="NIL">SlotD Lect2A</option>
		  <?php if(isset($post_data["slotD_lec2A_day".$x])) {
		  		echo '<option value="'.$post_data["slotD_lec2A_day".$x].'" selected>'.$post_data["slotD_lec2A_day".$x].'</option>';
			}	?>
		</select>
<!--		<input type="text" name="slotD_room_day<?php echo $x?>" size="18" placeholder="Room No" maxlength="3" pattern="[0-9]{3}" required>
			<input type="text" name="slotD_lec1_day<?php echo $x?>" size="18" placeholder="Main Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>
			<input type="text" name="slotD_lec2_day<?php echo $x?>" size="18" placeholder="Subs Lecturer ID" maxlength="3" pattern="[A-Za-z]{3}" required>	-->
	</td>
	</tr>
<?php	}	//end of for loop at line 18
?>
		<tr><td colspan="5"><input type="text" name="details" size="113" placeholder="Details if any is to given here" value="<?php echo $post_data[details] ?>">
		<input type="hidden" id="Month" name="Month" value="<?php echo $get_day[month] ?>" />
		<input type="hidden" id="Year" name="Year" value="<?php echo $get_day[year] ?>" />
		<input type="hidden" id="DateDiff" name="DateDiff" value="<?php echo $get_day[DateDiff] ?>" />
		<input type="hidden" id="Batch_serial" name="Batch_serail" value="<?php echo $all_property[serial] ?>" />
		</td>	</tr>
	</tbody></table>
<?php }	//End of Function writeTableEmpty
function writeTableSchdule($serial, $connection, $post_data = array()) {
	$get_day = mysqli_fetch_assoc(mysqli_query($connection, "select room_no, day(dur_from) AS day, month(dur_from) AS month, year(dur_from) AS year FROM `batch_details` WHERE serial=$serial"));
	
	$sch_batch = mysqli_query($connection, "SELECT * FROM `schedule` where Batch_no = '$serial'");
	$x = 1;	?>
	<table class="data-table"> <tbody>
		<tr class="data-heading">
		<td>Date</td>
		<td>Slot A</td>
		<td>Slot B</td>
		<td>Slot C</td>
		<td>Slot D</td>
		</tr>
<?php	while($sch_batchqr = mysqli_fetch_assoc($sch_batch)) {	?>
		<tr>
		<td><input readonly="" type="text" name="Day<?php echo $x?>" size="12" value="<?php echo $sch_batchqr[date]?>" />
		<td><input type="text" name="slotA_Sub_day<?php echo $x?>" size="20" value="<?php echo (isset($post_data["slotA_Sub_day".$x])? $post_data["slotA_Sub_day".$x]: $sch_batchqr[SlotA_sub])?>" placeholder="Subject or Topic" maxlength="50" />
			<select name="slotA_room_day<?php echo $x?>" id="slotA_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotA_lec1A_day<?php echo $x?>', 'slotA_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
			  <option value="007">SlotA Room</option>
			  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
			  while ($roomqrA = mysqli_fetch_assoc($roomq)) {
			  	if(isset($post_data["slotA_room_day".$x])) {
		  		echo '<option value="'.$roomqrA[name_id].'"'.(($roomqrA[name_id] == $post_data["slotA_room_day".$x])?" selected":"").'>'.$roomqrA[name_id].'-'.$roomqrA[specialization].'</option>';
			  	} else {
				echo '<option value="'.$roomqrA[name_id].'"'.(($roomqrA[name_id] == $sch_batchqr[SlotA_room])?" selected":"").'>'.$roomqrA[name_id].'-'.$roomqrA[specialization].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($roomq);?>
			</select>
			<select name="slotA_lec1_day<?php echo $x?>" id="slotA_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotA_lec2_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotA_lec2]?>')">
			  <option value="NIL">SlotA Lect1</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrA = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotA_lec1_day".$x])) {
		  		echo '<option value="'.$lectqrA[name_id].'"'.(($lectqrA[name_id] == $post_data["slotA_lec1_day".$x])?" selected":"").'>'.$lectqrA[name].'</option>';
			  	} else {
				echo '<option value="'.$lectqrA[name_id].'"'.(($lectqrA[name_id] == $sch_batchqr[SlotA_lec1])?" selected":"").'>'.$lectqrA[name].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotA_lec1A_day<?php echo $x?>" id="slotA_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotA_lec2A_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotA_lec2A]?>')" style="display: none;">
			  <option value="NIL">SlotA Lect1A</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrA = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotA_lec1A_day".$x])) {
		  		echo '<option value="'.$lectqrA[name_id].'"'.(($lectqrA[name_id] == $post_data["slotA_lec1A_day".$x])?" selected":"").'>'.$lectqrA[name].'</option>';
			  	} else {
				echo '<option value="'.$lectqrA[name_id].'"'.(($lectqrA[name_id] == $sch_batchqr[SlotA_lec1A])?" selected":"").'>'.$lectqrA[name].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotA_lec2_day<?php echo $x?>" id="slotA_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
			  <option value="NIL">SlotA Lect2</option>
			  <?php if(isset($post_data["slotA_lec2_day".$x])) {
			  		echo '<option value="'.$post_data["slotA_lec2_day".$x].'" selected>'.$post_data["slotA_lec2_day".$x].'</option>';
			  	} else {
			  		echo '<option value="'.$sch_batchqr[SlotA_lec2].'" selected>'.$sch_batchqr[SlotA_lec2].'</option>';
			  	} ?>
			</select>
			<select name="slotA_lec2A_day<?php echo $x?>" id="slotA_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
			  <option value="NIL">SlotA Lect2A</option>
			  <?php if(isset($post_data["slotA_lec2A_day".$x])) {
			  		echo '<option value="'.$post_data["slotA_lec2A_day".$x].'" selected>'.$post_data["slotA_lec2A_day".$x].'</option>';
			  	} elseif(isset($sch_batchqr[SlotA_lec2A])) {
			  		echo '<option value="'.$sch_batchqr[SlotA_lec2A].'" selected>'.$sch_batchqr[SlotA_lec2A].'</option>';
			  	} ?>
			</select>
		</td>
		<td><input type="text" name="slotB_Sub_day<?php echo $x?>" size="20" value="<?php echo (isset($post_data["slotB_Sub_day".$x])? $post_data["slotB_Sub_day".$x]: $sch_batchqr[SlotB_sub]);?>" placeholder="Subject or Topic" maxlength="50" />
			<select name="slotB_room_day<?php echo $x?>" id="slotB_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotB_lec1A_day<?php echo $x?>', 'slotB_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
			  <option value="007">SlotB Room</option>
			  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
			  while ($roomqrB = mysqli_fetch_assoc($roomq)) {
			  	if(isset($post_data["slotB_room_day".$x])) {
		  		echo '<option value="'.$roomqrB[name_id].'"'.(($roomqrB[name_id] == $post_data["slotB_room_day".$x])?" selected":"").'>'.$roomqrB[name_id].'-'.$roomqrB[specialization].'</option>';
			  	} else {
				echo '<option value="'.$roomqrB[name_id].'"'.(($roomqrB[name_id] == $sch_batchqr[SlotB_room])?" selected":"").'>'.$roomqrB[name_id].'-'.$roomqrB[specialization].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($roomq);	?>
			</select>
			<select name="slotB_lec1_day<?php echo $x?>" id="slotB_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotB_lec2_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotB_lec2]?>')">
			  <option value="NIL">SlotB Lect1</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrB = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotB_lec1_day".$x])) {
		  		echo '<option value="'.$lectqrB[name_id].'"'.(($lectqrB[name_id] == $post_data["slotB_lec1_day".$x])?" selected":"").'>'.$lectqrB[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrB[name_id].'"'.(($lectqrB[name_id] == $sch_batchqr[SlotB_lec1])?" selected":"").'>'.$lectqrB[name].'</option>';
			   }
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotB_lec1A_day<?php echo $x?>" id="slotB_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotB_lec2A_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotB_lec2A]?>')" style="display: none;">
			  <option value="NIL">SlotB Lect1A</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrB = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotB_lec1A_day".$x])) {
		  		echo '<option value="'.$lectqrB[name_id].'"'.(($lectqrB[name_id] == $post_data["slotB_lec1A_day".$x])?" selected":"").'>'.$lectqrB[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrB[name_id].'"'.(($lectqrB[name_id] == $sch_batchqr[SlotB_lec1A])?" selected":"").'>'.$lectqrB[name].'</option>';
			   }
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotB_lec2_day<?php echo $x?>" id="slotB_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
			  <option value="NIL">SlotB Lect2</option>
			  <?php if(isset($post_data["slotB_lec2_day".$x])) {
			  		echo '<option value="'.$post_data["slotB_lec2_day".$x].'" selected>'.$post_data["slotB_lec2_day".$x].'</option>';
			  	} else {
			  		echo '<option value="'.$sch_batchqr[SlotB_lec2].'" selected>'.$sch_batchqr[SlotB_lec2].'</option>';
			  	} ?>
			</select>
			<select name="slotB_lec2A_day<?php echo $x?>" id="slotB_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
			  <option value="NIL">SlotB Lect2A</option>
			  <?php if(isset($post_data["slotB_lec2A_day".$x])) {
			  		echo '<option value="'.$post_data["slotB_lec2A_day".$x].'" selected>'.$post_data["slotB_lec2A_day".$x].'</option>';
			  	} elseif(isset($sch_batchqr[SlotB_lec2A])) {
			  		echo '<option value="'.$sch_batchqr[SlotB_lec2A].'" selected>'.$sch_batchqr[SlotB_lec2A].'</option>';
			  	} ?>
			</select>
		</td>
		<td><input type="text" name="slotC_Sub_day<?php echo $x?>" size="20" value="<?php echo (isset($post_data["slotC_Sub_day".$x])? $post_data["slotC_Sub_day".$x]: $sch_batchqr[SlotC_sub]);?>" placeholder="Subject or Topic" maxlength="50" />
			<select name="slotC_room_day<?php echo $x?>" id="slotC_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotC_lec1A_day<?php echo $x?>', 'slotC_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
			  <option value="007">SlotC Room</option>
			  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
			  while ($roomqrC = mysqli_fetch_assoc($roomq)) {
			  	if(isset($post_data["slotC_room_day".$x])) {
		  		echo '<option value="'.$roomqrC[name_id].'"'.(($roomqrC[name_id] == $post_data["slotC_room_day".$x])?" selected":"").'>'.$roomqrC[name_id].'-'.$roomqrC[specialization].'</option>';
			  	} else {
				echo '<option value="'.$roomqrC[name_id].'"'.(($roomqrC[name_id] == $sch_batchqr[SlotC_room])?" selected":"").'>'.$roomqrC[name_id].'-'.$roomqrC[specialization].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($roomq);	?>
			</select>
			<select name="slotC_lec1_day<?php echo $x?>" id="slotC_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotC_lec2_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotC_lec2]?>')">
			  <option value="NIL">SlotC Lect1</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrC = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotC_lec1_day".$x])) {
		  		echo '<option value="'.$lectqrC[name_id].'"'.(($lectqrC[name_id] == $post_data["slotC_lec1_day".$x])?" selected":"").'>'.$lectqrC[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrC[name_id].'"'.(($lectqrC[name_id] == $sch_batchqr[SlotC_lec1])?" selected":"").'>'.$lectqrC[name].'</option>';
			  	}
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotC_lec1A_day<?php echo $x?>" id="slotC_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotC_lec2A_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotC_lec2A]?>')" style="display: none;">
			  <option value="NIL">SlotC Lect1A</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrC = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotC_lec1A_day".$x])) {
		  		echo '<option value="'.$lectqrC[name_id].'"'.(($lectqrC[name_id] == $post_data["slotC_lec1A_day".$x])?" selected":"").'>'.$lectqrC[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrC[name_id].'"'.(($lectqrC[name_id] == $sch_batchqr[SlotC_lec1A])?" selected":"").'>'.$lectqrC[name].'</option>';
			  	}
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotC_lec2_day<?php echo $x?>" id="slotC_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
			  <option value="NIL">SlotC Lect2</option>
			  <?php if(isset($post_data["slotC_lec2_day".$x])) {
			  		echo '<option value="'.$post_data["slotC_lec2_day".$x].'" selected>'.$post_data["slotC_lec2_day".$x].'</option>';
			  	} else {
			  		echo '<option value="'.$sch_batchqr[SlotC_lec2].'" selected>'.$sch_batchqr[SlotC_lec2].'</option>';
			  	} ?>
			</select>
			<select name="slotC_lec2A_day<?php echo $x?>" id="slotC_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
			  <option value="NIL">SlotC Lect2A</option>
			  <?php if(isset($post_data["slotC_lec2A_day".$x])) {
			  		echo '<option value="'.$post_data["slotC_lec2A_day".$x].'" selected>'.$post_data["slotC_lec2A_day".$x].'</option>';
			  	} elseif(isset($sch_batchqr[SlotC_lec2A])) {
			  		echo '<option value="'.$sch_batchqr[SlotC_lec2A].'" selected>'.$sch_batchqr[SlotC_lec2A].'</option>';
			  	} ?>
			</select>
		</td>
		<td><input type="text" name="slotD_Sub_day<?php echo $x?>" size="20" value="<?php echo (isset($post_data["slotD_Sub_day".$x])? $post_data["slotD_Sub_day".$x]: $sch_batchqr[SlotD_sub]);?>" placeholder="Subject or Topic" maxlength="50" />
			<select name="slotD_room_day<?php echo $x?>" id="slotD_room_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="roomTypeCheck('slotD_lec1A_day<?php echo $x?>', 'slotD_lec2A_day<?php echo $x?>', 'roomTypeCheck', this)">
			  <option value="007">SlotD Room</option>
			  <?php 	$roomq = mysqli_query($connection, "SELECT name_id, specialization FROM `resource` WHERE type in ('11', '12')");
			  while ($roomqrD = mysqli_fetch_assoc($roomq)) {
			  	if(isset($post_data["slotD_room_day".$x])) {
		  		echo '<option value="'.$roomqrD[name_id].'"'.(($roomqrD[name_id] == $post_data["slotD_room_day".$x])?" selected":"").'>'.$roomqrD[name_id].'-'.$roomqrD[specialization].'</option>';
			  	} else {
				echo '<option value="'.$roomqrD[name_id].'"'.(($roomqrD[name_id] == $sch_batchqr[SlotD_room])?" selected":"").'>'.$roomqrD[name_id].'-'.$roomqrD[specialization].'</option>';
				}
				echo "\r\n";
			  }
			  mysql_free_result($roomq);	?>
			</select>
			<select name="slotD_lec1_day<?php echo $x?>" id="slotD_lec1_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotD_lec2_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotD_lec2]?>')">
			  <option value="NIL">SlotD Lect1</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrD = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotD_lec1_day".$x])) {
		  		echo '<option value="'.$lectqrD[name_id].'"'.(($lectqrD[name_id] == $post_data["slotD_lec1_day".$x])?" selected":"").'>'.$lectqrD[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrD[name_id].'"'.(($lectqrD[name_id] == $sch_batchqr[SlotD_lec1])?" selected":"").'>'.$lectqrD[name].'</option>';
			  }
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotD_lec1A_day<?php echo $x?>" id="slotD_lec1A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" onblur="callresource('slotD_lec2A_day<?php echo $x?>', '13', this, '<?php echo $sch_batchqr[SlotD_lec2A]?>')" style="display: none;">
			  <option value="NIL">SlotD Lect1A</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13' and available='Y'");
			  while($lectqrD = mysqli_fetch_assoc($lectq)) {
			  	if(isset($post_data["slotD_lec1A_day".$x])) {
		  		echo '<option value="'.$lectqrD[name_id].'"'.(($lectqrD[name_id] == $post_data["slotD_lec1A_day".$x])?" selected":"").'>'.$lectqrD[name].'</option>';
			  	} else {
			  	echo '<option value="'.$lectqrD[name_id].'"'.(($lectqrD[name_id] == $sch_batchqr[SlotD_lec1A])?" selected":"").'>'.$lectqrD[name].'</option>';
			  }
				echo "\r\n";
			  }
			  mysql_free_result($lectq);  ?>
			</select>
			<select name="slotD_lec2_day<?php echo $x?>" id="slotD_lec2_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)">
			  <option value="NIL">SlotD Lect2</option>
			  <?php if(isset($post_data["slotD_lec2_day".$x])) {
			  		echo '<option value="'.$post_data["slotD_lec2_day".$x].'" selected>'.$post_data["slotD_lec2_day".$x].'</option>';
			  	} else {
			  		echo '<option value="'.$sch_batchqr[SlotD_lec2].'" selected>'.$sch_batchqr[SlotD_lec2].'</option>';
			  	} ?>
			</select>
			<select name="slotD_lec2A_day<?php echo $x?>" id="slotD_lec2A_day<?php echo $x?>" onchange="checkForBooking('checkForBooking','<?php echo $today_date['todayDate']?>', this)" style="display: none;">
			  <option value="NIL">SlotD Lect2A</option>
			  <?php if(isset($post_data["slotD_lec2A_day".$x])) {
			  		echo '<option value="'.$post_data["slotD_lec2A_day".$x].'" selected>'.$post_data["slotD_lec2A_day".$x].'</option>';
			  	} elseif(isset($sch_batchqr[SlotD_lec2A])) {
			  		echo '<option value="'.$sch_batchqr[SlotD_lec2A].'" selected>'.$sch_batchqr[SlotD_lec2A].'</option>';
			  	} ?>
			</select>
		</td>
		</tr>
<?php	$sch_batchqr_detail = $sch_batchqr[details];
	$x++;
	}	?>
		<tr><td colspan="5"><input type="text" name="details" size="113" placeholder="Details if any is to given here" value="<?php echo $sch_batchqr_detail?>">
		<input type="hidden" id="Month" name="Month" value="<?php echo $get_day[month] ?>" />
		<input type="hidden" id="Year" name="Year" value="<?php echo $get_day[year] ?>" />
		<input type="hidden" id="Batch_serial" name="Batch_serail" value="<?php echo $serial ?>" />
		</td>	</tr>
	</tbody></table>
<?php	}	//End of Function writeTableSchdule
function writeTableSchView($serial, $all_property, $connection) {
	$get_day = mysqli_fetch_assoc(mysqli_query($connection, "select day(dur_from) AS day, month(dur_from) AS month, year(dur_from) AS year FROM `batch_details` WHERE serial=$serial"));
	
	$sch_batch = mysqli_query($connection, "SELECT * FROM `schedule` where Batch_no = '$serial'");
	$x = 1;	?>
	<table class="data-table"> <tbody>
		<tr class="data-heading">
		<td>Date</td>
		<td>Slot A</td>
		<td>Slot B</td>
		<td>Slot C</td>
		<td>Slot D</td>
		</tr>
<?php	while($sch_batchqr = mysqli_fetch_assoc($sch_batch)) {	?>
		<tr>
		<td><input readonly="" type="text" name="Day<?php echo $x?>" size="12" value="<?php echo $sch_batchqr[date]?>">
		<td><input readonly="" type="text" name="slotA_Sub_day<?php echo $x?>" size="20" value="<?php echo $sch_batchqr[SlotA_sub]?>">
			<input readonly="" type="text" name="slotA_room_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotA_room]?>">
			<input readonly="" type="text" name="slotA_lec1_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotA_lec1]?>">
			<input readonly="" type="text" name="slotA_lec2_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotA_lec2]?>">
		</td>
		<td><input readonly="" type="text" name="slotB_Sub_day<?php echo $x?>" size="20" value="<?php echo $sch_batchqr[SlotB_sub]?>">
			<input readonly="" type="text" name="slotB_room_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotB_room]?>">
			<input readonly="" type="text" name="slotB_lec1_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotB_lec1]?>">
			<input readonly="" type="text" name="slotB_lec2_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotB_lec2]?>">
		</td>
		<td><input readonly="" type="text" name="slotC_Sub_day<?php echo $x?>" size="20" value="<?php echo $sch_batchqr[SlotC_sub]?>">
			<input readonly="" type="text" name="slotC_room_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotC_room]?>">
			<input readonly="" type="text" name="slotC_lec1_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotC_lec1]?>">
			<input readonly="" type="text" name="slotC_lec2_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotC_lec2]?>">
		</td>
		<td><input readonly="" type="text" name="slotD_Sub_day<?php echo $x?>" size="20" value="<?php echo $sch_batchqr[SlotD_sub]?>">
			<input readonly="" type="text" name="slotD_room_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotD_room]?>">
			<input readonly="" type="text" name="slotD_lec1_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotD_lec1]?>">
			<input readonly="" type="text" name="slotD_lec2_day<?php echo $x?>" size="18" value="<?php echo $sch_batchqr[SlotD_lec2]?>">
		</td>
		<?php $store_details = $sch_batchqr[details]; ?>
		</tr>
<?php	$x++;
	}	?>
		<tr><td colspan="5">
		<input readonly="" type="text" name="details" size="113" value="<?php echo $store_details?>" />
		<input type="hidden" id="Month" name="Month" value="<?php echo $get_day[month] ?>" />
		<input type="hidden" id="Year" name="Year" value="<?php echo $get_day[year] ?>" />
		<input type="hidden" id="Batch_serial" name="Batch_serail" value="<?php echo $serial ?>" />
		</td>	</tr>
	</tbody></table>
<?php }	//End of writeTableSchView
function addBatchTable($batch_resq = array(), $connection) {	//for Batch details to show/ Modify Batch details
?>
	<table class="data-table">
		<tbody>
		<tr class="data-heading">
		<td>Input Type</td>
		<td>Input Value</td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Batch no</td>
			<td><input type="text" name="batch_no" id="batch_no" size="20" placeholder="Batch No" value="<?php echo $batch_resq[batch_no]; ?>" maxlength="8" pattern="[A-Za-z\d]{4,}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Batch name</td>
			<td><input type="text" name="batch_name" id="batch_name" size="38" placeholder="Batch Name" value="<?php echo $batch_resq[batch_name]; ?>" maxlength="50" pattern=".{6,}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Batch Incharge</td>
			<td><select name="Incharge" id="Incharge">
			  <option value="NIL">Select Lecturer</option>
			  <?php 	$lectq = mysqli_query($connection, "SELECT name_id, name FROM `resource` WHERE type='13'");
			  while($lectqr = mysqli_fetch_assoc($lectq)) {
			  	echo '<option value="'.$lectqr[name_id].'"'.($batch_resq[Incharge] == $lectqr[name_id] ? " selected" : "").'>'.$lectqr[name].'</option>';
				echo "\r\n";
			  }
//			  mysql_free_result($lectq);  ?>
			</select> </td>
		<!--	<td><input type="text" name="Incharge" id="Incharge" size="20" placeholder="Batch Incharge" value="<?php echo $batch_resq[Incharge]; ?>" maxlength="3" pattern="[A-Za-z]{3}" required></td>	-->
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Batch type</td>
			<td><select name="batch_type" id="batch_type">
			  <option value="NIL">Select Batch Type</option>
			  <?php 	$batch_typeq = mysqli_query($connection, "SELECT * FROM `slot_details` WHERE slot_name like '%A' ");
			  while($batch_typeqr = mysqli_fetch_assoc($batch_typeq)) {
			  	echo '<option value="'.$batch_typeqr[slot_type].'"'.($batch_resq[batch_type] == $batch_typeqr[slot_type] ? " selected":"").'>'.$batch_typeqr[slot_details].'</option>';
				echo "\r\n";
			  }
//			  mysql_free_result($batch_typeq);  ?>
			
			</select></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Room No</td>
			<td><select name="room_no" id="room_no">
			  <option value="NIL">Select Room No</option>
			  <?php 	$room_typeq = mysqli_query($connection, "SELECT * FROM `resource` WHERE type='11'");
			  while($room_typeqr = mysqli_fetch_assoc($room_typeq)) {
			  	echo '<option value="'.$room_typeqr[name_id].'"'.($batch_resq[room_no] == $room_typeqr[name_id] ? " selected":"").'>'.$room_typeqr[name_id].' - '.$room_typeqr[name].'</option>';
				echo "\r\n";
			  }	?>
			</select></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Ext Lect Required?</td>
			<td><select name="ext_lect" id="ext_lect">
			  <option value="NIL">Select Yes or No</option>
			  <?php echo '<option value="Y"'.(($batch_resq[ext_lect] == "Y")? " selected":"").'>Yes</option>';
			  echo '<option value="N"'.(($batch_resq[ext_lect] == "N")? " selected":"").'>No</option>';	?>
			</select></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Course id</td>
			<td><input type="text" name="Course_id" id="Course_id" size="20" placeholder="Course ID from Course Details" value="<?php echo $batch_resq[Course_id]; ?>" maxlength="10" pattern="[A-Za-z\d]{10}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Duration</td>
			<td><input type="text" name="duration" id="duration" size="20" placeholder="Duration" value="<?php echo $batch_resq[duration]; ?>" maxlength="3" pattern="[\d]{1,}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Duration Unit</td>
			<td><select name="dur_unit" id="dur_unit">
			  <option value="NIL">Select Duration Unit</option>
			  <?php echo '<option value="DAY"'.((strcasecmp($batch_resq[dur_unit],"day") == 0)? " selected":"").'>Day</option>';
			  echo '<option value="DAYS"'.((strcasecmp($batch_resq[dur_unit],"days") == 0)? " selected":"").'>Days</option>';
			  echo '<option value="WEEK"'.((strcasecmp($batch_resq[dur_unit],"week") == 0)? " selected":"").'>Week</option>';
			  echo '<option value="WEEKS"'.((strcasecmp($batch_resq[dur_unit],"weeks") == 0)? " selected":"").'>Weeks</option>';	?>
			</select></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Period from</td>
			<td><input type="text" name="dur_from" id="dur_from" size="20" placeholder="date YYYY-MM-DD" value="<?php echo $batch_resq[dur_from]; ?>" maxlength="10" pattern=".{10}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Period to</td>
			<td><input type="text" name="dur_to" id="dur_to" size="20" placeholder="date YYYY-MM-DD" value="<?php echo $batch_resq[dur_to]; ?>" onblur="pop_calendar('dur_from','dur_to')" maxlength="10" pattern=".{10}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">Calendar</td>
			<td><span id="Calendar_sel"></span>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">No Of Students</td>
			<td><input type="text" name="stud_cnt" id="stud_cnt" size="20" placeholder="No Of Students" value="<?php echo $batch_resq[stud_cnt]; ?>" maxlength="3" pattern="[\d]{1,}"></td>
		</tr>
	</tbody>
	</table>
<?php	}	//End of addBatchTable
function addCourseTable($course_resq = array()) {	//for course_details to show/ edit course details
?>
	<!-- https://stackoverflow.com/questions/37635718/pattern-regex-inclusion-special-characters
	Only \S matches special characters, \w only matches [a-zA-Z0-9_].-->
	<table class="data-table">
		<tbody>
		<tr class="data-heading">
		<td>Input Type</td>
		<td>Input Value</td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">faculty</td>
			<td><input type="text" name="faculty" id="faculty" size="20" placeholder="faculty" value="<?php echo $course_resq[faculty]; ?>" maxlength="2" pattern="[A-Z]{2}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">course_cat</td>
			<td><input type="text" name="course_cat" id="course_cat" size="20" placeholder="course cat O/M" value="<?php echo $course_resq[course_cat]; ?>" maxlength="1" pattern="[A-Z]{1}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">course_code</td>
			<td><input type="text" name="course_code" id="course_code" size="20" placeholder="course code" value="<?php echo $course_resq[course_code]; ?>" maxlength="10" pattern="[A-Z\d]{10}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">course_name</td>
			<td><input type="text" name="course_name" id="course_name" size="50" placeholder="course name" value="<?php echo $course_resq[course_name]; ?>" maxlength="80" pattern="[\w\S\s]{10,}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">course_type</td>
			<td><input type="text" name="course_type" id="course_type" size="20" placeholder="course type" value="<?php echo $course_resq[course_type]; ?>" maxlength="10" pattern="[A-Za-z\d]{8,}" required></td>
		</tr>
		<tr class="data-table">
			<td style="padding-left: 1em">duration</td>
			<td><input type="text" name="duration" id="duration" size="20" placeholder="duration" value="<?php echo $course_resq[duration]; ?>" maxlength="12" pattern="[A-Za-z\d\s]{6,}" required></td>
		</tr>
		</tbody>
	</table>
<?php }	//End of addCourseTable
function yearMonthShow($year_batch, $month_batch, $connection) {	//for showing Year and Month drop-down
	echo 'Select Year <select name="year_batch" id="year_batch">
		<option value="Year">Select Year from list</option>';
		for ($x = 2018; $x <= 2020; $x++) {
		echo '<option value="'.$x.'"'.($x == $year_batch ? " selected" : "").'>'.$x.'</option>';
		echo "\r\n";
		}
	echo "</select>\r\n";
	$array_Month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	echo 'Select Month <select name="month_batch" id="month_batch" onchange="showBatch('."'article1','article1'".')">
		<option value="Month">Select Month from list</option>';
		echo "\r\n";
		for ($x=1; $x <= 12; $x++) {
			echo '<option value="'.$x.'"'.($x == $month_batch ? " selected" : "").'>'.$array_Month[$x-1].'</option>';
			echo "\r\n";
		}
	echo "</select> <br>\r\n";
}	//End of yearMonthShow

function checkForSch($all_property, $post_value, $connection) {

/*	if(strcasecmp($all_property[dur_unit], "Month") == 0 || strcasecmp($all_property[dur_unit], "Months") == 0) {
		$all_duration = $all_property[duration] * 30 - 2;
	} elseif(strcasecmp($all_property[dur_unit], "Week") == 0 || strcasecmp($all_property[dur_unit], "Weeks") == 0) {
		$all_duration = $all_property[duration] * 7 - 2;
	} else {
		$all_duration = $all_property[duration];
	}
*/
	$all_duration = $post_value[DateDiff];
	
	$insert_stmt = "";	//initialize insert statement to be augmented for insert query into schedule
	$insert_warning = array();  //declare an array for saving warning messages
	$insert_error = array();  //declare an array for saving errors

	for($x=1; $x<=$all_duration; $x++) {
	 if(!isset($post_value["Day".$x])) continue;
	 if($post_value[modify] == "modify") {
		$check_query = mysqli_query($connection, "SELECT * FROM schedule WHERE date= '".$post_value["Day".$x]."' and Batch_no != $all_property[serial]");
	 } else {
		$check_query = mysqli_query($connection, "SELECT * FROM schedule WHERE date= '".$post_value["Day".$x]."'");
	 }
	 while ($check_query_res = mysqli_fetch_assoc($check_query)) {
		if("007" == $post_value[("slotA_room_day".$x)]) {
			array_push($insert_error, "Slot A Room No cannot be NULL for ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotA_room] == $post_value[("slotA_room_day".$x)]) {
			array_push($insert_error, "Slot A Room No Already booked for ".$post_value["Day".$x]);  //save error to array
		}
		if("NIL" == $post_value[("slotA_lec1_day".$x)]) {
			array_push($insert_error, "slotA lecturer-1 on day".$x." cannot be NIL in Slot A on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotA_lec1] == $post_value[("slotA_lec1_day".$x)] && "NIL" != $post_value[("slotA_lec1_day".$x)]) {
			array_push($insert_error, $post_value[("slotA_lec1_day".$x)]." is already booked as Lecturer 1 in Slot A on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotA_lec1] == $post_value[("slotA_lec2_day").$x] && "NIL" != $post_value[("slotA_lec2_day").$x]) {
			array_push($insert_error, $post_value[("slotA_lec2_day".$x)]." is already booked as Lecturer 1 in Slot A on ".$post_value["Day".$x]);  //save warning to array
		}
		if("NIL" == $post_value[("slotA_lec2_day".$x)]) {
			array_push($insert_warning, "slotA lecturer-2 on day".$x." cannot be NIL in Slot A on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotA_lec2] == $post_value[("slotA_lec1_day".$x)] && "NIL" != $post_value[("slotA_lec1_day".$x)]) {
			array_push($insert_warning, $post_value[("slotA_lec1_day".$x)]." has booking as Lecturer 2 in Slot A on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotA_lec2] == $post_value[("slotA_lec2_day").$x] && "NIL" != $post_value[("slotA_lec2_day").$x]) {
			array_push($insert_warning, $post_value[("slotA_lec2_day").$x]." has booking as Lecturer 2 in slot A as Lecturer 2 on ".$post_value["Day".$x]);  //save warning to array
		}

		if("007" == $post_value[("slotB_room_day".$x)]) {
			array_push($insert_error, "Slot B Room No cannot be NULL for ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotB_room] == $post_value[("slotB_room_day".$x)]) {
			array_push($insert_error, "Slot B Room No Already booked for ".$post_value["Day".$x]);  //save error to array
		}
		if("NIL" == $post_value[("slotB_lec1_day".$x)]) {
			array_push($insert_error, "slotB lecturer-1 on day".$x." cannot be NIL in Slot B on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotB_lec1] == $post_value[("slotB_lec1_day".$x)] && "NIL" != $post_value[("slotB_lec1_day".$x)]) {
			array_push($insert_error, $post_value[("slotB_lec1_day".$x)]." is already booked as Lecturer 1 in Slot B on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotB_lec1] == $post_value[("slotB_lec2_day".$x)] && "NIL" != $post_value[("slotB_lec2_day".$x)]) {
			array_push($insert_error, $post_value[("slotB_lec2_day").$x]." is already booked as Lecturer 1 in Slot B on ".$post_value["Day".$x]);  //save warning to array
		}
		if("NIL" == $post_value[("slotB_lec2_day".$x)]) {
			array_push($insert_warning, "slotB lecturer-2 on day".$x." cannot be NIL in Slot B on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotB_lec2] == $post_value[("slotB_lec1_day".$x)] && "NIL" != $post_value[("slotB_lec1_day".$x)]) {
			array_push($insert_warning, $post_value[("slotB_lec1_day".$x)]." has booking as Lecturer 2 in Slot B on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotB_lec2] == $post_value[("slotB_lec2_day").$x] && "NIL" != $post_value[("slotB_lec2_day").$x]) {
			array_push($insert_warning, $post_value[("slotB_lec2_day").$x]." has booking as Lecturer 2 in slot B as Lecturer 2 on ".$post_value["Day".$x]);  //save warning to array
		}

		if("007" == $post_value[("slotC_room_day".$x)]) {
			array_push($insert_error, "Slot C Room No cannot be NULL for ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotC_room] == $post_value[("slotC_room_day".$x)]) {
			array_push($insert_error, "Slot C Room No Already booked for".$post_value["Day".$x]);  //save error to array
		}
		if("NIL" == $post_value[("slotC_lec1_day".$x)]) {
			array_push($insert_error, "slotC lecturer-1 on day".$x." cannot be NIL in Slot C on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotC_lec1] == $post_value[("slotC_lec1_day".$x)] && "NIL" != $post_value[("slotC_lec1_day".$x)]) {
			array_push($insert_error, $post_value[("slotC_lec1_day".$x)]." is already booked as Lecturer 1 in Slot C on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotC_lec1] == $post_value[("slotC_lec2_day").$x] && "NIL" != $post_value[("slotC_lec2_day").$x]) {
			array_push($insert_error, $post_value[("slotC_lec2_day".$x)]." is already booked as Lecturer 1 in Slot C on ".$post_value["Day".$x]);  //save warning to array
		}
		if("NIL" == $post_value[("slotC_lec2_day").$x]) {
			array_push($insert_warning, "slotC lecturer-2 on day".$x." cannot be NIL in Slot C on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotC_lec2] == $post_value[("slotC_lec1_day".$x)] && "NIL" != $post_value[("slotC_lec1_day".$x)]) {
			array_push($insert_warning, $post_value[("slotC_lec1_day").$x]." has booking as Lecturer 2 in slot C on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotC_lec2] == $post_value[("slotC_lec2_day").$x] && "NIL" != $post_value[("slotC_lec2_day").$x]) {
			array_push($insert_warning, $post_value[("slotC_lec2_day").$x]." has booking as Lecturer 2 in Slot C on ".$post_value["Day".$x]);  //save warning to array
		}

		if("007" == $post_value[("slotD_room_day".$x)]) {
			array_push($insert_error, "Slot D Room No cannot be NULL for ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotD_room] == $post_value[("slotD_room_day".$x)]) {
			array_push($insert_error, "Slot D Room No Already booked for ".$post_value["Day".$x]);  //save error to array
		}
		if("NIL" == $post_value[("slotD_lec1_day".$x)]) {
			array_push($insert_error, "slotD lecturer-1 on day".$x." cannot be NIL in Slot D on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotD_lec1] == $post_value[("slotD_lec1_day".$x)] && "NIL" != $post_value[("slotD_lec1_day".$x)]) {
			array_push($insert_error, $post_value[("slotD_lec1_day".$x)]." is already booked as Lecturer 1 in Slot D on ".$post_value["Day".$x]);  //save error to array
		}
		if($check_query_res[SlotD_lec1] == $post_value[("slotD_lec2_day").$x] && "NIL" != $post_value[("slotD_lec2_day").$x]) {
			array_push($insert_error, $post_value[("slotD_lec2_day".$x)]." is already booked as Lecturer 1 in Slot D on ".$post_value["Day".$x]);  //save warning to array
		}
		if("NIL" == $post_value[("slotD_lec2_day").$x]) {
			array_push($insert_warning, "slotD lecturer-2 on day".$x." cannot be NIL in Slot D on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotD_lec2] == $post_value[("slotD_lec1_day".$x)] && "NIL" != $post_value[("slotD_lec1_day".$x)]) {
			array_push($insert_warning, $post_value[("slotD_lec1_day").$x]." has booking as Lecturer 2 in slot D as Lecturer 2 on ".$post_value["Day".$x]);  //save warning to array
		}
		if($check_query_res[SlotD_lec2] == $post_value[("slotD_lec2_day").$x] && "NIL" != $post_value[("slotD_lec2_day").$x]) {
			array_push($insert_warning, $post_value[("slotD_lec2_day").$x]." has booking as Lecturer 2 in Slot D on ".$post_value["Day".$x]);  //save warning to array
		}
	 }

	 if(empty($insert_error)) {
		//Lect2A!="NIL" validation fails in case of Modify as no value is echo'ed out
	  if($post_value[("slotA_lec1A_day".$x)] != "NIL") $post_value[("slotA_lec1_day".$x)] = $post_value[("slotA_lec1_day".$x)]."&".$post_value[("slotA_lec1A_day".$x)];
	  if($post_value[("slotB_lec1A_day".$x)] != "NIL") $post_value[("slotB_lec1_day".$x)] = $post_value[("slotB_lec1_day".$x)]."&".$post_value[("slotB_lec1A_day".$x)];
	  if($post_value[("slotC_lec1A_day".$x)] != "NIL") $post_value[("slotC_lec1_day".$x)] = $post_value[("slotC_lec1_day".$x)]."&".$post_value[("slotC_lec1A_day".$x)];
	  if($post_value[("slotD_lec1A_day".$x)] != "NIL") $post_value[("slotD_lec1_day".$x)] = $post_value[("slotD_lec1_day".$x)]."&".$post_value[("slotD_lec1A_day".$x)];
	  if($post_value[("slotA_lec2A_day".$x)] != "NIL") $post_value[("slotA_lec2_day".$x)] = $post_value[("slotA_lec2_day".$x)]."&".$post_value[("slotA_lec2A_day".$x)];
	  if($post_value[("slotB_lec2A_day".$x)] != "NIL") $post_value[("slotB_lec2_day".$x)] = $post_value[("slotB_lec2_day".$x)]."&".$post_value[("slotB_lec2A_day".$x)];
	  if($post_value[("slotC_lec2A_day".$x)] != "NIL") $post_value[("slotC_lec2_day".$x)] = $post_value[("slotC_lec2_day".$x)]."&".$post_value[("slotC_lec2A_day".$x)];
	  if($post_value[("slotD_lec2A_day".$x)] != "NIL") $post_value[("slotD_lec2_day".$x)] = $post_value[("slotD_lec2_day".$x)]."&".$post_value[("slotD_lec2A_day".$x)];

	  if (!isset($post_value[modify])) {
		//details about the query https://stackoverflow.com/questions/51702171/prepare-sqlite-insert-statement-inside-php-for-loop
		// if Day1 was a contant $insert_stmt = "INSERT INTO schedule VALUES (‘".$all_property[serial]."’, ‘".$_POST[constant("Day".$x)]."’)";
		$insert_stmt .= "INSERT INTO `schedule` (`Batch_no`, `date`, `SlotA_sub`, `SlotA_room`, `SlotA_lec1`, `SlotA_lec2`, `SlotB_sub`, `SlotB_room`,
		`SlotB_lec1`, `SlotB_lec2`, `SlotC_sub`, `SlotC_room`, `SlotC_lec1`, `SlotC_lec2`, `SlotD_sub`, `SlotD_room`, `SlotD_lec1`, `SlotD_lec2`, `details`)
		VALUES ('".$all_property[serial]."', '".$post_value[("Day".$x)]."',
		'".$post_value[("slotA_Sub_day".$x)]."', '".$post_value[("slotA_room_day".$x)]."', '".$post_value[("slotA_lec1_day".$x)]."', '".$post_value[("slotA_lec2_day").$x]."',
		'".$post_value[("slotB_Sub_day".$x)]."', '".$post_value[("slotB_room_day".$x)]."', '".$post_value[("slotB_lec1_day".$x)]."', '".$post_value[("slotB_lec2_day").$x]."',
		'".$post_value[("slotC_Sub_day".$x)]."', '".$post_value[("slotC_room_day".$x)]."', '".$post_value[("slotC_lec1_day".$x)]."', '".$post_value[("slotC_lec2_day").$x]."',
		'".$post_value[("slotD_Sub_day".$x)]."', '".$post_value[("slotD_room_day".$x)]."', '".$post_value[("slotD_lec1_day".$x)]."', '".$post_value[("slotD_lec2_day").$x]."',
		'$post_value[details]');";
	  } elseif ($post_value[modify] == "modify") {
		$insert_stmt .= "update schedule set SlotA_sub='".$post_value[("slotA_Sub_day".$x)]."',
		SlotA_room='".$post_value[("slotA_room_day".$x)]."', SlotA_lec1='".$post_value[("slotA_lec1_day".$x)]."', SlotA_lec2='".$post_value[("slotA_lec2_day").$x]."',
		SlotB_sub='".$post_value[("slotB_Sub_day".$x)]."', SlotB_room='".$post_value[("slotB_room_day".$x)]."', SlotB_lec1='".$post_value[("slotB_lec1_day".$x)]."', SlotB_lec2='".$post_value[("slotB_lec2_day").$x]."',
		SlotC_sub='".$post_value[("slotC_Sub_day".$x)]."', SlotC_room='".$post_value[("slotC_room_day".$x)]."', SlotC_lec1='".$post_value[("slotC_lec1_day".$x)]."', SlotC_lec2='".$post_value[("slotC_lec2_day").$x]."',
		SlotD_sub='".$post_value[("slotD_Sub_day".$x)]."', SlotD_room='".$post_value[("slotD_room_day".$x)]."', SlotD_lec1='".$post_value[("slotD_lec1_day".$x)]."', SlotD_lec2='".$post_value[("slotD_lec2_day").$x]."',
		details='$post_value[details]' where Batch_no='$all_property[serial]' and date='".$post_value[("Day".$x)]."';";
	  }
	 }
	}	//end of for loop for checking data and prepairing query
//echo $insert_stmt;
//break;	//cheking of statements for inserting "&" for Lect2A, could not replicate the scenario
return array($insert_warning, $insert_error, $insert_stmt);
}	//End of checkForSch

function addLectDet($lect_resq) {	?>
	<table class="data-table">
	<tbody>
	<tr class="data-heading">
		<td>Input Type</td>
		<td>Input Value</td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Name ID</td>
		<td><input type="text" name="name_id" id="name_id" size="20" placeholder="2 Char Name ID" value="<?php echo $lect_resq[name_id]; ?>" maxlength="2" pattern="[A-Z]{2}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Name</td>
		<td><input type="text" name="name" id="name" size="20" placeholder="Lect Name" value="<?php echo $lect_resq[name]; ?>" maxlength="20" pattern="[A-Za-z\s]{5,}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Personal No</td>
		<td><input type="text" name="per_no" id="per_no" size="22" placeholder="Personal No from HR/ ESS" value="<?php echo $lect_resq[per_no]; ?>" maxlength="8" pattern="[\d]{8}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Designation</td>
		<td><input type="text" name="capacity" id="capacity" size="20" placeholder="Designation" value="<?php echo $lect_resq[capacity]; ?>" maxlength="3" pattern="[A-Z]{2,}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Specialization</td>
		<td><input type="text" name="specialization" id="specialization" size="30" placeholder="specialization" value="<?php echo $lect_resq[specialization]; ?>" maxlength="50" pattern="[\w\S\s]{2,}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Mobile</td>
		<td><input type="text" name="Mobile" id="Mobile" size="20" placeholder="Mobile" value="<?php echo $lect_resq[Mobile]; ?>" maxlength="10" pattern="[\d]{10}" required></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Email ID</td>
		<td><input type="email" name="email" id="email" size="20" placeholder="Email ID" value="<?php echo $lect_resq[email] ?>" maxlength="40"></td>
	</tr>
	<tr class="data-table">
		<td style="padding-left: 1em">Available</td>
		<td><input type="radio" name="available" id="available" <?php echo ($lect_resq[available] == "Y"? " checked":"");?> value="Y">Yes
			 <input type="radio" name="available" id="available" <?php echo ($lect_resq[available] == "N"? " checked":"");?> value="N">No
	</tr>
	</tbody>
	</table>
<?php }	//End of addLectDet

?>