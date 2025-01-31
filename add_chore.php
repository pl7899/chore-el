
<script>

function createNewChore(choreName, choreFreq) {
    var name = choreName;
	var freq = choreFreq;
    if (choreName.length > 0)
    {
	    $.post("chore-el_interface.php", { name: choreName, freq: choreFreq, action: "addChore" });
   	}
}

</script>
<?php

// should be able to take action for commiting to the DB then use the below line to jump back to the main page
createNewChore($_POST['choreName'], $_POST['choreFreq']);

header("Location: https://www.northridge-studios.com/chore-el/chore-el.php", true, 301);  
exit();  
?>