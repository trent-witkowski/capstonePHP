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
                <!--            NAV           -->
                <tr>
                    <td></td>
                    <td class="navCell">
                        <span><a href="index.php">Home</a></span>
                    </td>
                    <td class="navCell">
                        <span><a href="help.php">Help</a></span>
                    </td>
                    <td class="navCell">
                        <span><a href="resume.php">Resume</a></span>
                    </td>
                    <td colspan="2" class="navCell">
                        <span><a href="userAccount.php">Account</a></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="main">
        <!--            HTML START-->
        <div class="missionDiv">
            <p id="missionStatement">At Resumate, our mission is to empower individuals to take control of their professional journey by
                providing a dynamic platform that connects talent with opportunity. We strive to simplify career growth
                through intelligent networking, streamlined resume building, and meaningful connections that drive
                success in the modern workforce.
            </p>
            <span>- Previously Employed Resumate HR Coordinator 2025</span>
        </div>
        <div class="btnDiv">
            <form action="resume.php" method="post">
                <button class="button-19" type="submit">Begin Viewing Candidates</button>
            </form>
            <form action="login.php?pageType=signUp" method="post">
                <button class="button-19" type="submit">Begin Creating Resume</button>
            </form>
        </div>
        <!--            HTML END  -->
    </div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
<script>

    let navLinks = document.querySelectorAll(".navTable a");

    navLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            <?php
            $_SESSION['prevPage'] = 'home';
            ?>
            window.location.href = link.href;
        });
    });
</script>
</body>
</html>
