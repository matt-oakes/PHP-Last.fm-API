<?php

// Include the header
include 'template/header.php';

?>

<!-- Write the form to submit the username to results.php -->
<form action="results.php" method="GET">
	<label for="username">Last.fm username:</label>
	<input type="text" name="username" />
	<input type="submit" value="Go" />
</form>

<?php

//Include the footer
include 'template/footer.php';

?>