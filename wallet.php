<?php include("header.php");

$sms_config_homepay=array();
$sms_payment_list = array();
$sms_query = mysql_query("SELECT * FROM `payments_methods` WHERE `pm_method` = ".$__PMETHOD['sms']." ORDER BY `pm_money` ASC");
if(mysql_num_rows($sms_query) > 0) {
	while($row = mysql_fetch_array($sms_query)) {
		$sms_payment_list[] = $row;
	}
}

$transfer_config_homepay=array();
$transfer_payment_list = array();
$transfer_query = mysql_query("SELECT * FROM `payments_methods` WHERE `pm_method` = ".$__PMETHOD['transfer']." ORDER BY `pm_money` ASC");
if(mysql_num_rows($transfer_query) > 0) {
	while($row = mysql_fetch_array($transfer_query)) {
		$transfer_payment_list[] = $row;
	}
}

if(!empty($sms_payment_list)) {
	foreach($sms_payment_list as $p) {
		$brutto = ($p['pm_cost'] * $set['tax']) + $p['pm_cost'];
		$sms_config_homepay[$p['pm_id']] = array(
			"sms_acc_id" => $p['pm_accid'],
			"sms_name" => $p['pm_name'],
			"sms_netto" => $p['pm_cost'],
			"sms_brutto" => $brutto,
			"sms_number" => $p['pm_sms_number'],
			"sms_content" => $p['pm_sms_content'],
			"sms_money" => $p['pm_money']
		);
	}
}

if(!empty($transfer_payment_list)) {
	foreach($transfer_payment_list as $p) {
		$transfer_config_homepay[$p['pm_id']] = array(
			"transfer_acc_id" => $p['pm_accid'],
			"transfer_name" => $p['pm_name'],
			"transfer_cost" => $p['pm_cost'],
			"transfer_money" => $p['pm_money']
		);
	}
}

if(isset($_POST['check_code']) && $_POST['check_code']) {
	$code = vtxt($_POST['sms_code']);
 	if(!preg_match("/^[A-Za-z0-9]{8}$/", $code))
 		echo errorMessage("Błędny format kodu - 8 znaków.");
	else if(empty($sms_config_homepay[$_POST['sms_service']]))
		echo errorMessage("Brak takiej usługi.");
	else {
		$handle = fopen("http://homepay.pl/API/check_code.php?usr_id=".$config_homepay_usr_id."&acc_id=".$sms_config_homepay[$_POST['sms_service']]['sms_acc_id']."&code=".$code,'r');
		$check = fgets($handle, 8);
		fclose($handle);
		if($check == "1") {
			header("Location: wallet.php?success=".$sms_config_homepay[$_POST['sms_service']]['sms_money']);
			addMoney($user['u_id'], $sms_config_homepay[$_POST['sms_service']]['sms_money']);
			mysql_query("INSERT INTO `payments` (p_user, p_method, p_acc_id, p_money, p_cost, p_name, p_date) VALUES('".$user['u_id']."', '".$__PMETHOD['sms']."', '".$sms_config_homepay[$_POST['sms_service']]['sms_acc_id']."', '".$sms_config_homepay[$_POST['sms_service']]['sms_money']."', '".$sms_config_homepay[$_POST['sms_service']]['sms_netto']."', '".$sms_config_homepay[$_POST['sms_service']]['sms_name']."', '".time()."')");
		} else if($check == "0")
			echo errorMessage("Nieprawidłowy kod.");
		else
			echo errorMessage("Błąd w połączeniu z operatorem.");
	}
}

if(isset($_POST['check_tcode']) && $_POST['check_tcode']) {
	$code = vtxt($_POST['transfer_code']);
 	if(!preg_match("/^[A-Za-z0-9]{8}$/",$code))
 		echo errorMessage("Błędny format kodu - 8 znaków.");
	else if(empty($transfer_config_homepay[$_POST['transfer_service']]))
		echo errorMessage("Brak takiej usługi.");
	else {
		$handle = fopen("http://homepay.pl/API/check_tcode.php?usr_id=".$config_homepay_usr_id."&acc_id=".$transfer_config_homepay[$_POST['transfer_service']]['transfer_acc_id']."&code=".$code,'r');
		$check = fgets($handle, 8);
		fclose($handle);
		if($check == "1") {
			header("Location: wallet.php?success=".$transfer_config_homepay[$_POST['transfer_service']]['transfer_money']);
			addMoney($user['u_id'], $transfer_config_homepay[$_POST['transfer_service']]['transfer_money']);
			mysql_query("INSERT INTO `payments` (p_user, p_method, p_acc_id, p_money, p_cost, p_name, p_date) VALUES('".$user['u_id']."', '".$__PMETHOD['transfer']."', '".$transfer_config_homepay[$_POST['transfer_service']]['transfer_acc_id']."', '".$transfer_config_homepay[$_POST['transfer_service']]['transfer_money']."', '".$transfer_config_homepay[$_POST['transfer_service']]['transfer_cost']."', '".$transfer_config_homepay[$_POST['transfer_service']]['transfer_name']."', '".time()."')");
		} else if($check == "0")
			echo errorMessage("Nieprawidłowy kod.");
		else
			echo errorMessage("Błąd w połączeniu z operatorem.");
	}
}

if(isset($_GET['success']) && !empty($_GET['success']))
	echo successMessage("Konto zostało doładowane kwotą ".$_GET['success']." wPLN.");

?>

<h4>Portfel <small>- Stan konta: <?php echo $user['u_money']; ?> wPLN</small></h4>

<hr>

<div class="row">
	<div class="col-md-6">
		<h5 class="text-center">SMS</h5>
		<hr>
		<?php
		if(!empty($sms_payment_list)) {
			echo "<div class='table-responsive'>";
				echo "<table class='table table-condensed table-hover'>";
					echo "<thead>";
						echo "<tr>";
							echo "<th class='text-center'>Numer</th>";
							echo "<th class='text-center'>Treść</th>";
							echo "<th class='text-center'>Koszt</th>";
							echo "<th class='text-center'>wPLN</th>";
						echo "</tr>";
					echo "</thead>";
					echo "<tbody>";
						foreach($sms_config_homepay as $v) {
							echo "<tr>";
								echo "<td class='text-center'>".$v['sms_number']."</td>";	
								echo "<td class='text-center'>".$v['sms_content']."</td>";	
								echo "<td class='text-center'>".$v['sms_brutto']."zł z VAT</td>";
								echo "<td class='text-center'>".$v['sms_money']." wPLN</td>";
							echo "</tr>";
						}
					echo "</tbody>";
				echo "</table>";
			echo "</div>";

			echo "<form class='form-horizontal' action='' method='post'>";
				echo "<input type='hidden' name='check_code' value='1'>";
				echo "<div class='form-group'>";
					echo "<label for='sms_select' class='col-md-2 control-label'>Usługa:</label>";
					echo "<div class='col-md-10'>";
						echo "<select class='form-control' id='sms_select' name='sms_service'>";
							echo "<option value='' disabled selected>Wybierz usługę</option>";
							foreach($sms_config_homepay as $k=>$v) 
							echo "<option name='sms_service' value='".$k."'>".$v['sms_brutto']."zł z VAT - ".$v['sms_money']." wPLN</option>\n";
						echo "</select>";
					echo "</div>";
				echo "</div>";
				echo "<div class='form-group'>";
					echo "<label for='sms_code' class='col-md-2 control-label'>Kod:</label>";
					echo "<div class='col-md-10'>";
						echo "<input type='text' class='form-control' id='sms_code' name='sms_code' maxlength='8' placeholder='Kod z SMS'>";
					echo "</div>";
				echo "</div>";
				echo "<div class='form-group'>";
					echo "<div class='col-md-12 text-center'>";
						echo "<button type='submit' class='btn btn-primary'>Potwierdź</button>";
					echo "</div>";
				echo "</div>";
			echo "</form>";
		} else {
			echo warningMessage("Brak możliwych metod płatności.");
		}
		?>
	</div>

	<div class="col-md-6">
		<h5 class="text-center">Przelew</h5>
		<hr>
		<?php
		if(!empty($transfer_payment_list)) {
			echo "<div class='table-responsive'>";
				echo "<table class='table table-condensed table-hover'>";
					echo "<thead>";
						echo "<tr>";
							echo "<th class='text-center'>Koszt</th>";
							echo "<th class='text-center'>wPLN</th>";
							echo "<th class='text-center'>Wykonaj przelew</th>";
						echo "</tr>";
					echo "</thead>";
					echo "<tbody>";
						foreach($transfer_config_homepay as $v) {
							echo "<tr>";
								echo "<td class='text-center'>".$v['transfer_cost']."zł</td>";
								echo "<td class='text-center'>".$v['transfer_money']." wPLN</td>";
								echo "<td class='text-center'>";
									foreach($transfer_config_homepay as $v)
										echo "<a href='https://ssl.homepay.pl/wplata/".$v['transfer_acc_id']."-".$v['transfer_name']."' target='_blank' class='btn btn-primary btn-xs'>Wykonaj przelew <i class='glyphicon glyphicon-arrow-right'></i></a>";
								echo "</td>";	
							echo "</tr>";
						}
					echo "</tbody>";
				echo "</table>";
			echo "</div>";

			echo "<form class='form-horizontal' action='' method='post'>";
				echo "<input type='hidden' name='check_tcode' value='1'>";
				echo "<div class='form-group'>";
					echo "<label for='transfer_select' class='col-md-2 control-label'>Usługa:</label>";
					echo "<div class='col-md-10'>";
						echo "<select class='form-control' id='transfer_select' name='transfer_service'>";
							echo "<option value='' disabled selected>Wybierz usługę</option>";
							foreach($transfer_config_homepay as $k=>$v) 
							echo "<option name='transfer_service' value='".$k."'>".$v['transfer_cost']."zł - ".$v['transfer_money']." wPLN</option>\n";
						echo "</select>";
					echo "</div>";
				echo "</div>";
				echo "<div class='form-group'>";
					echo "<label for='transfer_code' class='col-md-2 control-label'>Kod:</label>";
					echo "<div class='col-md-10'>";
						echo "<input type='text' class='form-control' id='transfer_code' name='transfer_code' maxlength='8' placeholder='Kod z e-mail'>";
					echo "</div>";
				echo "</div>";
				echo "<div class='form-group'>";
					echo "<div class='col-md-12 text-center'>";
						echo "<button type='submit' class='btn btn-primary'>Potwierdź</button>";
					echo "</div>";
				echo "</div>";
			echo "</form>";
		} else {
			echo warningMessage("Brak możliwych metod płatności.");
		}
		?>

		<hr><p class="text-center"><small>Po dokonaniu i zaksięgowaniu wpłaty dostaniesz wiadomość e-mail z kodem aktywacyjnym, który należy wpisać w polu aktywacyjnym.</small></p>
	</div>
</div>

<hr>

<p class="text-center">
	<small>Płatności obsługuje firma <a href="http://homepay.pl/">Homepay.pl</a>.</small><br>
	<small><a href="http://ssl.homepay.pl/regulamin">Regulamin</a> | <a href="http://ssl.homepay.pl/reklamacje/">Reklamacje</a></small>
</p>

<?php include("footer.php"); ?>