<?php include("header.php");
$reflink = "http://".$set['domain']."/auth.php?ref=".$user['u_id'];

$invited_list = array();
$query = mysql_query("SELECT * FROM `invited` WHERE `inv_inviting` = '".$user['u_id']."' ORDER BY `inv_date` DESC");

if(mysql_num_rows($query) > 0) {
	while($row = mysql_fetch_array($query)) {
		$invited_list[] = $row;
	}
}

$query = mysql_query("SELECT * FROM `invited` WHERE `inv_invited` = '".$user['u_id']."' LIMIT 1");
if(mysql_num_rows($query) > 0) {
	while($row = mysql_fetch_array($query)) {
		$invitingid = $row['inv_inviting'];
		$inviting = getUser($invitingid);
		$invitingEmail = $inviting['u_email'];
	}
}
?>

<h4>Profil <small>- <?php echo $user['u_email']; ?></small></h4>

<hr>

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<table class="table text-center">
			<tr><td>E-mail:</td> <td><?php echo $user['u_email']; ?></td></tr>
			<tr><td>Hasło:</td> <td><a href="" class="btn btn-xs btn-primary">Zmień hasło</a></td></tr>
			<tr><td>Data rejestracji:</td> <td><?php echo showDate($user['u_register_date']); ?></td></tr>
			<tr><td>Zaproszenie od:</td> <td><?php echo (isset($inviting))? $invitingEmail : "Brak"; ?></td></tr>
			<tr><td>wPLN:</td> <td><?php echo $user['u_money']; ?> <a href="wallet.php" data-toggle="tooltip" data-placement="right" title="Doładuj wPLN"><span class="text-success"><i class="glyphicon glyphicon-plus"></i></span></a></td></tr>
		</table>
	</div>
</div>

<hr>

<h4>Zaproszeni <small><a href="faq.php">(?)</a></small></h4>

Link zapraszający: <input disabled value="<?php echo $reflink; ?>" id="disabled" type="text" class="validate" style="width: 350px; text-align: center;">
<br><br>

<?php if(!empty($invited_list)) { ?>

<table class="table table-hover">
	<thead>
		<tr>
			<th>E-mail</th>
			<th>Data rejestracji</th>
			<th>Status konta</th>
			<th>Profit wPLN</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($invited_list as $i) {
			$invited = getUser($i['inv_invited']);
			$invitedEmail = $invited['u_email'];
			$invitedRank = $invited['u_rank'];
			$date = $i['inv_date'];

			if($invitedRank == 0)
				$i['rank_text'] = "<span class='text-danger'>Nieaktywne</span>";
			else
				$i['rank_text'] = "<span class='text-success'>Aktywne</span>";

			echo "<tr>";
				echo "<td>".$invitedEmail."</td>";
				echo "<td>".showDate($date)."</td>";
				echo "<td>".$i['rank_text']."</td>";
				echo "<td>...</td>";
			echo "<tr>";
		}
		?>
	</tbody>
</table>

<?php
} else
	echo infoMessage("Brak zaproszonych użytkowników.");

include("footer.php"); ?>