<?php
/* Nie zmieniać: */
ob_start();
session_start();

@mysql_connect("localhost", "root", "") or die(mysql_error()."Błąd połączenia z bazą danych.");
mysql_select_db("test") or die(mysql_error()."Nieprawidłowa nazwa bazy danych.");

mysql_set_charset("utf-8");

$set = array();

require_once('functions.php');

$__RANK = array(
	'unactive' => 0,
	'user' => 1,
	'moderator' => 2,
	'admin' => 3
);

$__PMETHOD = array(
	'sms' => 0,
	'transfer' => 1
);



/* Ustawienia: */
$set['webName'] = "Test";				// Nazwa strony
$set['domain'] = "ouipage.pl/demo2";	// Adres strony
$set['contact'] = "ouix42@gmail.com";	// Kontaktowy adres e-mail
$set['tax'] = 0.23;						// Aktualna stawka podatku VAT

$config_homepay_usr_id = 8052;			// ID użytkownika HomePay