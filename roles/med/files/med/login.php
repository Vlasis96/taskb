<?php

require_once 'core.php';

$message_list = [];

function try_login( string $user_mail, string $user_pass ) {
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
	if ( is_null( $user ) ) {
		$message_list[] = 'user_mail: not found';
		return NULL;
	}
	if ( $user['user_pass'] !== $user_pass ) {
		$message_list[] = 'user_pass: not correct';
		return NULL;
	}
	return $user;
}

if ( !is_null( get_user_by_session() ) )
	redirect();

$user_mail = NULL;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$user_mail = strval( $_POST['user_mail'] );
	$user_pass = strval( $_POST['user_pass'] );
	$user = try_login( $user_mail, $user_pass );
	if ( !is_null( $user ) ) {
		session_start();
		$_SESSION['user'] = $user;
		session_commit();
		redirect();
	}
}

echo_header();

?>
<h2 class="leaf">Login</h2>
<p class="leaf">or <a href="register.php">Register</a></p>
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
	<button type="submit" class="w3-button leaf w3-theme-action">Login</button>
</form>
<?php

echo_footer();
