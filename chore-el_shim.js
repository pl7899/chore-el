//$Rev: 747 $

var lastAcceptedTheme = 0;
var currentShownTheme = 0;

function retrieveChoreList() {
    $.post("chore-el_interface.php", { action: "retrieveChoreList" },
        function(data) {
    	 $('#choreListDump').html(data);
        });
}

