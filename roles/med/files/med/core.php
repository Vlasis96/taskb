<?php

require_once 'config.php';

require_once 'vendor/autoload.php';

$mysqli = new mysqli( MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_NAME );
$mysqli->init();
$mysqli->options( MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE );
$mysqli->set_charset( 'utf8mb4' );

function redirect( string $url = '/' ): void {
	header( 'Location: ' . $url );
	exit;
}

function get_user_by_mail( string $user_mail ) {
	global $mysqli;
	$stmt = $mysqli->prepare( 'SELECT `user_id`, `user_mail`, `user_pass` FROM `user` WHERE `user_mail` = ?' );
	$stmt->bind_param( 's', $user_mail );
	$stmt->execute();
	$rslt = $stmt->get_result();
	$item_list = [];
	while ( !is_null( $item = $rslt->fetch_assoc() ) )
		$item_list[] = $item;
	$rslt->free();
	$stmt->close();
	if ( empty( $item_list ) )
		return NULL;
	return $item_list[0];
}

function get_user_by_session() {
	$user = NULL;
	session_start();
	if ( !empty( $_SESSION ) ) {
		$user = $_SESSION['user'];
	}
	session_abort();
	return $user;
}

function echo_header(): void {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Medical Archive</title>
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-khaki.css">
		<style>
.flex-row {
	display: flex;
}
.flex-col {
	display: flex;
	flex-direction: column;
}
.root {
	padding: 8px;
}
.root .leaf {
	margin: 8px;
}
textarea {
	resize: vertical;
}
		</style>
	</head>
	<body class="flox-col root w3-theme-light">
		<h1 class="leaf">Medical Archive</h1>
<?php
}

function echo_footer(): void {
?>
	</body>
</html>
<?php
}
