<!DOCTYPE html>
<html lang="en">
<!-- $Rev: 751 $ -->
<head>
    <link id="mainstyle" rel="stylesheet" type="text/css" href="css/chore-el.css"> </link>
    <title>
       chore-el
    </title>
</head>
<body>
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"> </script>
<script src="chorer-el_shim.js"></script>
<script>
    var input = document;
    var todayDate = new Date();
	fullYear = todayDate.getFullYear();
	currentMonth = todayDate.getMonth()+1;
	currentDateNumber = todayDate.getDate();
    var currentWeek = week(fullYear, todayDate.getMonth()+1, (todayDate.getDate() - todayDate.getDay()));
</script>

	<div class="wholePage">
	<div class="leftColumn">
		<div class="rowHeader">
<!--
			<pre>

	  ___  _  _   __   ____  ____        ____  __   
	 / __)/ )( \ /  \ (  _ \(  __) ___  (  __)(  )  
	( (__ ) __ ((  O ) )   / ) _) (___)  ) _) / (_/\
	 \___)\_)(_/ \__/ (__\_)(____)      (____)\____/
			
			</pre>
-->
		</div>
		<div class="rowLongTerm center">
			<table>
				<tr>
				<th>Chore</th>
				<th>Frequency</th>
				<th>Last Completed</th>
				<th>Control</th>
				</tr>
				<tr>
				<td>Change Bed Clothes</td>
				<td>Monthly</td>
				<td>Jan-25-2025</td>
				<td>
					<button class="button">Edit This Chore</button>
					<button class="button">Completed Today</button>		
				</td>
				</tr>
				<tr>
				<td>Wash Bath Towels</td>
				<td>Monthly</td>
				<td>Jan-25-2025</td>
				<td>
					<button class="button">Edit This Chore</button>
					<button class="button">Completed Today</button>		
				</td>
				</tr>
				<tr>
				<td>Clean Kitchen Floor</td>
				<td>Quarterly</td>
				<td>Jun-01-2024</td>
				<td>
					<button class="button">Edit This Chore</button>
					<button class="button">Completed Today</button>		
				</td>
				</tr>
				<tr>
				<td>Powerwash House</td>
				<td>Every 3 years</td>
				<td>Jun-01-2022</td>
				<td>
					<button class="button">Edit This Chore</button>
					<button class="button">Completed Today</button>		
				</td>
				</tr>
			</table>		
		</div>
		</br>
		<div class="rowNearTerm center">
			<table>
				<tr>
					<td>Clean Half Bath</td>
					<td>Weekly</td>
					<td>Jand-25-2025</td>
					<td>
						<button class="button">Edit This Chore</button>
						<button class="button">Completed Today</button>		
					</td>
				</tr>
				<tr>
					<td>Clean Master Bath</td>
					<td>Weekly</td>
					<td>Jul-25-2025</td>
					<td>
						<button class="button">Edit This Chore</button>
						<button class="button">Completed Today</button>		
					</td>
				</tr>
				<tr>
					<td>Clean Second Bath</td>
					<td>Weekly</td>
					<td>Jul-25-2025</td>
					<td>
						<button class="image-btn">
							<img src="assets/pencil.png" alt="Button Image"  width="32" height="32">
						</button>
						<label class="container">
							<input type="checkbox" checked="checked">
							<span class="checkmark"></span>
						  </label>

					</td>
				</tr>
			</table>	
		</div>
	</div>
	<div class="rightColumn">
		<img src="assets/logo.png" alt="logo" width="384" height="256" >
		<hr>
		<div class="rowInfoControls">
			<button class="button">Add Chore</button>
			<button class="button">Find A Chore</button>
			<button class="button">Show This Month</button>
			<button class="button">Randomizer!</button>
			<button class="button">Admin</button>
		</div>
		<hr>
		<div id="auto_load_time" class="dateText">
			<p>location 1</p>
      	</div>
		<hr>
		<label class="container">One
			<input type="checkbox" checked="checked">
			<span class="checkmark"></span>
		  </label>
		  <label class="container">Two
			<input type="checkbox">
			<span class="checkmark"></span>
		  </label>
		  <label class="container">Three
			<input type="checkbox">
			<span class="checkmark"></span>
		  </label>
		  <label class="container">Four
			<input type="checkbox">
			<span class="checkmark"></span>
		  </label>
		  
	</div>
	<div id="choreListDump">
		chore list area
	</div>
	</div>

<script>
        $(document).ready(function() {
	        auto_load_date();
			setInterval(auto_load_date, 23000);
            retrieveProjectList();
        });
		
        function auto_load_date() {
            var d = new Date();
            var minutesString = d.getMinutes();
            minutesString = minutesString<10 ? "0" + minutesString : minutesString;
            var timestring = "<span class=dateFormat>" + d.toDateString() + "</span>";
            timeString = timeString + "<span class=timeFormat>" + d.getHours() + ":" + minutesString + "</span> <br>";
            document.getElementById("auto_load_time").innerHTML = timeString;
        }

    </script>

</body>
</html>