function showBatchDetails(eleToChg, case_val, action = "make") {
	//console.log("function showBatchDetails case_val="+case_val+"&action="+action+"&serial="+ele_value);
	var ele_value = document.getElementById("batchData").value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById(eleToChg).innerHTML = this.responseText;
     document.getElementById("Scheduller").innerHTML = "";
    } else {
		//console.log(this.status);  
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&action="+action+"&serial="+ele_value, true);
  xhttp.send();
}
function showBatch(elemIdToGet, case_val) {
	//console.log("function showBatch case_val="+case_val+"&month_batch="+month_batch+"&year_batch="+year_batch);
	var year_batch = document.getElementById("year_batch").value;
	var month_batch = document.getElementById("month_batch").value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById(elemIdToGet).innerHTML = this.responseText;
     document.getElementById("Scheduller").innerHTML = "";
    } else {
		//console.log(this.status);
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&month_batch="+month_batch+"&year_batch="+year_batch, true);
  xhttp.send();
}

function showSchedule(case_val, serial) {
	//console.log("function showSchedule case_val="+case_val+"&serial=" + serial);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("Scheduller").innerHTML = this.responseText;
    } else {
		//console.log(this.status);    	
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&serial=" + serial, true);
  xhttp.send();
}
function checkForBooking(case_val, day, ele_value) {
	//console.log("function checkForBooking case_val="+case_val+"&day="+day+"&elemID="+ele_value.id+"&elem_val="+ele_value.value);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
	  //var result_arr = this.responseText;
	  //if (result_arr.constructor === Array) {	alert("Is Array"); } else { alert("Is not Array"); };
     document.getElementById(ele_value.id).className = this.responseText;
     //document.getElementById(ele_value.id).classList.add(this.responseText);
     //https://stackoverflow.com/questions/195951/change-an-elements-class-with-javascript
     //console.log(result_arr);
    } else {
		//console.log(this.status);
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&day="+day+"&elemID="+ele_value.id+"&elem_val="+ele_value.value, true);
  xhttp.send();
}
function roomTypeCheck(lect1A, lect2A, case_val, elemPassed) {
	//console.log("function roomTypeCheck case_val="+case_val+"&arg11="+elemPassed.value);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     var roomType = this.responseText;
     //console.log(lect1A+lect2A);
     if (roomType == "LAB") {
		document.getElementById(lect1A).style.display = "block";
		document.getElementById(lect2A).style.display = "block";
	  } else {
		document.getElementById(lect1A).selectedIndex = 0;
		document.getElementById(lect1A).style.display = "none";
		document.getElementById(lect2A).selectedIndex = 0;
		document.getElementById(lect2A).style.display = "none";
		/*https://www.aspsnippets.com/Articles/Reset-Clear-DropDownList-selection-selected-value-using-JavaScript-and-jQuery.aspx*/
	  }
    } else {
		//console.log(this.status);    	
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&arg11="+elemPassed.value, true);
  xhttp.send();
}

function callresource(elemIdToGet, typeToGet, elemValue, lect2ID="NIL") {
	//console.log("function callresource case_val=SlotXLect2&type="+typeToGet+"&lectsel="+elemValue.value+"&lect2="+lect2ID);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById(elemIdToGet).innerHTML = this.responseText;
    } else {
		//console.log(this.status);    	
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val=SlotXLect2&type="+typeToGet+"&lectsel="+elemValue.value+"&lect2="+lect2ID, true);
  xhttp.send();
}

function oneGetValue(elemIdToGet, case_val, elemPassed) {
	//console.log("function oneGetValue elemIdToGet="+elemIdToGet+"&case_val="+case_val+"&arg11="+elemPassed.value);
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById(elemIdToGet).innerHTML = this.responseText;
    } else {
		//console.log(this.status);    	
    }
   };
  xhttp.open("GET", "includes/scripts.php?case_val="+case_val+"&arg11="+elemPassed.value, true);
  xhttp.send();
}

function pop_calendar(dur_from,dur_to) {
	console.log("function pop_calender durFrom="+durFrom+"&durTo="+durTo);
	durFrom = document.getElementById(dur_from).value;
	durTo = document.getElementById(dur_to).value;
}
/*for checking form values while typing, is eliminated for FormValidator
//onkeyup="checkForDate(20,<?php echo $today_date['dur_from']?>)"	*/ 
function checkForDate(resType, curValCheck) {
	if (resType == 10) {
		alert("Room type selected for dated "+curValCheck);
	} else if (resType == 20) {
		alert("Lecturer type selected for dated "+curValCheck);
	} else {
		alert("from where do you get this message");
	}
}

//FormValidator new FormValidator(formName, fields, callback)
new FormValidator('slot_form', [{
	 name: 'slotA_room_day1',
	 display: 'slotA_room_day1',
	 rules: 'required|exact_length[3]|numeric'
}, {
    name: 'slotA_lec_day1',
    display: 'slotA_lec_day1',
    rules: 'required|exact_length[3]|alpha'
}, {
    name: 'req',
    display: 'required',
    rules: 'required'
}, {
    name: 'alphanumeric',
    rules: 'alpha_numeric'
}, {
    name: 'password',
    rules: 'required'
}, {
    name: 'password_confirm',
    display: 'password confirmation',
    rules: 'required|matches[password]'
}, {
    name: 'email',
    rules: 'valid_email'
}, {
    name: 'minlength',
    display: 'min length',
    rules: 'min_length[8]'
}, {
    name: 'tos_checkbox',
    display: 'terms of service',
    rules: 'required'
}], function(errors, evt) {
    var SELECTOR_ERRORS = $('.error_box'),
        SELECTOR_SUCCESS = $('.success_box');

    if (errors.length > 0) {
        SELECTOR_ERRORS.empty();

        for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
            SELECTOR_ERRORS.append(errors[i].message + '<br />');
        }

        SELECTOR_SUCCESS.css({ display: 'none' });
        SELECTOR_ERRORS.fadeIn(200);
    } else {
        SELECTOR_ERRORS.css({ display: 'none' });
        SELECTOR_SUCCESS.fadeIn(200);
    }

    if (evt && evt.preventDefault) {
        evt.preventDefault();
    } else if (event) {
        event.returnValue = false;
    }
});
