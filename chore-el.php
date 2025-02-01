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
<script src="chore-el_shim.js"></script>
<script>
    var input = document;
    var todayDate = new Date();
	fullYear = todayDate.getFullYear();
	currentMonth = todayDate.getMonth()+1;
	currentDateNumber = todayDate.getDate();
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
		<div class="rowLongTerm center" id="choreListDump">

		</div>
		<hr>
		<div id="addChoreLocation">
		<p>New Chore <input style="width: 320px;" name="chore" id="genericInput" type="text" value="enter chorename"> </p>

		</div>
		</br>
	</div>
	<div class="rightColumn">
		<img src="assets/logo.png" alt="logo" width="384" height="256" >
		<hr>
		<div class="rowInfoControls">
			<button class="open-button" onclick="openForm()">Add Chore</button>
			<button class="button">Find A Chore</button>
			<button class="button">Show This Month</button>
			<button class="button">Randomizer!</button>
			<button class="button">Admin</button>

		</div>
		<hr>
		<div id="auto_load_time" class="dateText">
			<p>location 1</p>
      	</div>


	</div>
	</div>

<script>
        $(document).ready(function() {
	        auto_load_date();
			setInterval(auto_load_date, 23000);
            retrieveChoreList();
        });
		
        function auto_load_date() {
            var d = new Date();
            var minutesString = d.getMinutes();
            minutesString = minutesString<10 ? "0" + minutesString : minutesString;
            var timeString = "<span class=dateFormat>" + d.toDateString() + " </span>";
            timeString = timeString + "<span class=timeFormat> " + d.getHours() + ":" + minutesString + "</span> <br>";
            document.getElementById("auto_load_time").innerHTML = timeString;
        }

</script>

</body>
</html>