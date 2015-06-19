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

function getUserByEmail($email) {
	if(isset($email)) {
		$ret = row("SELECT * FROM `users` WHERE u_email = '".$email."' LIMIT 1");
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

function sendActivationLink($id) {
	$sql = "SELECT * FROM `users` WHERE `u_id` = '".$id."'";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) > 0) {
		$row = mysql_fetch_array($query);

		$code = $row['u_code'];
		$email = $row['u_email'];
		
		$domain = $set['domain'];
		$webName = $set['webName'];

		$activateLink = "http://".$domain."/auth.php?activate&id=$id&code=$code";
		$topic = $webName." - Link aktywacyjny";
		$message = "<html><body>
			Wiadomość wygenerowana automatycznie. Prosimy na nią nie odpowiadać.<br><br>

			Dziękujemy za zarejestrowanie na <a href=\"http://".$domain."/\">".$webName."</a>,<br><br>

			<b>Kliknij w poniższy link, by aktywować konto:</b><br>
			<a href='".$activateLink."'>".$activateLink."</a><br><br>

			Pozdrawiamy<br>
			Administracja ".$webName."</body></html>";
		$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$webName.'@' . "\r\n";

		mail($email, $topic, $message, $headers);
	} else {
		throw new Exception("Użytkownik podany w pierwszym parametrze $id nie został odnaleziony!");
	}
}
