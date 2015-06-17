<?php

function vtxt($var) {
	return trim(mysql_real_escape_string(strip_tags($var)));
}

function row($sql) {
	return mysql_fetch_assoc(mysql_query($sql)); 
}

function getUser($u_id) {
	if(isset($u_id) && is_numeric($u_id)) {
		$ret = row("SELECT * FROM `users` WHERE u_id = '".$u_id."' LIMIT 1");
		return $ret;
	} else {
		return array();
	}
}

if(isset($_SESSION['u_id'])) {
	$user = getUser($_SESSION['u_id']);
}

function successMessage($text) {
	return "<div class='panel panel-success'><div class='panel-heading'><h3 class='panel-title'>Sukces!</h3></div><div class='panel-body'>".$text."</div></div>";
}

function errorMessage($text) {
	return "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Błąd!</h3></div><div class='panel-body'>".$text."</div></div>";
}

function warningMessage($text) {
	return "<div class='panel panel-warning'><div class='panel-heading'><h3 class='panel-title'>Uwaga!</h3></div><div class='panel-body'>".$text."</div></div>";
}

function infoMessage($text) {
	return "<div class='panel panel-primary'><div class='panel-heading'><h3 class='panel-title'>Informacja!</h3></div><div class='panel-body'>".$text."</div></div>";
}