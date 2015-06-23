<?php include("header.php"); ?>

<h4>FAQ</h4>

<hr>

<div class="panel panel-default">
	<div class="panel-heading"><i class="glyphicon glyphicon-share-alt"></i> Czym jest wPLN?</div>
	<div class="panel-body">
		wPLN to wirtualna waluta, którą możesz wykorzystać do kupowania przedmiotów. <?php if(isset($user)) echo "Możesz doładować swoje konto korzystając z <a href='wallet.php'>tego odnośnika</a>."; ?>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><i class="glyphicon glyphicon-share-alt"></i> Kim są zaproszeni użytkownicy?</div>
	<div class="panel-body">
		Zaproszeni użytkownicy, to osoby które zarejestrowały się korzystając z Twojego <?php if(isset($user)) echo "<a href='profile.php'>"; ?>linku zapraszającego <?php if(isset($user)) echo "</a>"; ?> lub podały podczas rejestracji Twój adres e-mail.<br>
		Gdy zaproszona przez Ciebie osoba zakupi wPLN, Ty dostaniesz profit zależny od ilości zakupionych wPLN.
	</div>
</div>

<?php include("footer.php"); ?>