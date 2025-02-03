
function retrieveChoreList() {
    $.post("chore-el_interface.php", { action: "retrieveChoreList" },
        function(data) {
    	 $('#choreListDump').html(data);
        });
}

function addChore() {
	newChoreName = document.getElementById('addChoreName').value;
	newChoreFrequency = document.getElementById('addChoreFrequency').value;
	newChoreNotes = document.getElementById('addChoreNotes').value;
	newRandomCheckbox = document.getElementById('randomCheckbox').checked;
	
	
	document.getElementById('addChoreName').value = "";
	document.getElementById('addChoreFrequency').value = "";
	document.getElementById('addChoreNotes').value = "";
	document.getElementById('randomCheckbox').checked = "";

    $.post("chore-el_interface.php", { action: "addChore", choreName:  newChoreName, choreFrequency: newChoreFrequency, choreNotes: newChoreNotes, randomizer: newRandomCheckbox},
        function(data) {
			retrieveChoreList();
        });
}