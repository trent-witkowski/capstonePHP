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
    $check = $pdo->prepare("SELECT resumeId FROM Resume WHERE userId = ?");
    $check->execute([$userID]);
    $existingResume = $check->fetch(PDO::FETCH_ASSOC);

    if ($existingResume) {
        $existingId = $existingResume['resumeId'];
        header("Location: resume.php?pageType=view&resumeId=$existingId");
        exit();
    } else {
        $stmt = $pdo->prepare("INSERT INTO Resume (userId, mainContext, createdOn, updatedOn) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$userID, 'New Resume']);
        $newResumeId = $pdo->lastInsertId();
        header("Location: resume.php?pageType=view&resumeId=$newResumeId");
        exit();
    }
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

        <?php if (count($resumes) === 0): ?>
            <form method="post" action="<?= $thisPage ?>">
                <input type="hidden" name="createResume" value="1">
                <button type="submit">Create New Resume</button>
            </form>
        <?php else: ?>
            <p>You already have a resume.</p>
        <?php endif; ?>
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
            <form method="post" action="#">
                <div id="educationSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h2>Education</h2>
                        <button type="button" id="addEducationBtn"><img src="garbage/pencil.png" alt="Add More"></button>
                    </div>
                    <?php
                    $eduCount = count($educations);
                    if ($eduCount === 0) $eduCount = 1;
                    for ($i = 0; $i < $eduCount; $i++):
                        $edu = $educations[$i] ?? ['institutionName' => '', 'Degree' => '', 'fieldOfStudy' => '', 'startDate' => '', 'endDate' => ''];
                    ?>
                        <div class="educationBlock">
                            <label>Institution</label>
                            <input type="text" name="institution[]" value="<?= htmlspecialchars($edu['institutionName']) ?>">
                            <label>Degree</label>
                            <input type="text" name="degree[]" value="<?= htmlspecialchars($edu['degree']) ?>">
                            <label>Field of Study</label>
                            <input type="text" name="fieldOfStudy[]" value="<?= htmlspecialchars($edu['fieldOfStudy']) ?>">
                            <label>Start Date</label>
                            <input type="date" name="startDate[]" value="<?= $edu['startDate'] ?>">
                            <label>End Date</label>
                            <input type="date" name="endDate[]" value="<?= $edu['endDate'] ?>">
                            <hr>
                        </div>
                    <?php endfor; ?>
                </div>

                <div id="workSection" class="resumeInfo">
                    <div class="fieldDiv">
                        <h2>Work Experience</h2>
                        <button type="button" id="addWorkBtn"><img src="garbage/pencil.png" alt="Add More"></button>
                    </div>
                    <?php
                    $workCount = count($workHistory);
                    if ($workCount === 0) $workCount = 1;
                    for ($i = 0; $i < $workCount; $i++):
                        $job = $workHistory[$i] ?? ['jobTitle' => '', 'companyName' => '', 'jobDescription' => '', 'startDate' => '', 'endDate' => ''];
                    ?>
                        <div class="workBlock">
                            <label>Job Title</label>
                            <input type="text" name="jobTitle[]" value="<?= htmlspecialchars($job['jobTitle']) ?>">
                            <label>Company Name</label>
                            <input type="text" name="companyName[]" value="<?= htmlspecialchars($job['companyName']) ?>">
                            <label>Job Description</label>
                            <textarea name="jobDescription[]"><?= htmlspecialchars($job['jobDescription']) ?></textarea>
                            <label>Start Date</label>
                            <input type="date" name="workStartDate[]" value="<?= $job['startDate'] ?>">
                            <label>End Date</label>
                            <input type="date" name="workEndDate[]" value="<?= $job['endDate'] ?>">
                            <hr>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="btnDiv">
                    <input type="submit" value="Submit" name="submit">
                    <input type="submit" value="Cancel" name="cancel">
                </div>
            </form>
        <?php endif; ?>
    </div>
    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>

</html>

<script>
let educationOriginalValues = [];
let workOriginalValues = [];

document.querySelector('#addEducationBtn').addEventListener('click', () => {
    let container = document.querySelector('#educationSection');
    let blocks = container.querySelectorAll('.educationBlock');

    let block = blocks[0].cloneNode(true);
    block.querySelectorAll('input').forEach(input => input.value = '');
    block.querySelectorAll('input').forEach(input => input.setAttribute('readonly', true));
    addRemoveButton(block, 'education');
    container.appendChild(block);
});

document.querySelector('#addWorkBtn').addEventListener('click', () => {
    let container = document.querySelector('#workSection');
    let blocks = container.querySelectorAll('.workBlock');

    let block = blocks[0].cloneNode(true);
    block.querySelectorAll('input, textarea').forEach(input => input.value = '');
    block.querySelectorAll('input, textarea').forEach(input => input.setAttribute('readonly', true));
    addRemoveButton(block, 'work');
    container.appendChild(block);
});

function addRemoveButton(block, type) {
    if (!block.querySelector('.removeBtn')) {
        let btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'removeBtn';
        btn.textContent = 'Remove';
        btn.addEventListener('click', () => {
            let container = document.querySelector(`#${type}Section`);
            let blocks = container.querySelectorAll(`.${type}Block`);
            if (blocks.length > 1) {
                block.remove();
            } else {
                block.querySelectorAll('input, textarea').forEach(input => input.value = '');
            }
        });
        block.appendChild(btn);
    }
}

function toggleEditMode(sectionSelector, enable) {
    let section = document.querySelector(sectionSelector);
    let inputs = section.querySelectorAll('input, textarea');
    let submitBtns = document.querySelectorAll('.btnDiv');
    if (enable) {
        inputs.forEach(input => input.removeAttribute('readonly'));
        submitBtns.forEach(div => div.style.display = 'block');
    } else {
        inputs.forEach(input => {
            input.value = input.getAttribute('data-original') || '';
            input.setAttribute('readonly', true);
        });
        submitBtns.forEach(div => div.style.display = 'none');
    }
}

document.querySelectorAll('.editBtn').forEach(editBtn => {
    editBtn.addEventListener('click', () => {
        toggleEditMode('.resumeInfo', true);
        document.querySelectorAll('input, textarea').forEach(input => {
            input.setAttribute('data-original', input.value);
        });
        editBtn.style.display = 'none';
    });
});

document.querySelectorAll('.cancelBtn').forEach(cancelBtn => {
    cancelBtn.addEventListener('click', () => {
        toggleEditMode('.resumeInfo', false);
        document.querySelectorAll('.editBtn').forEach(btn => btn.style.display = 'inline-block');
    });
});
</script>
