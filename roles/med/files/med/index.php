<?php

require_once 'core.php';

$user = get_user_by_session();
if ( is_null( $user ) )
	redirect( '/login.php' );

function get_requests(): array {
	global $user;
	global $mysqli;
	$stmt = $mysqli->prepare( 'SELECT `last_name`, `first_name`, `birth_date`, `amka`, `content` FROM `request` WHERE `user_id` = ?' );
	$stmt->bind_param( 'i', $user['user_id'] );
	$stmt->execute();
	$rslt = $stmt->get_result();
	$item_list = [];
	while ( !is_null( $item = $rslt->fetch_assoc() ) )
		$item_list[] = $item;
	$rslt->free();
	$stmt->close();
	return $item_list;
}

$requests = get_requests();

echo_header();

?>
<div class="leaf root w3-border w3-theme">
	<p class="leaf">Welcome, <?= $user['user_mail'] ?>!</p>
</div>
<div class="leaf">
	<table class="w3-table-all">
		<thead>
			<tr class="w3-theme">
				<th>Last Name</th>
				<th>First Name</th>
				<th>Birth Date</th>
				<th>AMKA</th>
				<th>Content</th>
			</tr>
		</thead>
		<tbody>
<?php
foreach ( $requests as $request ) {
?>
			<tr>
				<td><?= $request['last_name'] ?></td>
				<td><?= $request['first_name'] ?></td>
				<td><?= $request['birth_date'] ?></td>
				<td><?= $request['amka'] ?></td>
				<td><?= $request['content'] ?></td>
			</tr>
<?php
}
?>
		</tbody>
	</table>
</div>
<a href="/request.php" class="w3-button leaf w3-theme w3-border">Request</a>
<a href="/logout.php" class="w3-button leaf w3-theme w3-border">Logout</a>
<?php

echo_footer();
