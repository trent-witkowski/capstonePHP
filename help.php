<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Resumate</title>
	<link rel="stylesheet" href="css/home.css">
	<link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
<div class="banner">
	<h1>Resumate</h1>
	<span>
    <?php if (isset($_SESSION['userID'])): ?>
        <a href="index.php?logout=true">Log Out</a>
    <?php else: ?>
        <a href="login.php?pageType=login">Login/Sign up</a>
    <?php endif; ?>
</span>
</div>
<div class="navDiv">
	<table class="navTable">
		<tbody>
		<tr>
			<td></td>
			<td class="navCell">
				<span><a href="index.php">Home</a></span>
			</td>
			<td class="navCell">
				<span><a href="help.php">Help</a></span>
			</td>
			<td class="navCell">
				<span><a href="resume.php?pageType=view">Resume</a></span>
			</td>
			<td colspan="2" class="navCell">
				<span><a href="userAccount.php?pageType=view">Account</a></span>
			</td>
		</tr>
		</tbody>
	</table>
</div>

<div class="main">
	<p>No help here. Figure it out yourself</p>
</div>
<div class="footerDiv">
    <p>Copyright 2025<span>&copy;</span>Resumate</p>
    <span><a href="mailto:totalyReal@resumate.com">totalyReal@resumate.com</a></span>
</div>
</body>
</html>