<?php
// Require index for database use
require('config.php');

// Set up variables
$loginError = "";

/* Connect to database to display highscore table and other database stuff */
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die ('Your DB connection has failed or is misconfigured, please enter correct values in the config file and try again');

// If a post request is submitted to login
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Get input read in (this does not sanatize anything)
	$username = htmlspecialchars(filter_input(INPUT_POST, 'username'));
	$password = htmlspecialchars(filter_input(INPUT_POST, 'password'));

	// Set user to be authenticated by default, checks will set to not auth
	$authenticated = 1;

	// Get the hashed password to check against database
	$passwordHash = md5($password);

	/* ---------------- */
	/* START VALIDATION */
	/* ---------------- */

	// Validate username field
	if (empty($username)) {
		$loginError = "\nNo username entered, please enter a valid username or signup\n";
		$authenticated = 0;
	} else {
		// Check if username exists in database
		$query = "SELECT * FROM users WHERE username='" . mysqli_real_escape_string($link, $username) . "'";
		$result = mysqli_query($link, $query);
		$numRows = mysqli_num_rows($result);

		// Check if any rows returned
		if($numRows == 0) {
			$loginError = "\nThe username entered does not match our records, please enter a valid username or signup\n";
			$authenticated = 0;
		}
	}

	// Validate password field if no previous errors 
	if ($loginError == "") {
		if (empty($password)) {
			$loginError = "\nNo password entered, please enter the correct password\n";
			$authenticated = 0;
		} else {
			// Check if password matches the username in database
			$query = "SELECT password FROM users WHERE username='" . mysqli_real_escape_string($link, $username) . "'";
			$result = mysqli_query($link, $query);
			$row = mysqli_fetch_assoc($result);

			// Check if hash of password matches for user
			if($row['password'] != $passwordHash) {
				$loginError = "\nInvalid password entered, please enter the correct password\n";
				$authenticated = 0;
			}
		}
	}

	// If authenticated, move back to main page and set session variables
	if ($authenticated == 1) {
		// Set up session data

		// Start user session
		session_start();
		$_SESSION['username'] = $username;

		// Alert user they have been logged in
		$alert = "You have been logged in " . $username . "!";
		echo "<script type='text/javascript'>alert('$alert');</script>";

		// Redirect to main page
		$url = "index.php";
		echo "<script type='text/javascript'>window.location.replace('$url');</script>";
	}
}
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Hangman - Login</title>
	<link rel="stylesheet" href="main.css" type="text/css">
</head>
<body>
<div id="content">
	<div id="login">
		<form method="post">
			<h1>Login</h1>

			<div id="username">
				<p><label for="username">Username</label></p>
				<input type="text" id="username" name="username"><br>
			</div>

			<div id="password">
				<p><label for="password">Password</label></p>
				<input type="password" id="password" name="password"><br>
			</div>
			<input type="submit" value="Submit" id="btn">
			<br><span class="error"><?php echo $loginError ?></span><br>
		</form>
	</div>
</div>

</body>
</html>