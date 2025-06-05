<?php
session_start();

//logout.php

include('config.php');

//Reset OAuth access token
$google_client->revokeToken();

//Destroy entire session data.
session_destroy();

//redirect page to index.php

session_unset();
header("Location: index.php");
exit();

?>
