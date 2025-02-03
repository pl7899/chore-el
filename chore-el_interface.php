<?php
// connect to database 
include('db_functions.php');

$db = db_connect();

$queryString = "SELECT `id`, `taskDescription`, `dateOpened`, DATE_FORMAT(`targetDate`, \"%b-%d\"), DAYNAME(`targetDate`), DATEDIFF(`targetDate`, NOW()), DATE_ADD(`targetDate`, INTERVAL 1 DAY) FROM `todoActions` WHERE `id`>\"200\"";
$findLateTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" AND DATEDIFF(`targetDate`, NOW()) < 0";

if ($_POST['action'] == "retrieveChoreList")
{
	$rows = mysqli_query($db, "SELECT * FROM `todoChores` ");
	echo "<table> <tr> <th>Chore</th> <th>Frequency</th> <th>Last Done</th> <th>Randomize</th> <th>Controls</th> </tr> ";
		while ($row = mysqli_fetch_array($rows)) {
		{
			//echo "<button onclick=\"handleProjectSelection(`" . $row['name'] . "`)\" class=\"button\">" . $row['name'] . "</button>";
			echo "<tr> <td>" .  $row['description'] . "</td> <td>" .  $row['frequencyDays'] . "</td> <td>" .  $row['completeDate'] . "</td> <td>"  .  $row['randomizer'] . "</td> <td>" .  $row['id'] . "</td> </tr> ";
		}
	} 
	echo "</table> ";
}
else if ($_POST['action'] == "addChore")
{
	$chore = $_POST['choreName'];
	$freq = $_POST['choreFrequency'];
	$notes = $_POST['choreNotes'];
	$randomizer = $_POST['randomizer'];
	$chore = mysqli_real_escape_string($db, $chore);
	$freq = mysqli_real_escape_string($db, $freq);

	if ($chore != NULL) {
		$sql = "INSERT INTO todoChores (description, frequencyDays, notes, randomizer)
            VALUES ('$chore', '$freq', '$notes', '$randomizer')";
		mysqli_query($db, $sql);
	}
}
elseif ($_POST['action'] == "exportTasksByWeekNumber")
{
	$weekValue = $_POST['weekValue'];

	exportFreshPlanner($weekValue);

	echo "exportTasksByWeekNumber( " . $weekValue . " )";	
}
elseif ($_POST['action'] == "findTasksByString")
{
	$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`)  FROM `todoActions` WHERE `taskDescription` LIKE '%" . $_POST['searchString'] . "%' ORDER BY `isOpen` DESC, `priority`");
	echo "<p><span style=\"color:var(--strong_text);\">// ----- Tasks Resulting From a Search For " . $_POST['searchString'] . " </span></p>";
	printTaskTable($rows, $db);
}
else if ($_POST['action'] == "deleteTaskByNumber")
{
	$taskToDelete = $_POST['taskID'];
	if ($taskToDelete != NULL) {
		$sql = "DELETE FROM todoActions WHERE id=\"$taskToDelete\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "closeTaskByNumber")
{
	$taskToClose = $_POST['taskID'];
	$date = date_create("");
	$newDate = date_format($date,"Y-m-d");		
	if ($taskToClose != NULL) {
		$sql = "UPDATE todoActions SET isOpen=0, closeDate=\"$newDate\" WHERE id=\"$taskToClose\" ";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "updateTaskTargetDate")
{
	$newDate = $_POST['date'];
	$task = $_POST['taskID'];
	echo "updateTaskTargetDate: " . $newDate . " (" . $task . ") <br>";
	if ($newDate != NULL) {
		$sql = "UPDATE `todoActions` SET `targetDate`=\"$newDate\" WHERE `id`=\"$task\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "updateTaskHeader")
{
	$header = $_POST['header'];
	$task = $_POST['taskID'];
	$task = mysqli_real_escape_string($db, $task);

	echo "updateTaskHeader: " . $header . " (" . $task . ") <br>";
	if ($header != NULL) {
		$sql = "UPDATE `todoActions` SET `taskDescription`=\"$header\" WHERE `id`=\"$task\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "updateTaskNotes")
{
	$notes = $_POST['notes'];
	$notes = mysqli_real_escape_string($db, $notes);
	
	$task = $_POST['taskID'];
	
	echo "updateTaskNotes: " . $notes . " (" . $task . ") <br>";
	if ($notes != NULL) {
		$sql = "UPDATE `todoActions` SET `notes`=\"$notes\" WHERE `id`=\"$task\"";
		mysqli_query($db, $sql);
	}
}
else
{
	echo 'no command';
}
//mysqli_close($db); // this is executed for all if cases

function printTaskTable($rows, $dataBase) {
	$optionsRead = mysqli_query($dataBase, "SELECT * FROM `todoOptions` WHERE `id`=1");
	$options = mysqli_fetch_array($optionsRead);
	$outputSingleBlankLine = 1;
	$previousDateDelta = 99;
	echo "<table class=\"alternateColors\" id=\"taskTable\"><tr><th width=\"7%\"> ID </th><th width=\"13%\"> Project </th><th width=\"5%\">Pri</th>";
	if($options['showDate'] == 1) {
		echo "<th width=\"62%\">Task</th><th width=\"13%\"> Due Date </th>";
	}
	else {
		echo "<th width=\"75%\">Task</th></tr>";
	}
	if($rows == null)
	{
		return;
	}
	while ($row = mysqli_fetch_array($rows)) {
		//var_dump ($row);
		$strikethroughString = "";
		$priorityClassString = "";
		$taskIsOpen = $row['isOpen'];
		$startingFontSize = 14;
		// if the task is open, show no mater what
		// if the task is closed, only show if showClosed is enabled
		if(($taskIsOpen == 1) || (($taskIsOpen != 1) && ($options['showClosed'] == 1)))
		{
			if($taskIsOpen != 1) // test for task being closed
			{
				$strikethroughString = " class='strikethrough' ";
			}
			if($options['fancyTasks'] == 1) 
			{
				$priorityClassString = "class='priority_" . $row['priority'] . "'";
			}
			else 
			{
				$priorityClassString = "class='priority_2'";
			}
			
			$dateDelta = $row['DATEDIFF(`targetDate`, NOW())'];
			if(($outputSingleBlankLine == 1) && (($dateDelta - $previousDateDelta) != 0) && ($previousDateDelta != 99))
			{
				$outputSingleBlankLine = 1;
				echo "<tr>";
				echo "<td align=\"center\"> " . "---" . "</td>";
				echo "<td align=\"center\">" . "---" . "</td>";
				echo "<td align=\"center\">" . "---" . "</td>";
				echo "<td align=\"center\">" . "   --------------" . "</td>";
				echo "<td align=\"center\">" . "---" . "</td></tr>";
			}
			$previousDateDelta = $dateDelta;

			echo "<tr " . $strikethroughString . $priorityClassString . "onclick=\"retrieveTaskForUpdate(" . $row['id'] . ", null)\">";
			echo "<td align=\"center\"> " . $row['id'] . "</td>";
			echo "<td align=\"center\">" . $row['project'] . "</td>";
			echo "<td align=\"center\">" . $row['priority'] . "</td>";
//			echo "<td align=\"center\">" . $outputSingleBlankLine . " " . $dateDelta . " " . $previousDateDelta . "</td>";
			echo "<td align=\"left\">" . $row['taskDescription'] . "</td>";
			if($taskIsOpen != 1)
			{
				// this task is closed, shove out an empty cell for the date since we don't care what the target was now that it is closed
				echo "<td align=\"center\"> </td></tr>";			
			}
			else if($options['showDate'] == 1) 
			{
				
				if($dateDelta > 6) { // if the task is due later than this week, show the date
					echo "<td align=\"center\"> " . $row['DATE_FORMAT(`targetDate`, "%b-%d")'] . "</td></tr>";
				}
				else if ($dateDelta == 0 ) { // not sure why I included yesterdays tasks in today? || $dateDelta == -1) { // if this task is due today
					echo "<td align=\"center\" class=taskListDueToday> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
				}
				else if ($dateDelta < 0) {  // if the task has passed the targetDate
					if($options['fancyTasks'] == 1) 
					{
						echo "<td align=\"center\" class=taskListLateTask font-size:" . ($startingFontSize + (-2 * $dateDelta)) . "px;\"> " . $row['DATE_FORMAT(`targetDate`, "%b-%d")'] .  "</td></tr>";
					}
					else 
					{
						echo "<td align=\"center\" class=taskListDueToday> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
					}
				}
				else { // otherwise show the name of the day
					echo "<td align=\"center\"> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
				}
				
			}
			else 
			{
				echo "</tr>";
			}
		}
	}
	echo "</table>";
}
?>