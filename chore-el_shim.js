//$Rev: 747 $

var lastAcceptedTheme = 0;
var currentShownTheme = 0;
var themes = ["b_orig_th.css", "gb_th.css", "76_th.css", "newspaper_th.css", "limitedclrs_th.css", "c64_th.css", "sublime.css"];

function setupGenericInput(displayString, inputFieldString) {
	$('#section_2').html("<p>" + displayString + "<input style=\"width: 320px;\" name=\"task\" id=\"genericInput\" type=\"text\" value=\"" + inputFieldString + "\"/> </p>");
	document.getElementById("genericInput").focus();
	setCaretPosition(document.getElementById("genericInput"), document.getElementById("genericInput").value.length);
}

function displayStringAtGenericInput(displayString) {
	$('#section_2').html("<p>" + displayString + "<input style=\"width: 320px;\" name=\"task\" id=\"genericInput\" type=\"text\" /> </p>");
}

function setupNotesInput(displayString, inputFieldString) {
	$('#section_2').html("<p>" + displayString + "<input style=\"width:320px;\" name=\"task\" id=\"genericInput\" type=\"text\" disabled value=\"" + inputFieldString + "\"/> </p>");
	document.getElementById('notesInput').disabled = false;
	document.getElementById('notesInput').focus();
}

function disableGenericInput() {
	$('#section_2').html("<p> No Active Command: <br><input style=\"width:320px;\" name=\"task\" id=\"genericInput\" type=\"text\" disabled/> </p>");
}

function disableNotesInput() {
	document.getElementById('notesInput').disabled = true;
}

function retrieveProjectList() {
    $.post("webdo_interface.php", { action: "retrieveProjectList" },
        function(data) {
    	 $('#project_list_buttons').html(data);
        });
}
   
function retrieveTaskForUpdate(taskID, activeProject) {
    $.post("webdo_interface.php", { action: "retrieveTaskForUpdateDisplay", project:  activeProject, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
        });
}

function updateProjectHiddenBit(projectName) {
    $.post("webdo_interface.php", { action: "toggleProjectHiddenBit", project:  projectName },
        function(data) {
         	$('#task_update_area').html(data);
           	handleProjectSelection("AllTasks");
			retrieveProjectList();
		});
}

function exportTasksByWeekNumber(weekNumber) {
    $.post("webdo_interface.php", { action: "exportTasksByWeekNumber", weekValue: weekNumber },
        function(data) {
         	$('#task_update_area').html(data);  // I need to open a separate page here
			window.open('../webdo/export.html', '_blank');
        });
}

function retrieveLateTasks() {
    $.post("webdo_interface.php", { action: "retrieveLateTasks" },
        function(data) {
         	$('#task_list_display_area').html(data);
			retrieveTaskForUpdate(getTaskIDForFirstTaskDisplayedInTable(), null);
        });
 }

function deleteTaskByNumber() {
    var taskToDelete = document.getElementById("genericInput").value;
    if (taskToDelete.length > 0)
    {
		document.getElementById('genericInput').value = '';
	    $.post("webdo_interface.php", { taskID: taskToDelete, action: "deleteTaskByNumber" },
	        function(data) {
				disableGenericInput();  // this only executes on success, what if user inputs non-existant task ID
				handleProjectSelection(lastProjectSetActive);
   			});
   	}
}

function closeTaskByNumber() {
    var taskToClose = document.getElementById("genericInput").value;
    if (taskToClose.length > 0)
    {
		document.getElementById('genericInput').value = '';
	    $.post("webdo_interface.php", { taskID: taskToClose, action: "closeTaskByNumber" },
	        function(data) {
				disableGenericInput();  // this only executes on success, what if user inputs non-existant task ID
				handleProjectSelection(lastProjectSetActive);
   			});
   	}
}

function openTaskByNumber() {
    var taskToOpen = document.getElementById("genericInput").value;
    if (taskToOpen.length > 0)
    {
		document.getElementById('genericInput').value = '';
	    $.post("webdo_interface.php", { taskID: taskToOpen, action: "openTaskByNumber" },
	        function(data) {
				disableGenericInput();  // this only executes on success, what if user inputs non-existant task ID
				handleProjectSelection(lastProjectSetActive);
   			});
   	}
}

function deleteProjectByName() {
    var projectToDelete = document.getElementById("genericInput").value;
    if (projectToDelete.length > 0)
    {
		document.getElementById('genericInput').value = '';
	    $.post("webdo_interface.php", { projectName: projectToDelete, action: "deleteProjectByName" },
	        function(data) {
				disableGenericInput();  // this only executes on success, what if user inputs non-existant task ID
				retrieveProjectList();
   			});
   	}
}

function updateTaskPriority(priority, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskPriority", priority: priority, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	disableGenericInput();
         	handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(taskID, null);
        });
}

function updateTaskTargetDate(targetDate, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskTargetDate", date: targetDate, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	disableGenericInput();
         	handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(taskID, null);
        });
}

function batchTakeAction(taskIDList, targetDate) {
    $.post("webdo_interface.php", { action: "batchUpdateTaskTargetDate", date: targetDate, taskIDList: taskIDList },
        function(data) {
    	 $('#task_update_area').html(data);
         disableGenericInput();
         handleProjectSelection(lastProjectSetActive);
		 retrieveTaskForUpdate(null, lastProjectSetActive);
        });
}

function updateTaskHeader(header, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskHeader", header: header, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	disableGenericInput();
         	handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(taskID, null);
        });
}

function updateTaskProject(project, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskProject", project: project, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	disableGenericInput();
         	handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(taskID, null);
        });
}

function updateTaskDependency(dependency, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskDependency", dependency: dependency, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	disableGenericInput();
         	handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(taskID, null);
        });
}

function updateTaskNotes(notes, taskID) {
    $.post("webdo_interface.php", { action: "updateTaskNotes", notes: notes, taskID: taskID },
        function(data) {
         	$('#task_update_area').html(data);
         	// handleProjectSelection(lastProjectSetActive);  // the project tasks display isn't effected by the notes update, so no need to refresh
			retrieveTaskForUpdate(taskID, null);
         	disableGenericInput();
         	// must wait until the retrieveTaskForUpdate() is called in order for the taskArea ID to exist again.
         	//disableNotesInput();     // Actually no need to disable at all since the php creates the area intially as disabled
        });
}

function handleProjectSelection(projectToActivate){
	if(projectToActivate.charAt(0) == "%")
	{
		findTasksByString(projectToActivate.substring(1, projectToActivate.length));
		lastProjectSetActive = projectToActivate;
	}
	else if(projectToActivate.charAt(0) == "&") // string for displaying late tasks
	{
		retrieveLateTasks();
		lastProjectSetActive = projectToActivate;
	}
	else
	{
		handleProjectSelectionByProject(projectToActivate);		
	}
}

function handleProjectSelectionByProject(projectToActivate) {
    $.post("webdo_interface.php", { action: "retrieveTaskListForProject", project:  projectToActivate, previousProject: lastProjectSetActive},
        function(data) {
	        var success = data.substring(0, 1);
	        data = data.substring(1);
         	$('#task_list_display_area').html(data);
         	if(success == '1')
         	{
	         	lastProjectSetActive = projectToActivate;        	
         	}
         	else
         	{
	         	//lastProjectSetActive = previousProject;
         	}
			retrieveTaskForUpdate(getTaskIDForFirstTaskDisplayedInTable(), null);
        });
}

function findTasksByString(searchString) {
    $.post("webdo_interface.php", { action: "findTasksByString", searchString: searchString },
        function(data) {
         	$('#task_list_display_area').html(data);
         	disableGenericInput();
         	// handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(getTaskIDForFirstTaskDisplayedInTable(), null);
        });
}

function showWeeklyWorkReport(currentWeek) {
	var weekString = "WK_" + currentWeek;
    $.post("webdo_interface.php", { action: "findTasksByString", searchString: weekString },
        function(data) {
         	$('#task_list_display_area').html(data);
         	disableGenericInput();
         	// handleProjectSelection(lastProjectSetActive);
			retrieveTaskForUpdate(getTaskIDForFirstTaskDisplayedInTable(), null);
        });
    $.post("webdo_interface.php", { action: "generateWeeklyWorkReport", searchString: weekString },
        function(data) {
         	$('#task_list_display_area').html(data);
         	disableGenericInput();
         	//handleProjectSelection("work");
		    //retrieveTaskForUpdate(findTasksByStringNoTaskListUpdate(weekString), null);
        });
 	lastProjectSetActive = "work";
}

function getTaskIDForFirstTaskDisplayedInTable() {
	var value = null;
	var table = document.getElementById('taskTable');
	if(table.rows[1] != undefined)
	{
		var cell = table.rows[1].cells[0];
		value = cell.firstChild.data;	
	}
	return value;
}

function createNewTask(lastProjectSetActive) {
    var task = document.getElementById("genericInput").value;
    var outputTask = "";
    var priority = -1;
    var project = lastProjectSetActive;
    var str = task.split(" ");

    if (str.length > 0)
    {
        for(var i=0; i < str.length; i++)
        {
            if(str[i].startsWith("@"))
            {
                project = str[i].slice(1);
            }
            else if(str[i].startsWith("#"))
            {
                priority = str[i].slice(1);
            }
            else
            {
                outputTask = outputTask + str[i] + " ";
            }
        }
    }
    //outputTask = outputTask.replace(/'/g, "''");  // testing a correction on the strings in PHP instead
	document.getElementById('genericInput').value = '';
	$.post("webdo_interface.php", { task: outputTask, priority: priority, project: project, action: "addTask" },
	    function(data) {
	        // if the post is successful update the displayed project tasks
			handleProjectSelection(lastProjectSetActive); // don't force to the project the task was added to, update the list that was last requested
			disableGenericInput()
	   });
}

function createNewProject() {
    var project = document.getElementById("genericInput").value;
    if (project.length > 0)
    {
		document.getElementById('genericInput').value = '';
	    $.post("webdo_interface.php", { project: project, action: "addProject" },
	        function(data) {
		        // if the post is successful update the displayed project tasks
				handleProjectSelection(project);
				retrieveProjectList();
				disableGenericInput();
   			});
   	}
}

function setOptions(newOptions) {
    $.post("webdo_interface.php", { options: newOptions, action: "setOptions" },
        function(data) {
         	$('#console_log_output_area').html(data);
			disableGenericInput();
			handleProjectSelection(lastProjectSetActive);
        });
}

function setCaretPosition(ctrl, pos)
{
	if (ctrl.setSelectionRange)
	{
		ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	}
	else if (ctrl.createTextRange)
	{
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

function getFileRevisionNumber() {
	document.getElementById('file_revision_table').rows[1].cells[1].innerHTML = "$Rev: 747 $";
    $.post("webdo_interface.php", { action: "getFileVersion" },
        function(data) {
//         	$('#task_update_area').html(data);
			document.getElementById('file_revision_table').rows[2].cells[1].innerHTML = data;
		});
}

function changeTheme(direction, overRide)
{
	var numberOfThemes = themes.length - 1;
	currentShownTheme = currentShownTheme + direction;
	if (currentShownTheme < 0)
	{
		currentShownTheme = numberOfThemes;
	}
	else if(currentShownTheme > numberOfThemes)
	{
		currentShownTheme = 0;
	}
	if(overRide !== null)
	{
		currentShownTheme = lastAcceptedTheme;
	}
    $('#mainstyle').replaceWith('<link id="mainstyle" rel="stylesheet" type="text/css" href="' + themes[currentShownTheme] + '"></link>');
}

function retrieveThemeDB()
{
    $.post("webdo_interface.php", { action: "retrieveThemeDB" },
        function(data) {
	        lastAcceptedTheme = themes.indexOf(data);
	        currentShownTheme = lastAcceptedTheme;
			$('#mainstyle').replaceWith('<link id="mainstyle" rel="stylesheet" type="text/css" href="' + data + '"></link>');
		});
}

function setThemeDB()
{
	lastAcceptedTheme = currentShownTheme;
    $.post("webdo_interface.php", { action: "setThemeDB", themeToSave: themes[currentShownTheme]},
        function(data) {
			$('#mainstyle').replaceWith('<link id="mainstyle" rel="stylesheet" type="text/css" href="' + data + '"></link>');
		});
}
