<?php include("header.php"); 

if(isset($user)) header("Location: index.php");

$error = array();

if(isset($_POST['submitLogin'])) {
	$Lemail = (isset($_POST['Lemail']))? vtxt($_POST['Lemail']) : "";
	$Lpassword = (isset($_POST['Lpassword']))? vtxt($_POST['Lpassword']) : "";

	$sql = "SELECT `u_salt`, `u_id` FROM `users` WHERE `u_email` = '".$Lemail."'";
	$query = mysql_query($sql);
	if(mysql_num_rows($query) > 0) {
		$row = mysql_fetch_assoc($query);
		$pass = md5(md5($row['u_salt']).md5($Lpassword));
		
		$sql = "SELECT * FROM `users` WHERE `u_email` = '".$Lemail."' AND `u_password` = '".$pass."'";
		$query = mysql_query($sql) or die(mysql_error());
		
		if(mysql_num_rows($query) > 0) {
			$user = mysql_fetch_assoc($query);

			$_SESSION['u_email'] = $Lemail;
			$_SESSION['u_id'] = $user['u_id'];
			header("Location: index.php");
		} else {
			echo errorMessage("Błędne dane logowania.");
		}
	} else {
		echo errorMessage("Błędne dane logowania.");
	}
}

if(isset($_POST['submitRegister'])) {
	$Remail = (isset($_POST['Remail']))? vtxt($_POST['Remail']) : "";
	$Rpassword = (isset($_POST['Rpassword']))? vtxt($_POST['Rpassword']) : "";
	$Rpasswordr = (isset($_POST['Rpasswordr']))? vtxt($_POST['Rpasswordr']) : "";
	$Rrules = (isset($_POST['Rrules']))? $_POST['Rrules'] : "";

	if(empty($Remail) || empty($Rpassword) || empty($Rpasswordr))
		$error[] = "Wypełnij wszystkie pola.";
	else {
		if(mysql_num_rows(mysql_query("SELECT `u_email` FROM `users` WHERE `u_email` = '".$Remail."'")) > 0)
			$error[] = "Adres e-mail jest już zajęty.";
		if(!filter_var($Remail, FILTER_VALIDATE_EMAIL))
			$error[] = "Adres e-mail jest nieprawidłowy.";
		if($Rpassword != $Rpasswordr)
			$error[] = "Podane hasła nie są identyczne.";
	}
	if($Rrules != 1)
		$error[] = "Musisz zaakceptować regulamin.";

	if(empty($error)) {
		$salt = substr(md5(time()), 0, 5);
		$insertPassword = md5(md5($salt).md5($Rpassword));
		$code = substr(md5(time()), 0, 30);

		mysql_query("INSERT INTO `users` (u_email, u_password, u_salt, u_code, u_register_date, u_ip) VALUES('".$Remail."', '".$insertPassword."', '".$salt."', '".$code."', '".time()."', '".$_SERVER['REMOTE_ADDR']."')") or die(mysql_error());

		echo successMessage("Konto zostało założone. Teraz możesz się zalogować.");
	} else {
		echo "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Wystąpiły następujące błedy:</h3></div><div class='panel-body'><ul>";
		foreach($error as $e)
			echo "<li>".$e."</li>";
		echo "</ul></div></div>";
	}
}

?>

<div class="row">
	<div class="col-md-6">
		<h4>Logowanie</h4>
		<form class="form-horizontal" method="post" action="">
			<div class="form-group">
				<label for="Lemail" class="col-md-3 control-label">E-mail</label>
				<div class="col-md-9">
					<input type="email" class="form-control" id="Lemail" name="Lemail" placeholder="E-mail">
				</div>
			</div>
			<div class="form-group">
				<label for="Lpassword" class="col-md-3 control-label">Hasło</label>
				<div class="col-md-9">
					<input type="password" class="form-control" id="Lpassword" name="Lpassword" placeholder="Hasło">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-3 col-md-4">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="Lremember"> Zapamiętaj mnie
						</label>
					</div>
				</div>
				<div class="col-md-5 text-right">
					<a href="">Przypomnij hasło</a>
				</div>
			</div>
			<div class="col-md-12 text-center">
				<input class="btn btn-primary" type="submit" name="submitLogin" value="Zaloguj">
			</div>
		</form>
	</div>

	<div class="col-md-6">
		<h4>Rejestracja</h4>
		<div class="col-md-12 text-right">
			Pola oznaczone gwiazdką (<span class="text-danger">*</span>) są wymagane.
		</div>
		<form class="form-horizontal" method="post" action="">
			<div class="form-group">
				<label for="Remail" class="col-md-3 control-label">E-mail <span class="text-danger">*</span></label>
				<div class="col-md-9">
					<input type="email" class="form-control" id="Remail" name="Remail" placeholder="E-mail">
				</div>
			</div>
			<div class="form-group">
				<label for="Rpassword" class="col-md-3 control-label">Hasło <span class="text-danger">*</span></label>
				<div class="col-md-9">
					<input type="password" class="form-control" id="Rpassword" name="Rpassword" placeholder="Hasło">
				</div>
			</div>
			<div class="form-group">
				<label for="Rpasswordr" class="col-md-3 control-label">Powtórz <span class="text-danger">*</span></label>
				<div class="col-md-9">
					<input type="password" class="form-control" id="Rpasswordr" name="Rpasswordr" placeholder="Powtórz hasło">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-3 col-md-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="Rrules" value="1"> Akceptuję <a href="">regulamin</a> <span class="text-danger">*</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-12 text-center">
				<input class="btn btn-primary" type="submit" name="submitRegister" value="Zarejestruj">
			</div>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>