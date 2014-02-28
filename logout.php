<?php
//code pulled from http://us3.php.net/session_destroy

// Initialize the session.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
?>
<html>
	<head>
		<title>
			Logged out
		</title>
	</head>
	<body>
		<h2>Logged out!</h2><br />
		<a href="login.php">Log back in?</a>
	</body>
</html>