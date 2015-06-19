<?php include("header.php");

$email = (isset($_POST['email']))? vtxt($_POST['email']) : "";
$title = (isset($_POST['title']))? vtxt($_POST['title']) : "";
$message = (isset($_POST['message']))? vtxt($_POST['message']) : "";

if(isset($_POST['send'])) {
	$error = array();

	if(empty($email) || empty($title) || empty($message))
		$error[] = "Wypełnij wszystkie pola.";
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		$error[] = "Adres e-mail jest nieprawidłowy.";

	if(empty($error)) {
		$message2 = "Wiadomość od: ".$email." --- ".$message;
		mail($set['contact'], $title, $message2) or die("błąd");
		echo successMessage("Wiadomość została wysłana.");
	} else {
		echo "<div class='panel panel-danger'><div class='panel-heading'><h3 class='panel-title'>Wystąpiły następujące błedy:</h3></div><div class='panel-body'><ul>";
		foreach($error as $e)
			echo "<li>".$e."</li>";
		echo "</ul></div></div>";
	}
}

?>

<h4>Kontakt</h4>

<hr>

<div class="col-md-offset-2 col-md-8">
	<form class="form-horizontal" method="post" action="">
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">E-mail</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" placeholder="Twój e-mail">
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-2 control-label">Tytuł</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="title" name="title" value="<?php echo $title ?>" placeholder="Tytuł wiadomości">
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-2 control-label">Wiadomość</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="8" name="message" style="resize: vertical;"><?php echo $message ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12 text-center">
				<button type="submit" class="btn btn-primary" name="send">Wyślij</button>
			</div>
		</div>
	</form>
</div>

<?php include("footer.php"); ?>