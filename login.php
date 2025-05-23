<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
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

if (isset($_SESSION["userID"])) {
	header("Location: userAccount.php");
	exit();
}

ini_set('display_errors', '1');
error_reporting(-1);

require 'sanitize.php';
require 'callQuery.php';

try {
    $pdo = new PDO('mysql:host=sql111.infinityfree.com:3306;dbname=if0_38758969_resumatedb', 'if0_38758969', 'CVTCit2025');
} catch(PDOException $ex) {
    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
    include 'error.html.php';
    throw $ex;
}
$errMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    $username = sanitizeString(INPUT_POST, 'signUpUsername');
    $password = sanitizeString(INPUT_POST, 'signUpPassword');
    $userType = sanitizeString(INPUT_POST, 'userType');

    if (!empty($username) && !empty($password)) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM User WHERE userName = ?");
        $checkStmt->execute([$username]);
        $userExists = $checkStmt->fetchColumn();

        if ($userExists) {
            echo "<p style='text-align:center; color:red;'>Username already exists. Please choose a different one.</p>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO User (userName, password, userType) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $userType]);
            echo "<p style='text-align:center;'>Account created successfully. <a href='login.php?pageType=login'>Login here</a>.</p>";
            
            $stmt = $pdo->prepare("SELECT * FROM User WHERE userName = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $_SESSION['userID']   = $user['userid'];
                $_SESSION['userName'] = $user['userName'];
                $_SESSION['userType'] = $user['userType'];
                $_SESSION['prevPage'] = 'signUp';
            
                $resumeStmt = $pdo->prepare("INSERT INTO Resume (userId, mainContext, createdOn, updatedOn) VALUES (?, ?, NOW(), NOW())");
                $resumeStmt->execute([$user['userid'], 'New Resume']);
                $newResumeId = $pdo->lastInsertId();

                $pdo->prepare("INSERT INTO Education (resumeId, institutionName, degree, fieldOfStudy, startDate, endDate) 
                            VALUES (?, 'Sample University', 'Bachelor of Science', 'Computer Science', '2020-08-01', '2024-05-01')")
                    ->execute([$newResumeId]);

                $pdo->prepare("INSERT INTO Work (resumeId, jobTitle, companyName, jobDescription, startDate, endDate) 
                            VALUES (?, 'Software Intern', 'Tech Corp', 'Worked on frontend development using React.', '2023-06-01', '2023-08-31')")
                    ->execute([$newResumeId]);

                $pdo->prepare("INSERT INTO Hobbies (resumeId, description) VALUES (?, 'Playing guitar')")
                    ->execute([$newResumeId]);

                $pdo->prepare("INSERT INTO Projects (resumeId, description) VALUES (?, 'Personal portfolio website')")
                    ->execute([$newResumeId]);

                $pdo->prepare("INSERT INTO skill (resumeId, skill, proficiency, startDate) 
                            VALUES (?, 'HTML/CSS', 'Intermediate', '2022-01-01')")
                    ->execute([$newResumeId]);
            
                header("Location: userAccount.php?pageType=view");
                exit();
            } else {
                $errMsg = "Account creation failed. Oh no, try again!";
            }
        }
    } else {
        $errMsg =  "Please fill out all fields.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitizeString(INPUT_POST, 'loginUsername');
    $password = sanitizeString(INPUT_POST, 'loginPassword');
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE userName = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
			
        if ($user) {
            $_SESSION['userID'] = $user['userid'];
            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userType'] = $user['userType'];

            header("Location: userAccount.php?pageType=view");
            exit();
        } else {
            $errMsg =  "Incorrect username or password.";
        }
    } else {
        $errMsg =  "Please fill out all fields.";
    }
}
?>
    <div class="banner">
        <h1>Resumate</h1>
        <span>
            <?php if (isset($_SESSION['userID'])): ?>
                <a href="login.php?logout=true">Log Out</a>
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
    <?php
     $thisPage = sanitizeString(INPUT_SERVER, 'PHP_SELF');
    if (sanitizeString(INPUT_GET, 'pageType') == 'login') {
    ?>
<!--        HTML START-->
        <div class="loginDiv">
            <form action="<?= htmlspecialchars($thisPage) ?>?pageType=login" method="post">
                <h2>Login</h2>
                <label for="loginUsername" class="inputLbl">Username: </label>
                <input type="text" name="loginUsername" id="loginUsername">
                <br><br>
                <label for="loginPassword" class="inputLbl">Password: </label>
                <input type="text" name="loginPassword" id="loginPassword">
                <br>
                <p style='text-align:center; color:red;'><?= $errMsg ?></p>
                <input type="submit" value="Login" name="login">
            </form>
            <br>
            <span><a href="login.php?pageType=signUp">Don't have an Account?</a></span>
        </div>
<!--        HTML END  -->
    <?php
    }
    else { // Sign Up
        ?>
<!--        HTML START-->
        <div class="loginDiv">
            <form action="<?=$thisPage?>" method="post">

                <h2>Sign Up</h2>
                <label for="signUpUsername" class="inputLbl">Username: </label>
                <input type="text" name="signUpUsername" id="signUpUsername">
                <br><br>

                <label for="signUpPassword" class="inputLbl">Password: </label>
                <input type="text" name="signUpPassword" id="signUpPassword">
                <br><br>
                <div class="selectorDiv">
                    <label for="userType" class="inputLbl">Account Type: </label>
                    <select name="userType" id="userType">
                        <option value="1">User</option>
                        <option value="0">Business</option>
                    </select>
                </div>
                <br>
                <p style='text-align:center; color:red;'><?= $errMsg ?></p>
                <div>
                    <input type="submit" value="Sign Up" name="signUp">
                </div>
            </form>
            <br>
            <span><a href="login.php?pageType=login">Already have an Account?</a></span>
        </div>
<!--        HTML END  -->

    <?php
    }

    ?>

</div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>
</html>

