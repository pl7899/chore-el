
function retrieveChoreList() {
    $.post("chore-el_interface.php", { action: "retrieveChoreList" },
        function(data) {
    	 $('#choreListDump').html(data);
        });
}

function addChore() {
	newChoreName = document.getElementById('addChoreName').value;
	newChoreFrequency = document.getElementById('addChoreFrequency').innerHTML;
	newChoreNotes = document.getElementById('addChoreNotes').innerHTML;	
    $.post("chore-el_interface.php", { action: "addChore", choreName:  newChoreName, c: newChoreFrequency, choreNotes: newChoreNotes},
        function(data) {
			retrieveChoreList();
        });
}