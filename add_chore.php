<?php
?>
<script type="text/javascript" src="chore-el_shim.js"></script>
<?php

// should be able to take action for commiting to the DB then use the below line to jump back to the main page
createNewChore($choreName, $choreFreq);

header("Location: https://www.northridge-studios.com/chore-el/chore-el.php", true, 301);  
exit();  
?>

