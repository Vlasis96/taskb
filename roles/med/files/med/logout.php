<?php

require_once 'core.php';
 
if ( !is_null( get_user_by_session() ) ) {
	session_start();
	unset( $_SESSION['user'] );
	session_commit();
}

redirect( '/login.php' );
