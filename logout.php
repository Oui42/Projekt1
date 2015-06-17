<?php
require 'config.php';

if(isset($user)) {
	session_unset();
	session_destroy();
}

header("Location: index.php");
?>