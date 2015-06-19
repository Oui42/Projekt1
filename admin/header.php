<?php require ('../config.php');
if(!isset($user) || $user['u_rank'] < $__RANK['admin'])
	header("Location: ../index.php");
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $set['webName']; ?></title>
		<link href="../css/bootstrap.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<!-- Wersja mobilna: -->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php"><i class="glyphicon glyphicon-globe"></i> <?php echo $set['webName']; ?> - Panel administratora</a>
				</div>
				<div class="collapse navbar-collapse" id="navbar-collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="../index.php">Home</a></li>
						<li><a href="index.php">Ustawienia</a></li>
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Użytkownicy <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="users_list.php">Lista</a></li>
								<li><a href="users_invited.php">Zaproszeni</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Oferta <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="offer_categories.php">Kategorie</a></li>
								<li><a href="offer_products.php">Produkty</a></li>
								<li><a href="offer_transactions.php">Transakcje</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Płatności <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="payments_methods.php">Metody</a></li>
								<li><a href="payments_transactions.php">Transakcje</a></li>
							</ul>
						</li>
						<li><a href="stats.php">Statystyki</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<main>
			<div class="container">