<?php

require_once 'core.php';

$message_list = [];

function register_user( string $user_mail, string $user_pass ): void {
	global $mysqli;
	$stmt = $mysqli->prepare( 'INSERT INTO `user` (`user_mail`, `user_pass`) VALUES (?, ?)' );
	$stmt->bind_param( 'ss', $user_mail, $user_pass );
	$stmt->execute();
	$stmt->close();
}

function try_register( string $user_mail, string $user_pass ) {
	global $message_list;
	$user_mail = filter_var( $user_mail, FILTER_VALIDATE_EMAIL );
	if ( $user_mail === FALSE ) {
		$message_list[] = 'user_mail: not valid';
		return NULL;
	}
	if ( $user_pass === '' ) {
		$message_list[] = 'user_pass: not valid';
		return NULL;
	}
	$user = get_user_by_mail( $user_mail );
	if ( !is_null( $user ) ) {
		$message_list[] = 'user_mail: exists';
		return NULL;
	}
	register_user( $user_mail, $user_pass );
	$user = get_user_by_mail( $user_mail );
	return $user;
}

if ( !is_null( get_user_by_session() ) )
	redirect();

$user_mail = NULL;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$user_mail = strval( $_POST['user_mail'] );
	$user_pass = strval( $_POST['user_pass'] );
	$user = try_register( $user_mail, $user_pass );
	if ( !is_null( $user ) ) {
		session_start();
		$_SESSION['user'] = $user;
		session_commit();
		redirect();
	}
}

echo_header();

?>
<h2 class="leaf">Register</h2>
<p class="leaf">or <a href="login.php">Login</a></p>
<form class="leaf flex-col root w3-border" method="post" autocomplete="off">
<?php
foreach ( $message_list as $message ) {
?>
<div class="leaf root w3-border w3-red">
	<p class="leaf"><?= $message ?></p>
</div>
<?php
}
?>
	<label class="leaf flex-col">
		<span>Email:</span>
		<input type="email" name="user_mail" class="w3-input" required="required" value="<?= $user_mail ?>">
	</label>
	<label class="leaf flex-col">
		<span>Password:</span>
		<input type="password" name="user_pass" class="w3-input" required="required">
	</label>
	<button type="submit" class="w3-button leaf w3-theme-action">Register</button>
</form>
<?php

echo_footer();
