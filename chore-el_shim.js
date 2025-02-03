
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
    $.post("chore-el_interface.php", { action: "addChore", choreName:  newChoreName, choreFrequency: newChoreFrequency, choreNotes: newChoreNotes},
        function(data) {
			retrieveChoreList();
        });
}