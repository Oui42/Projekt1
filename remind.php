<?php include("header.php");

if(isset($user)) header("Location: index.php");

if(isset($_GET['remindsuccess']))
	echo successMessage("Wiadomość została wysłana.");

if(isset($_POST['remind'])) {
	$error = array();
	$email = (isset($_POST['email']))? vtxt($_POST['email']) : "";

	if(empty($email))
		$error[] = "Podaj adres e-mail.";

	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		$error[] = "Adres e-mail jest nieprawidłowy.";

	if(mysql_num_rows(mysql_query("SELECT `u_email` FROM `users` WHERE `u_email` = '".$email."'")) == 0)
		$error[] = "Adres e-mail nie istnieje.";

	if(empty($error)) {
		$_user = getUserByEmail($email);
		$code = $_user['u_code'];
		$userid = $_user['u_id'];

		$domain = $set['domain'];
		$webName = $set['webName'];

		$link = "http://".$domain."/remind.php?new&id=$userid&code=$code";
		$topic = $webName." - Nowe hasło";
		$message = "<html><body>
			Wiadomość wygenerowana automatycznie. Prosimy na nią nie odpowiadać.<br><br>

			Jeżeli nie chcesz zrestartować hasła - zignoruj tę wiadomość.<br><br>

			<b>Kliknij w poniższy link, by zrestartować hasło:</b><br>
			<a href='".$link."'>".$link."</a><br><br>

			Pozdrawiamy<br>
			Administracja ".$webName."</body></html>";
		$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$webName.'@' . "\r\n";

		mail($email, $topic, $message, $headers);

		header("Location: remind.php?remindsuccess");
	} else {
		echo "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Wystąpiły następujące błedy:</h3></div><div class='panel-body'><ul>";
		foreach($error as $e)
			echo "<li>".$e."</li>";
		echo "</ul></div></div>";
	}
}

if(isset($_GET['new'])) {
	$id = mysql_real_escape_string($_GET['id']);

	$sql = "SELECT * FROM `users` WHERE `u_id` = '".$id."'";
	$query = mysql_query($sql);

	$row = mysql_fetch_array($query);

	if(mysql_num_rows($query) > 0) {
		if(($_GET['code'] == $row['u_code']) && ($id == $row['u_id'])) {
			if(isset($_POST['restart'])) {
				$error = array();
				$password = (isset($_POST['password']))? vtxt($_POST['password']) : "";
				$passwordr = (isset($_POST['passwordr']))? vtxt($_POST['passwordr']) : "";

				if(empty($password) || empty($passwordr))
					$error[] = "Wypełnij wszystkie pola.";
				if($password != $passwordr)
					$error[] = "Podane hasła nie są identyczne.";
				if(strlen($password) < 5)
					$error[] = "Hasło musi mieć minimum 5 znaków.";

				if(empty($error)) {
					$salt = substr(md5(time()), 0, 5);
					$insertPassword = md5(md5($salt).md5($password));
					$code = substr(md5(time()), 0, 30);

					mysql_query("UPDATE `users` SET `u_password` = '".$insertPassword."', `u_salt` = '".$salt."', `u_code` = '".$code."' WHERE `u_id` = '".$id."'");
					header("Location: auth.php?remindsuccess");
				} else {
					echo '<div class="row"><div class="col s12"><div class="card-panel red"><span class="white-text"><span class="flow-text">Wystąpiły następujące błędy:</span><br>';
					foreach($error as $e)
						echo $e."<br>";
					echo '</span></div></div></div>';
				}
			}
?>

<h4>Nowe hasło</h4>

<hr>

<div class="col-md-offset-3 col-md-6">
	<form class="form-horizontal" method="post" action="">
		<div class="form-group">
			<label for="password" class="col-md-3 control-label">Nowe hasło</label>
			<div class="col-md-9">
				<input type="password" class="form-control" id="password" name="password" placeholder="Hasło">
			</div>
		</div>
		<div class="form-group">
			<label for="passwordr" class="col-md-3 control-label">Powtórz hasło</label>
			<div class="col-md-9">
				<input type="password" class="form-control" id="passwordr" name="passwordr" placeholder="Hasło">
			</div>
		</div>
		<div class="col-md-12 text-center">
			<input class="btn btn-primary" type="submit" name="restart" value="Zmień">
		</div>
	</form>
</div>

<?php 
		} else {
			echo errorMessage("Nieprawidłowe ID użytkownika lub kod z wiadomości e-mail.");
		}
	} else {
		echo errorMessage("Użytkownik o takim ID nie istnieje.");
	}
} else { ?>

<h4>Przypomnij hasło</h4>

<hr>

<div class="col-md-offset-3 col-md-6">
	<form class="form-horizontal" method="post" action="">
		<div class="form-group">
			<label for="email" class="col-md-3 control-label">E-mail</label>
			<div class="col-md-9">
				<input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
			</div>
		</div>
		<div class="col-md-12 text-center">
			<input class="btn btn-primary" type="submit" name="remind" value="Wyślij">
		</div>
	</form>
</div>

<?php } include("footer.php"); ?>