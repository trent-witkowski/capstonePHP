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

ini_set('display_errors', '1');
error_reporting(-1);

require 'sanitize.php';
require 'callQuery.php';

try {
    $pdo = new PDO(
        'mysql:host=sql111.infinityfree.com:3306;dbname=if0_38758969_resumatedb',
        'if0_38758969',
        'CVTCit2025'
    );
} catch (PDOException $ex) {
    $error = 'Unable to connect to the database server<br><br>' . $ex->getMessage();
    include 'error.html.php';
    throw $ex;
}

$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    header("Location: login.php?pageType=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createResume'])) {
    $stmt = $pdo->prepare("INSERT INTO Resume (userId, mainContext, createdOn, updatedOn) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute([$userID, 'New Resume']);
    $newResumeId = $pdo->lastInsertId();
    header("Location: resume.php?pageType=view&resumeId=$newResumeId");
    exit();
}

$stmt = $pdo->prepare("SELECT userFirstName, userLastName FROM User WHERE userid = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$fullName = $user ? $user['userFirstName'] . ' ' . $user['userLastName'] : "Unknown";

$resumesStmt = $pdo->prepare("SELECT resumeId, mainContext, createdOn FROM Resume WHERE userid = ?");
$resumesStmt->execute([$userID]);
$resumes = $resumesStmt->fetchAll(PDO::FETCH_ASSOC);

$thisPage = htmlspecialchars($_SERVER['PHP_SELF']);
$selectedResumeId = sanitizeString(INPUT_GET, 'resumeId');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/resume.css">
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
        <h1>Viewing <?= htmlspecialchars($fullName) ?>'s Resume</h1>
        <br>

        <h2>Your Resumes</h2>

        <!-- Create new resume button -->
        <form method="post" action="<?= $thisPage ?>">
            <input type="hidden" name="createResume" value="1">
            <button type="submit">Create New Resume</button>
        </form>
        <br>

        <!-- List O'resumes -->
        <?php if (count($resumes) === 0): ?>
            <p>You don't have any resumes yet.</p>
        <?php endif; ?>

        <?php foreach ($resumes as $resume): ?>
            <form method="get" action="<?= $thisPage ?>">
                <input type="hidden" name="pageType" value="view">
                <input type="hidden" name="resumeId" value="<?= $resume['resumeId'] ?>">
                <button type="submit">
                    <?= htmlspecialchars($resume['mainContext']) ?> (Created: <?= date("M Y", strtotime($resume['createdOn'])) ?>)
                </button>
            </form>
        <?php endforeach; ?>
        <hr>

        <?php if ($selectedResumeId): ?>
            <?php
            $eduStmt = $pdo->prepare("SELECT * FROM Education WHERE resumeId = ?");
            $eduStmt->execute([$selectedResumeId]);
            $educations = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

            $workStmt = $pdo->prepare("SELECT * FROM Work WHERE resumeId = ?");
            $workStmt->execute([$selectedResumeId]);
            $workHistory = $workStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="resumeInfo">
                <h2>Education</h2>
                <?php foreach ($educations as $edu): ?>
                    <div class="resumeEntry">
                        <strong><?= htmlspecialchars($edu['institutionName']) ?></strong><br>
                        <?= htmlspecialchars($edu['Degree']) ?> in <?= htmlspecialchars($edu['fieldOfStudy']) ?><br>
                        <?= date("M Y", strtotime($edu['startDate'])) ?> - <?= date("M Y", strtotime($edu['endDate'])) ?>
                        <hr>
                    </div>
                <?php endforeach; ?>

                <h2>Work Experience</h2>
                <?php foreach ($workHistory as $job): ?>
                    <div class="resumeEntry">
                        <strong><?= htmlspecialchars($job['jobTitle']) ?></strong> at <?= htmlspecialchars($job['companyName']) ?><br>
                        <?= date("M Y", strtotime($job['startDate'])) ?> - <?= date("M Y", strtotime($job['endDate'])) ?><br>
                        <em><?= htmlspecialchars($job['jobDescription']) ?></em>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>

</html>