<?php

require_once 'core.php';

$user = get_user_by_session();
if ( is_null( $user ) )
	redirect( '/login.php' );

$first_name = NULL;
$last_name = NULL;
$birth_date = NULL;
$amka = NULL;
$content = NULL;

function send_mail( string $to, string $subject, string $body ): bool {
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
	$mail->SMTPAuth = TRUE;
	$mail->Username = MAIL_ADDR;
	$mail->Password = MAIL_PASS;
	$mail->setFrom( MAIL_ADDR, MAIL_NAME );
	$mail->addAddress( $to );
	$mail->IsHTML( FALSE );
	$mail->CharSet = 'UTF-8';
	$mail->Subject = $subject;
	$mail->Body = $body;
	return $mail->send();
}

function save_request( int $user_id, string $last_name, string $first_name , string $birth_date, string $amka, string $content ): void {
	global $mysqli;
	$stmt = $mysqli->prepare( 'INSERT INTO `request` (`user_id`, `last_name`, `first_name`, `birth_date`, `amka`, `content`) VALUES (?, ?, ?, ?, ?, ?)' );
	$stmt->bind_param( 'isssss', $user_id, $last_name, $first_name, $birth_date, $amka, $content );
	$stmt->execute();
	$stmt->close();
}

function try_request( string $last_name, string $first_name , string $birth_date, string $amka, string $content ): bool {
	global $user;
	global $message_list;
	$last_name = trim( $last_name );
	if ( $last_name === '' ) {
		$message_list[] = 'last_name: empty';
		return FALSE;
	}
	$first_name = trim( $first_name );
	if ( $first_name === '' ) {
		$message_list[] = 'first_name: empty';
		return FALSE;
	}
	$birth_date = DateTime::createFromFormat( 'Y-m-d', $birth_date );
	if ( $birth_date === FALSE ) {
		$message_list[] = 'birth_date: not valid';
		return FALSE;
	}
	$birth_date = $birth_date->format( 'Y-m-d');
	$amka = trim( $amka );
	if ( $amka === '' ) {
		$message_list[] = 'amka: empty';
		return FALSE;
	}
	$amka = filter_var( $amka, FILTER_VALIDATE_REGEXP, [ 'options' => [ 'regexp' => '/^\\d{11}$/' ], ] );
	if ( $amka === FALSE ) {
		$message_list[] = 'amka: not valid';
		return FALSE;
	}
	$content = trim( $content );
	if ( $content === '' ) {
		$message_list[] = 'content: empty';
		return FALSE;
	}
	save_request( $user['user_id'], $last_name, $first_name, $birth_date, $amka, $content );
	$subject = 'Request Submission';
	$body = implode( "\r\n", [
		'Last Name:',
		$last_name,
		'',
		'First Name:',
		$first_name,
		'',
		'Birth Date',
		$birth_date,
		'',
		'AMKA',
		$amka,
		'',
		'Content',
		$content,
		'',
		'Submission Successful',
	] );
	if ( send_mail( $user['user_mail'], $subject, $body ) )
		return TRUE;
	$message_list[] = 'mail: not sent';
	return FALSE;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$last_name = strval( $_POST['last_name'] );
	$first_name = strval( $_POST['first_name'] );
	$birth_date = strval( $_POST['birth_date'] );
	$amka = strval( $_POST['amka'] );
	$content = strval( $_POST['content'] );
	if ( try_request( $last_name, $first_name, $birth_date, $amka, $content ) )
		redirect();
}

echo_header();

?>
<h2 class="leaf">Request</h2>
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
		<span>Last Name:</span>
		<input type="text" name="last_name" class="w3-input" required="required" value="<?= $last_name ?>">
	</label>
	<label class="leaf flex-col">
		<span>First Name:</span>
		<input type="text" name="first_name" class="w3-input" required="required" value="<?= $first_name ?>">
	</label>
	<label class="leaf flex-col">
		<span>Birth Date:</span>
		<input type="date" name="birth_date" class="w3-input" required="required" value="<?= $birth_date ?>">
	</label>
	<label class="leaf flex-col">
		<span>AMKA:</span>
		<input type="text" name="amka" class="w3-input" required="required" value="<?= $amka ?>" pattern="\d{11}">
	</label>
	<label class="leaf flex-col">
		<span>Content:</span>
		<textarea name="content" class="w3-input" required="required"><?= $content ?></textarea>
	</label>
	<button type="submit" class="w3-button leaf w3-theme-action">Send</button>
</form>
<a href="/" class="w3-button leaf w3-theme w3-border">Home</a>
<?php

echo_footer();
