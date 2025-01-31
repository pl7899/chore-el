//$Rev: 747 $

var lastAcceptedTheme = 0;
var currentShownTheme = 0;

function retrieveChoreList() {
    $.post("chore-el_interface.php", { action: "retrieveChoreList" },
        function(data) {
    	 $('#choreListDump').html(data);
        });
}

function createNewChore(choreName, choreFreq) {
    var name = choreName;
	var freq = choreFreq;
    if (choreName.length > 0)
    {
	    $.post("chore-el_interface.php", { name: choreName, freq: choreFreq, action: "addChore" });
   	}
}