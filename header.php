<?php require ('config.php'); ?>

<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $set['webName']; ?></title>
		<link href="css/bootstrap.css" rel="stylesheet">
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
					<a class="navbar-brand" href="index.php"><i class="glyphicon glyphicon-globe"></i> <?php echo $set['webName']; ?></a>
				</div>
				<div class="collapse navbar-collapse" id="navbar-collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="index.php">Home</a></li>
						<li><a href="rules.php">Regulamin</a></li>
						<li><a href="faq.php">FAQ</a></li>
						<li><a href="contact.php">Kontakt</a></li>
						<li class="dropdown">
							<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Konto <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?php if(!isset($user)) echo '<li><a href="auth.php">Logowanie / Rejestracja</a></li>'; ?>
								<?php if(isset($user)) {
									if($user['u_rank'] >= $__RANK['admin']) echo '<li><a href="admin/">Panel Administratora</a></li>';
									echo '<li><a href="profile.php">'.$user['u_email'].'</a></li>';
									echo '<li role="separator" class="divider"></li>';
									echo '<li><a href="wallet.php">Portfel ('.$user['u_money'].' wPLN)</a></li>';
									echo '<li><a href="history.php">Historia zakupów</a></li>';
									echo '<li role="separator" class="divider"></li>';
									echo '<li><a href="logout.php">Wyloguj</a></li>';
								} ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<main>
			<div class="container">