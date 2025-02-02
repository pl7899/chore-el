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
		<div id="coreSearchArea">
			<p>Search For Chores</p>
			<input style="width: 80px;" name="searchString" id="searchString" type="text" value="SearchString"> 
			<button class="button">Find A Chore</button>
		</div>
		<hr>
		<div id="addChoreLocation">
			<p>Create a New Chore</p>
			<p>
				<input style="width: 80px;" name="choreName" id="addChoreName" type="text" value="chorename"> 
				<input style="width: 60px;" name="choreFreq" id="addChoreFrequency" type="text" value="chorefreq">
				<input style="width: 60px;" name="choreUnknown" id="addChoreUnknown" type="text" value="unknown"> 
				<button class="button">Add Chore</button>
			</p>
			<p>
				<input style="width: 400px;" name="choreNotes" id="addChoreNotes" type="text" value="chorenotes">
			</p>
		</div>
		</br>
	</div>
	<div class="rightColumn">
		<img src="assets/logo.png" alt="logo" width="384" height="256" >
		<hr>
		<div class="rowInfoControls">
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