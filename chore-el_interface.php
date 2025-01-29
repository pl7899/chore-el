<?php
//$Rev: 752 $
// connect to database 
include('db_functions.php');
include('export_functions.php');

$db = db_connect();

$queryString = "SELECT `id`, `taskDescription`, `dateOpened`, DATE_FORMAT(`targetDate`, \"%b-%d\"), DAYNAME(`targetDate`), DATEDIFF(`targetDate`, NOW()), DATE_ADD(`targetDate`, INTERVAL 1 DAY) FROM `todoActions` WHERE `id`>\"200\"";
$findLateTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" AND DATEDIFF(`targetDate`, NOW()) < 0";

if ($_POST['action'] == "retrieveProjectList")
{
	$rows = mysqli_query($db, "SELECT * FROM `todoProjects` WHERE `hidden`=0 ORDER BY `rank` ASC");
	echo "<span style=\"color:var(--strong_text);\">// ----- Projects Currently Tracked </span><br>";
	while ($row = mysqli_fetch_array($rows)) {
		if($row['projName'] == "AllTasks")
		{
			echo "<button onclick=\"handleProjectSelection(`" . $row['projName'] . "`)\" class=\"button\">" . "Mains" . "</button>";		
		}
		else if ($row['projName'] == "break")
		{
			echo "<br>";
		}
		
		else
		{
			echo "<button onclick=\"handleProjectSelection(`" . $row['projName'] . "`)\" class=\"button\">" . $row['projName'] . "</button>";			
		}
	} 
}
elseif ($_POST['action'] == "exportTasksByWeekNumber")
{
	$weekValue = $_POST['weekValue'];

	exportFreshPlanner($weekValue);

	echo "exportTasksByWeekNumber( " . $weekValue . " )";	
}
elseif ($_POST['action'] == "retrieveTaskListForProject")
{
	$optionsRead = mysqli_query($db, "SELECT * FROM `todoOptions` WHERE `id`=1");
	$options = mysqli_fetch_array($optionsRead);
	$rows = mysqli_query($db, "SELECT * FROM `todoProjects` WHERE `projName`=\"" . $_POST['project'] . "\"");

	$projectToRequest = $_POST['project'];
	$rowCount = mysqli_num_rows($rows);  // this is either 1 or 0 and shows if the project was found in the project list
	//debug_to_console("retrieveTaskListForProject: rowCount = " . $rowCount );

	
	if ($rowCount == 0)
	{ 
	    // project is not in the Project list DB
		//debug_to_console("retrieveTaskListForProject: non-existing project (" . $_POST[project] . ") selected falling back to " . $_POST[previousProject]);
		$projectToRequest = $_POST['previousProject'];
		//var_dump($rows);
	} 
	if(strtolower($projectToRequest) == "alltasks")
	{
		$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" ORDER BY DATEDIFF(`targetDate`, NOW()), `project`, `priority`");
		echo $rowCount . "<p><span style=\"color:var(--strong_text);\">// ----- All Active Tasks (" . mysqli_num_rows($rows) . ") </span></p>";
	}
	else if (strtolower($projectToRequest) == "thisweek")
	{
	    // get number of days until monday, or if it is past go back to monday, then query for the tasks with those five days for the target
	    //get todays numerical value
	    
	    $today=date("N"); // N = 1(mon) .. 7(sun)
	    $startDateModifier = 1 - $today; // number of days to add to today to get to Monday
	    if(substr($startDateModifier, 0, 1) == "-")
	    {
			$mondayDate = date('M-j', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
			$mondayDateFullString = date('Y/m/d', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
	    }
	    else
	    {
			$mondayDate = date('M-j', strtotime(' + ' . $startDateModifier . ' days'));
			$mondayDateFullString = date('Y/m/d', strtotime(' + ' . $startDateModifier . ' days'));
		}
	    
	    $fridayDate = date('M-j', strtotime($mondayDate . ' +  4 days'));
		$fridayDateFullString = date('Y/m/d', strtotime($mondayDate . ' +  4 days'));
		
		// I am removing the "work" tasks from the weekly task list, I can always look at the work project for those tasks
		//$findWeeksTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" 
		//					AND (`targetDate` BETWEEN '" . $mondayDateFullString . "' AND '" . $fridayDateFullString . "' OR project='thisweek') ORDER BY `isOpen` DESC, " . $options['entryToSortListBy'];
		$findWeeksTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" 
							AND (`targetDate` BETWEEN '" . $mondayDateFullString . "' AND '" . $fridayDateFullString . "' OR project='thisweek') AND project!='work' ORDER BY `isOpen` DESC, " . $options['entryToSortListBy'];
		$rows = mysqli_query($db, $findWeeksTasks);
		echo $rowCount; 
		//echo "<p>today: " . $today . " startDateModifier: " . $startDateModifier . " modayDate: " . $mondayDate . " mondayDateFullString: " . $mondayDateFullString . " fridayDate: " . $fridayDate . " fridayDateFullString: " . $fridayDateFullString . "sign::" . substr($startDateModifier, 0, 1) . "value: " . substr($startDateModifier, 1, 1);
		echo "<p><span style=\"color:var(--strong_text);\">// ----- All Tasks that are scheduled for " . $mondayDateFullString . " - " . $fridayDateFullString . " (" . mysqli_num_rows($rows) . ") </span></p>";
	}
    else if (strtolower($projectToRequest) == "weekend")
    {
	    // get number of days until friday, or if it is the weekend go back to Friday, then query for the tasks with those three days for the target
	    //get todays numerical value
	    $today=date("N"); // N = 1(mon) .. 7(sun)
	    $startDateModifier = 5 - $today; // number of days to add to today to get to Friday
	    if(substr($startDateModifier, 0, 1) == "-")
	    {
			$fridayDate = date('M-j', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
			$fridayDateFullString = date('Y/m/d', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
	    }
	    else
	    {
			$fridayDate = date('M-j', strtotime(' + ' . $startDateModifier . ' days'));
			$fridayDateFullString = date('Y/m/d', strtotime(' + ' . $startDateModifier . ' days'));
		}
	    
	    $sundayDate = date('M-j', strtotime($fridayDate . ' +  2 days'));
		$sundayDateFullString = date('Y/m/d', strtotime($fridayDate . ' +  2 days'));
		
		$findWeekendTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" 
							AND (`targetDate` BETWEEN '" . $fridayDateFullString . "' AND '" . $sundayDateFullString . "' OR project='weekend') AND project!='work'  ORDER BY `isOpen` DESC, " . $options['entryToSortListBy'];
		$rows = mysqli_query($db, $findWeekendTasks);
		echo $rowCount . "<p><span style=\"color:var(--strong_text);\">// ----- All Tasks that are scheduled for " . $fridayDate . " - " . $sundayDate . " (" . mysqli_num_rows($rows) . ") </span></p>";
    }
	else
	{
		//debug_to_console("command to execute: " . "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `project`=\"" . $projectToRequest . "\" ORDER BY `isOpen` DESC, " . $options['entryToSortListBy']);
		$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `project`=\"" . $projectToRequest . "\" AND `isOpen`=\"1\" ORDER BY `isOpen` DESC, " . $options['entryToSortListBy']);
		$openTasks = mysqli_num_rows($rows);
		$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `project`=\"" . $projectToRequest . "\" ORDER BY `isOpen` DESC, " . $options['entryToSortListBy']);
		echo $rowCount . "<p><span style=\"color:var(--strong_text);\">// ----- (Open/Closed : " . $openTasks . "/" . ((mysqli_num_rows($rows)) - ($openTasks)) . ") Tasks from the " . $projectToRequest . " project</span></p>";
	}
	printTaskTable($rows, $db);
}
elseif ($_POST['action'] == "retrieveLateTasks")
{
	$rows = mysqli_query($db, $findLateTasks);
	echo "<p><span style=\"color:var(--strong_text);\">// ----- Tasks from all projects that are overdue</span></p>";
	printTaskTable($rows, $db);
}
elseif ($_POST['action'] == "findTasksByString")
{
	$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`)  FROM `todoActions` WHERE `taskDescription` LIKE '%" . $_POST['searchString'] . "%' ORDER BY `isOpen` DESC, `priority`");
	echo "<p><span style=\"color:var(--strong_text);\">// ----- Tasks Resulting From a Search For " . $_POST['searchString'] . " </span></p>";
	printTaskTable($rows, $db);
}
else if ($_POST['action'] == "addTask")
{
	$task = $_POST['task'];
	$task = mysqli_real_escape_string($db, $task);

	$priority = $_POST['priority'];
	$project = $_POST['project'];
	if ($task != NULL) {
		$sql = "INSERT INTO todoActions (taskDescription, priority, project, isOpen)
            VALUES ('$task', '$priority', '$project', 1)";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "addProject")
{
	$project = $_POST['project'];
	if ($project != NULL) {
		$sql = "INSERT INTO todoProjects (projName) VALUES ('$project')";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "toggleProjectHiddenBit")
{
	$project = $_POST['project'];
	if ($project != NULL) {
		$hiddenBit = 99;
		$sql = "SELECT * FROM `todoProjects` WHERE `projName`=\"$project\"";
		$rows = mysqli_query($db, $sql);
		$row = mysqli_fetch_array($rows);
		$hidden = $row['hidden'];
		if($hidden == 0)
		{
			$hiddenBit = 1;
		}
		else 
		{
			$hiddenBit = 0;
		}
		echo "input: " . $project . " : hidden from DB(" . $hiddenBit . ") " . $row['hidden'] . "::" . $hiddenBit . "<br>";

		$sql = "UPDATE `todoProjects` SET `hidden`=\"$hiddenBit\" WHERE `projName`=\"$project\"";
		mysqli_query($db, $sql);
	}
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
else if ($_POST['action'] == "delayTaskByNumber")
{
	$taskToDelay = $_POST['taskID'];
	if ($taskToDelay != NULL) {
		$date = date_create("");
		date_add($date, date_interval_create_from_date_string("6 days"));
		$newDate = date_format($date,"Y-m-d");		
		$sql = "UPDATE todoActions SET targetDate=\"$newDate\" WHERE id=\"$taskToDelay\"";		
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "openTaskByNumber")
{
	$taskToOpen = $_POST['taskID'];
	if ($taskToOpen != NULL) {
		$sql = "UPDATE todoActions SET isOpen=1 WHERE id=\"$taskToOpen\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "deleteProjectByName")
{
	$projectToDelete = $_POST['projectName'];
	if ($projectToDelete != NULL) {
		$sql = "DELETE FROM todoProjects WHERE projName=\"$projectToDelete\"";
		mysqli_query($db, $sql);
	}
}
elseif ($_POST['action'] == "generateWeeklyWorkReport")
{
	$optionsRead = mysqli_query($db, "SELECT * FROM `todoOptions` WHERE `id`=1");
	$options = mysqli_fetch_array($optionsRead);
	
	$projectToRequest = "thisweek";
    // get number of days until monday, or if it is past go back to monday, then query for the tasks with those five days for the target
    //get todays numerical value
    
    $today=date("N"); // N = 1(mon) .. 7(sun)
    $startDateModifier = 1 - $today; // number of days to add to today to get to Monday
    if(substr($startDateModifier, 0, 1) == "-")
    {
		$mondayDate = date('M-j', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
		$mondayDateFullString = date('Y/m/d', strtotime(' - ' . substr($startDateModifier, 1, 1) . ' days'));
    }
    else
    {
		$mondayDate = date('M-j', strtotime(' + ' . $startDateModifier . ' days'));
		$mondayDateFullString = date('Y/m/d', strtotime(' + ' . $startDateModifier . ' days'));
	}
    
    $saturdayDate = date('M-j', strtotime($mondayDate . ' +  5 days'));
	$saturdayDateFullString = date('Y/m/d', strtotime($mondayDate . ' +  5 days'));
	$fridayDateFullString = date('Y/m/d', strtotime($mondayDate . ' +  4 days'));
	
	// I am removing the "work" tasks from the weekly task list, I can always look at the work project for those tasks
	//$findWeeksTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE `isOpen`=\"1\" 
	//					AND (`targetDate` BETWEEN '" . $mondayDateFullString . "' AND '" . $fridayDateFullString . "' OR project='thisweek') ORDER BY `isOpen` DESC, " . $options['entryToSortListBy'];
	$findWeeksTasks = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`) FROM `todoActions` WHERE 
						(`targetDate` BETWEEN '" . $mondayDateFullString . "' AND '" . $saturdayDateFullString . "' ) AND project='work' ORDER BY `isOpen` DESC, " . $options['entryToSortListBy'];
	$rows = mysqli_query($db, $findWeeksTasks);
	//echo "<p>today: " . $today . " startDateModifier: " . $startDateModifier . " modayDate: " . $mondayDate . " mondayDateFullString: " . $mondayDateFullString . " fridayDate: " . $fridayDate . " fridayDateFullString: " . $fridayDateFullString . "sign::" . substr($startDateModifier, 0, 1) . "value: " . substr($startDateModifier, 1, 1);
	echo "<p><span style=\"color:var(--strong_text);\">// ----- All WORK Tasks that fall between " . $mondayDateFullString . " and " . $fridayDateFullString . " (" . mysqli_num_rows($rows) . ") </span></p>";
	printWeeklyReportTaskTable($rows, $db);
}
else if ($_POST['action'] == "retrieveTaskForUpdateDisplay")
{
	$projectOfInterest = $_POST['project'];
	$taskOfInterest = $_POST['taskID'];
	if ($taskOfInterest == null) {
		// we didn't get a specific task to display, we will get the first one returned from the active project
		$branch = "grabbed by PROJECT (" . $projectOfInterest . ")";
		if(strtolower($projectOfInterest) == "alltasks")
		{
			$sql = "SELECT * FROM `todoActions` WHERE `isOpen`=\"1\" ORDER BY `project`, `priority`";
		}
		else
		{
			$sql = "SELECT * FROM `todoActions` WHERE `project`=\"" . $projectOfInterest . "\" ORDER BY `isOpen` DESC, `priority`";
		}
	}
	else {
		// we were told what task to display get that info and pass it up
		$branch = "grabbed by TASK";
		$sql = "SELECT * FROM `todoActions` WHERE `id`=" . $taskOfInterest;
	}
	$rows = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($rows);
	$dateTimeOpened = explode(" ", $row['dateOpened']);
	$dateOpened = $dateTimeOpened[0];
	$dateTimeTarget = explode(" ", $row['targetDate']);
	$dateTarget = $dateTimeTarget[0];
	$dateTimeClose = explode(" ", $row['closeDate']);
	$dateClose = $dateTimeClose[0];
	$dependency = $row['dependencies'];
	// debug echo "input: " . $projectOfInterest . " (" . $taskOfInterest . ") " . $branch . "<br>";
	echo "<table style=\"width:100%\">";
	echo "<tr><td width=\"25%\">id:</td><td width=\"75%\" id=\"currentlyActiveTask\">" . $row['id'] . "</td></tr>";
	echo "<tr><td width=\"25%\">priority:</td><td width=\"75%\">" . $row['priority'] . "</td></tr>";
	echo "<tr><td width=\"25%\">project:</td><td width=\"75%\">" . $row['project'] . "</td></tr>";
	//echo "<tr><td width=\"25%\">isOpen:</td><td width=\"75%\">" . $row['isOpen'] . "</td></tr>";
	echo "<tr><td width=\"25%\">dateOpened:</td><td width=\"75%\">" . $dateOpened . "</td></tr>";
	echo "<tr><td width=\"25%\">targetDate:</td><td width=\"75%\">" . $dateTarget . "</td></tr>";
	echo "<tr><td width=\"25%\">closeDate:</td><td width=\"75%\">" . $dateClose . "</td></tr>";
	//echo "<tr><td width=\"25%\">dependency:</td><td width=\"75%\">" . $dependency . "</td></tr>";
	echo "<tr><td width=\"25%\">header:</td><td width=\"75%\" id=\"currentlyActiveTaskHeader\">" . $row['taskDescription'] . "</td></tr>";
	//echo "<tr><td width=\"25%\">notes:</td></tr>";
	echo "</table>";
	echo "<textarea rows=\"28\" style=\"width:90%;margin-left:5%;margin-top:15px;\" id=\"notesInput\" disabled >"  . $row['notes'] . "</textarea>";
}
else if ($_POST['action'] == "updateTaskPriority")
{
	$priority = $_POST['priority'];
	$task = $_POST['taskID'];
	echo "updateTaskPriority: " . $priority . " (" . $task . ") <br>";
	if ($priority != NULL) {
		$sql = "UPDATE `todoActions` SET `priority`=\"$priority\" WHERE `id`=\"$task\"";
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
else if ($_POST['action'] == "batchUpdateTaskTargetDate")
{
	$newDate = $_POST['date'];
	$taskList = $_POST['taskIDList'];
	$array = explode(',', $taskList); //split string into array seperated by ', '

	foreach($array as $taskValue) //loop over values
	{
		$taskTrim=trim($taskValue);
		if ($newDate != NULL) {
			$sql = "UPDATE `todoActions` SET `targetDate`=\"$newDate\" WHERE `id`=\"$taskTrim\"";
			mysqli_query($db, $sql);
		}
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
else if ($_POST['action'] == "updateTaskProject")
{
	$project = $_POST['project'];
	$task = $_POST['taskID'];
	if ($project != NULL) {
		$sql = "UPDATE `todoActions` SET `project`=\"$project\" WHERE `id`=\"$task\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "updateTaskDependency")
{
	$dependency = $_POST['dependency'];
	$task = $_POST['taskID'];
	if ($task != NULL) {
		$sql = "UPDATE `todoActions` SET `dependencies`=\"$dependency\" WHERE `id`=\"$task\"";
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "setOptions")
{
	$newOptions = $_POST['options'];   
	$individualOptions = explode(" ", $newOptions);
	$enable = "";
	
	$showDate = strpos(strtolower($newOptions), "showdate");
	$showClosed = strpos(strtolower($newOptions), "showclosed");
	$sortByDate = strpos(strtolower($newOptions), "sortbydate");
	$sortByPriority = strpos(strtolower($newOptions), "sortbypriority");
	$fancyTasks = strpos(strtolower($newOptions), "fancytasks");
	
	debug_to_console("showDate: " . $showDate);
	debug_to_console("showClosed: " . $showClosed);
	debug_to_console("fancyTasks: " . $fancyTasks);
	
	// Note our use of ===.  Simply == would not work as expected
	// because the position of 'a' was the 0th (first) character.
	if (($showDate === false) & ($showClosed === false) & ($sortByDate === false) & ($sortByPriority === false) & ($fancyTasks === false)) 
	{
		// The options supported were not found in the string '$newOptions'
		debug_to_console("setOptions() no valid option string found");
	}
	else if (($showDate !== false) | ($showClosed !== false) | ($fancyTasks !== false))
	{
		if($showDate !== false)
		{
			$optionString = "showdate";
			$enable = $newOptions[$showDate - 1];
		}
		else if ($showClosed !== false)
		{
			$optionString = "showClosed";
			$enable = $newOptions[$showClosed - 1];
		}
		else 
		{
			$optionString = "fancyTasks";
			$enable = $newOptions[$fancyTasks - 1];
		}
		if ($enable == '+') 
		{
			$sql = "UPDATE `todoOptions` SET `$optionString`=\"1\" WHERE `id`=\"1\""; 
		}
		else
		{
			$sql = "UPDATE `todoOptions` SET `$optionString`=\"0\" WHERE `id`=\"1\""; 
		}
		debug_to_console("command to execute: " . $sql);
		mysqli_query($db, $sql);
	}	
	else if (($sortByDate !== false) | ($sortByPriority !== false))
	{
		if($sortByDate !== false)
		{
			$optionString = "DATEDIFF(`targetDate`, NOW()),`priority`";  // hang the priority on the string so that it ends up in the query as the third sort order
		}
		else 
		{
			$optionString = "priority";
		}
		$sql = "UPDATE `todoOptions` SET `entryToSortListBy`=\"" . $optionString . "\" WHERE `id`=\"1\""; 
		debug_to_console("command to execute: " . $sql);
		mysqli_query($db, $sql);
	}
}
else if ($_POST['action'] == "retrieveThemeDB")
{
	$optionsRead = mysqli_query($db, "SELECT * FROM `todoOptions` WHERE `id`=1");
	$options = mysqli_fetch_array($optionsRead);
	echo $options['theme'];
}
else if ($_POST['action'] == "setThemeDB")
{
	$sql = "UPDATE `todoOptions` SET `theme`=\"" . $_POST['themeToSave'] . "\"WHERE `id`=\"1\""; 
	mysqli_query($db, $sql);
	echo $_POST['themeToSave'];
}
else if ($_POST['action'] == "dumpAllTasks")
{
	$sql = "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\") FROM `todoActions`";
	$rows = mysqli_query($db, $sql);
	echo '[' . '</br>';
	while ($row = mysqli_fetch_array($rows)) {
		echo '&nbsp{' . '</br>';
		echo '&nbsp&nbsp"taskDescription": "' . $row['taskDescription'] . '",</br>';
		echo '&nbsp&nbsp"dateOpened": "' . $row['dateOpened'] . '",</br>';
		echo '&nbsp&nbsp"priority": ' . $row['priority'] . ',</br>';
		echo '&nbsp&nbsp"project": "' . $row['project'] . '",</br>';
		echo '&nbsp&nbsp"isOpen": ' . $row['isOpen'] . ',</br>';
		if($row['notes'] !== "")
		{
			echo '&nbsp&nbsp"notes": "' . $row['notes'] . '",</br>';
		}
		echo '&nbsp&nbsp"targetDate": "' . $row['targetDate'] . '"</br>';
		echo '&nbsp},' . '</br>';
	} 
	echo ']' . '</br>';
}
else if ($_POST['action'] == "outputNotes")
{
	echo ' entered outputNote Functionality';
	$rows = mysqli_query($db, "SELECT *, DATE_FORMAT(`targetDate`, \"%b-%d\"), DATEDIFF(`targetDate`, NOW()), DAYNAME(`targetDate`)  FROM `todoActions` WHERE `taskDescription` LIKE '%" . $_POST['task'] . "%' ORDER BY `isOpen` DESC, `priority`");
	while ($row = mysqli_fetch_array($rows)) {
		echo 'Task Selected : ' . $row['taskDescription'] . '\n';
		
		echo '-----------------------------------------------\n';
		echo '  dateOpened : ' . $row['dateOpened'] . '\n';
		if($row['notes'] !== "")
		{
			echo $row['notes'] . '\n';
		}
		echo '\n';
	}
} 
else if ($_POST['action'] == "getFileVersion")
{
	echo '$Rev: 752 $';
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

function printWeeklyReportTaskTable($rows, $dataBase) {
	$optionsRead = mysqli_query($dataBase, "SELECT * FROM `todoOptions` WHERE `id`=1");
	$options = mysqli_fetch_array($optionsRead);
	
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
	$previousDateDelta = 99;
	$outputSingleBlankLine = 1;
	while ($row = mysqli_fetch_array($rows)) {
		//var_dump ($row);
		$strikethroughString = "";
		$priorityClassString = "";
		$taskIsOpen = $row['isOpen'];
		$startingFontSize = 14;
		// if the task is open, show no mater what
		// if the task is closed, only show if showClosed is enabled
		if($taskIsOpen != 1) // test for task being closed
		{
			$strikethroughString = " class='strikethrough' ";
			$dateDelta = $row['DATEDIFF( NOW(), `closeDate`)'];
		}
		else 
		{
			$dateDelta = $row['DATEDIFF(`targetDate`, NOW())'];
		}
		if($options['fancyTasks'] == 1) 
		{
			$priorityClassString = "class='priority_" . $row['priority'] . "'";
		}
		else 
		{
			$priorityClassString = "class='priority_2'";
		}
		if(($outputSingleBlankLine == 1) && (($dateDelta - $previousDateDelta) != 0) && ($previousDateDelta != 99))
		{
			$outputSingleBlankLine = 1;
			echo "<tr>";
			echo "<td align=\"center\"> " . "---" . "</td>";
			echo "<td align=\"center\">" . "---" . "</td>";
			echo "<td align=\"center\">" . "---" . "</td>";
			echo "<td align=\"center\">" . "   --------------" . "</td>";
//			echo "<td align=\"left\"> a " . $outputSingleBlankLine . " b " . $dateDelta . " c " . $previousDateDelta . " " . "</td>";
			echo "<td align=\"center\">" . "---" . "</td></tr>";
		}
		echo "<tr " . $strikethroughString . $priorityClassString . "onclick=\"retrieveTaskForUpdate(" . $row['id'] . ", null)\">";
		echo "<td align=\"center\"> " . $row['id'] . "</td>";
		echo "<td align=\"center\">" . $row['project'] . "</td>";
		echo "<td align=\"center\">" . $row['priority'] . "</td>";
//		echo "<td align=\"left\"> a " . $outputSingleBlankLine . " b " . $dateDelta . " c " . $previousDateDelta . " " . "</td>";
		echo "<td align=\"left\">" . $row['taskDescription'] . "</td>";		

		$previousDateDelta = $dateDelta;

		if($taskIsOpen != 1)
		{
			// this task is closed, shove out an empty cell for the date since we don't care what the target was now that it is closed
			$dateTimeClose = explode(" ", $row['closeDate']);
			//$dateTimeClose = explode("-", $dateTimeClose[0]);
			//$dateClose = $dateTimeClose[1] . "-" . $dateTimeClose[2];
			//$dateClose = $row['DAYNAME(`closeDate`)'];
			$timestamp = strtotime($dateTimeClose[0]);
			$day = date('l', $timestamp);
			
			echo "<td align=\"center\">" . $day . "</td></tr>";
		}
		else if($options['showDate'] == 1) 
		{
			
			
			if($dateDelta > 6) { // if the task is due later than this week, show the date
				echo "<td align=\"center\"> " . $row['DATE_FORMAT(`targetDate`, "%b-%d")'] . "</td></tr>";
			}
			else if ($dateDelta == 0) { // if this task is due today
				echo "<td align=\"center\" style=\"color: var(--tasklist_due_today);\"> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
			}
			else if ($dateDelta < 0) {  // if the task has passed the targetDate
				if($options['fancyTasks'] == 1) 
				{
					echo "<td align=\"center\" style=\"color: var(--tasklist_late_task); font-size:" . ($startingFontSize + (-2 * $dateDelta)) . "px;\"> " . $row['DATE_FORMAT(`targetDate`, "%b-%d")'] .  "</td></tr>";
				}
				else 
				{
					echo "<td align=\"center\" style=\"color: var(--tasklist_due_today);\"> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
				}
			}
			else { // otherwise show the name of the day
				echo "<td align=\"center\"> " . $row['DAYNAME(`targetDate`)'] .  "</td></tr>";
			}
		}	
	}
	echo "</table>";
}

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}
?>