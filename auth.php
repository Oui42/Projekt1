<?php include("header.php"); 

if(isset($user)) header("Location: index.php");

if(isset($_GET['remindsuccess']))
	echo successMessage("Hasło zostało zmienione. Zaloguj się używając nowego hasła.");

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

			if($user['u_rank'] > $__RANK['unactive']) {
				$_SESSION['u_email'] = $email;
				$_SESSION['u_id'] = $user['u_id'];
				header("Location: index.php");
			} else {
				echo errorMessage('Konto jest nieaktywne. <a href="index.php?resend&id='.$row['id'].'">Wyślij ponownie klucz aktywacyjny</a>.');
			}
		} else {
			echo errorMessage("Błędne dane logowania.");
		}
	} else {
		echo errorMessage("Błędne dane logowania.");
	}
}

if(isset($_GET['resend'])) {
	if(empty($_GET['id']))
		echo errorMessage("Nieprawidłowe ID użytkownika.");
	else {
		$id = mysql_real_escape_string($_GET['id']);
		$sql = "SELECT * FROM `users` WHERE `u_id` = '".$id."'";
		$query = mysql_query($sql);

		$row = mysql_fetch_array($query);

		if(mysql_num_rows($query) > 0) {
			if($row['u_rank'] == 0) {
				sendActivationLink($id);
			} else {
				echo infoMessage("To konto jest już aktywne.");
			}
		} else {
			echo errorMessage("Użytkownik o podanym ID nie istnieje.");
		}
	}
}

$refemail = "";
if(isset($_GET['ref'])) {
	$refid = $_GET['ref'];
	$refuser = getUser($refid);
	if($refuser > 0)
		$refemail = $refuser['u_email'];
	else
		$refemail = "";
}

if(isset($_POST['submitRegister'])) {
	$Remail = (isset($_POST['Remail']))? vtxt($_POST['Remail']) : "";
	$Rpassword = (isset($_POST['Rpassword']))? vtxt($_POST['Rpassword']) : "";
	$Rpasswordr = (isset($_POST['Rpasswordr']))? vtxt($_POST['Rpasswordr']) : "";
	$Rinviting = (isset($_POST['Rinviting']))? vtxt($_POST['Rinviting']) : "";
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
		if(!filter_var($inviting, FILTER_VALIDATE_EMAIL))
			$error[] = "Adres e-mail osoby zapraszającej jest nieprawidłowy.";
		if(mysql_num_rows(mysql_query("SELECT `u_email` FROM `users` WHERE `u_email` = '".$inviting."'")) == 0)
			$error[] = "Adres e-mail osoby zapraszającej nie istnieje.";
		if(strlen($Rpassword) < 5)
			$error[] = "Hasło musi mieć minimum 5 znaków.";
	}
	if($Rrules != 1)
		$error[] = "Musisz zaakceptować regulamin.";

	if(empty($error)) {
		$salt = substr(md5(time()), 0, 5);
		$insertPassword = md5(md5($salt).md5($Rpassword));
		$code = substr(md5(time()), 0, 30);

		mysql_query("INSERT INTO `users` (u_email, u_password, u_salt, u_code, u_register_date, u_ip) VALUES('".$Remail."', '".$insertPassword."', '".$salt."', '".$code."', '".time()."', '".$_SERVER['REMOTE_ADDR']."')") or die(mysql_error());

		$id = mysql_insert_id();
		if(!empty($inviting)) {
			$invitingUser = getUserByEmail($inviting);
			if($invitingUser > 0) {
				$invitingid = $invitingUser['u_id'];
				mysql_query("INSERT INTO `invited` (inv_id, inv_inviting, inv_invited, inv_date) VALUES('NULL', '".$invitingid."', '".$id."', '".time()."')");
			}
		}

		sendActivationLink($id);

		echo successMessage("Konto zostało założone. Link aktywacyjny został wysłany na podany adres e-mail.");
	} else {
		echo "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Wystąpiły następujące błedy:</h3></div><div class='panel-body'><ul>";
		foreach($error as $e)
			echo "<li>".$e."</li>";
		echo "</ul></div></div>";
	}
}

if(isset($_GET['activate'])) {
	if(empty($_GET['id']) || empty($_GET['code']))
		echo errorMessage("Nieprawidłowe ID użytkownika lub kod aktywacyjny.");
	else {
		$id = mysql_real_escape_string($_GET['id']);
		$sql = "SELECT * FROM `users` WHERE `u_id` = '".$id."'";
		$query = mysql_query($sql);

		$row = mysql_fetch_array($query);

		if(mysql_num_rows($query) > 0) {
			if($row['u_rank'] == 0) {
				if(($_GET['code'] == $row['u_code']) && ($id == $row['u_id'])) {
					mysql_query("UPDATE `users` SET `u_rank` = '1' WHERE `u_id` = '".$id."'");
					echo successMessage("Konto zostało aktywowane. Teraz możesz się zalogować.");
				} else {
					echo errorMessage("Nieprawidłowe ID użytkownika lub kod aktywacyjny.");
				}
			} else {
				echo infoMessage("To konto jest już aktywne.");
			}
		} else {
			echo errorMessage("Użytkownik o podanym ID nie istnieje.");
		}
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
					<a href="remind.php">Przypomnij hasło</a>
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
				<label for="Rinviting" class="col-md-3 control-label">Zapraszający</label>
				<div class="col-md-9">
					<input type="email" class="form-control" id="Rinviting" name="Rinviting" value="<?php echo $refemail; ?>" placeholder="E-mail zapraszającego">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-3 col-md-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="Rrules" value="1"> Akceptuję <a href="rules.php">regulamin</a> <span class="text-danger">*</span>
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