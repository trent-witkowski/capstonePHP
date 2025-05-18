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

$selectedResumeId = sanitizeString(INPUT_GET, 'resumeId');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $selectedResumeId = $_POST['resumeId'];

    // Handle Education
    $eduIds = $_POST['educationId'] ?? [];
    $institutions = $_POST['institution'] ?? [];
    $degrees = $_POST['degree'] ?? [];
    $fields = $_POST['fieldOfStudy'] ?? [];
    $starts = $_POST['startDate'] ?? [];
    $ends = $_POST['endDate'] ?? [];

    $existingEduStmt = $pdo->prepare("SELECT educationId FROM Education WHERE resumeId = ?");
    $existingEduStmt->execute([$selectedResumeId]);
    $existingEduIds = $existingEduStmt->fetchAll(PDO::FETCH_COLUMN);

    $postedEduIds = array_filter($eduIds);
    $toDelete = array_diff($existingEduIds, $postedEduIds);

    foreach ($toDelete as $id) {
        $del = $pdo->prepare("DELETE FROM Education WHERE educationId = ?");
        $del->execute([$id]);
    }

    for ($i = 0; $i < count($institutions); $i++) {
        $id = $eduIds[$i];
        if ($id) {
            $update = $pdo->prepare("UPDATE Education SET institutionName=?, degree=?, fieldOfStudy=?, startDate=?, endDate=? WHERE educationId=?");
            $update->execute([$institutions[$i], $degrees[$i], $fields[$i], $starts[$i], $ends[$i], $id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO Education (resumeId, institutionName, degree, fieldOfStudy, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$selectedResumeId, $institutions[$i], $degrees[$i], $fields[$i], $starts[$i], $ends[$i]]);
        }
    }

    // Handle Work
    $workIds = $_POST['workId'] ?? [];
    $jobTitles = $_POST['jobTitle'] ?? [];
    $companies = $_POST['companyName'] ?? [];
    $descs = $_POST['jobDescription'] ?? [];
    $wstarts = $_POST['workStartDate'] ?? [];
    $wends = $_POST['workEndDate'] ?? [];

    $existingWorkStmt = $pdo->prepare("SELECT workId FROM Work WHERE resumeId = ?");
    $existingWorkStmt->execute([$selectedResumeId]);
    $existingWorkIds = $existingWorkStmt->fetchAll(PDO::FETCH_COLUMN);

    $postedWorkIds = array_filter($workIds);
    $toDeleteWork = array_diff($existingWorkIds, $postedWorkIds);

    foreach ($toDeleteWork as $id) {
        $del = $pdo->prepare("DELETE FROM Work WHERE workId = ?");
        $del->execute([$id]);
    }

    for ($i = 0; $i < count($jobTitles); $i++) {
        $id = $workIds[$i];
        if ($id) {
            $update = $pdo->prepare("UPDATE Work SET jobTitle=?, companyName=?, jobDescription=?, startDate=?, endDate=? WHERE workId=?");
            $update->execute([$jobTitles[$i], $companies[$i], $descs[$i], $wstarts[$i], $wends[$i], $id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO Work (resumeId, jobTitle, companyName, jobDescription, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$selectedResumeId, $jobTitles[$i], $companies[$i], $descs[$i], $wstarts[$i], $wends[$i]]);
        }
    }

    header("Location: resume.php?pageType=view&resumeId=$selectedResumeId");
    exit();
}
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

    <?php
    $stmt = $pdo->prepare("SELECT userFirstName, userLastName FROM User WHERE userid = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $fullName = $user ? $user['userFirstName'] . ' ' . $user['userLastName'] : "Unknown";

    $eduStmt = $pdo->prepare("SELECT * FROM Education WHERE resumeId = ?");
    $eduStmt->execute([$selectedResumeId]);
    $educations = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

    $workStmt = $pdo->prepare("SELECT * FROM Work WHERE resumeId = ?");
    $workStmt->execute([$selectedResumeId]);
    $workHistory = $workStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h1>Viewing <?= htmlspecialchars($fullName) ?>'s Resume</h1>

    <form method="post">
        <input type="hidden" name="resumeId" value="<?= htmlspecialchars($selectedResumeId) ?>">

        <div class="infoTitle">
            <button type="button" class="editBtn"><img src="garbage/pencil.png" alt="Edit"></button>
        </div>

        <!-- === EDUCATION SECTION === -->
        <div id="educationSection" class="resumeInfo">
            <div class="fieldDiv">
                <h3>Education</h3>
                <button type="button" id="addEducationBtn">Add More</button>
            </div>

            <?php
            $eduCount = count($educations) ?: 1;
            for ($i = 0; $i < $eduCount; $i++):
                $edu = $educations[$i] ?? ['educationId' => '', 'institutionName' => '', 'degree' => '', 'fieldOfStudy' => '', 'startDate' => '', 'endDate' => ''];
            ?>
                <div class="educationBlock">
                    <input type="hidden" name="educationId[]" value="<?= htmlspecialchars($edu['educationId']) ?>">
                    <label>Institution</label>
                    <input type="text" name="institution[]" value="<?= htmlspecialchars($edu['institutionName']) ?>" readonly>
                    <label>Degree</label>
                    <input type="text" name="degree[]" value="<?= htmlspecialchars($edu['degree']) ?>" readonly>
                    <label>Field of Study</label>
                    <input type="text" name="fieldOfStudy[]" value="<?= htmlspecialchars($edu['fieldOfStudy']) ?>" readonly>
                    <label>Start Date</label>
                    <input type="date" name="startDate[]" value="<?= $edu['startDate'] ?>" readonly>
                    <label>End Date</label>
                    <input type="date" name="endDate[]" value="<?= $edu['endDate'] ?>" readonly>
                    <button type="button" class="removeBtn">Remove</button>
                    <hr>
                </div>
            <?php endfor; ?>
        </div>

        <!-- === WORK SECTION === -->
        <div id="workSection" class="resumeInfo">
            <div class="fieldDiv">
                <h3>Work Experience</h3>
                <button type="button" id="addWorkBtn">Add More</button>
            </div>

            <?php
            $workCount = count($workHistory) ?: 1;
            for ($i = 0; $i < $workCount; $i++):
                $job = $workHistory[$i] ?? ['workId' => '', 'jobTitle' => '', 'companyName' => '', 'jobDescription' => '', 'startDate' => '', 'endDate' => ''];
            ?>
                <div class="workBlock">
                    <input type="hidden" name="workId[]" value="<?= htmlspecialchars($job['workId']) ?>">
                    <label>Job Title</label>
                    <input type="text" name="jobTitle[]" value="<?= htmlspecialchars($job['jobTitle']) ?>" readonly>
                    <label>Company Name</label>
                    <input type="text" name="companyName[]" value="<?= htmlspecialchars($job['companyName']) ?>" readonly>
                    <label>Job Description</label>
                    <textarea name="jobDescription[]" readonly><?= htmlspecialchars($job['jobDescription']) ?></textarea>
                    <label>Start Date</label>
                    <input type="date" name="workStartDate[]" value="<?= $job['startDate'] ?>" readonly>
                    <label>End Date</label>
                    <input type="date" name="workEndDate[]" value="<?= $job['endDate'] ?>" readonly>
                    <button type="button" class="removeBtn">Remove</button>
                    <hr>
                </div>
            <?php endfor; ?>
        </div>

        <!-- SUBMIT -->
        <div class="btnDiv" style="display: none;">
            <input type="submit" name="submit" value="Submit">
            <button type="button" class="cancelBtn">Cancel</button>
        </div>
    </form>

    <div class="footerDiv">
        <p>Copyright 2025<span>&copy;</span>Resumate</p>
        <span><a href="#">totalyReal@resumate.com</a></span>
    </div>
</body>

</html>

<script>
    let originalValues = [];

    function storeOriginalValues() {
        originalValues = [];
        document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
            originalValues.push({
                element: input,
                value: input.value
            });
        });
    }

    function restoreOriginalValues() {
        originalValues.forEach(item => {
            item.element.value = item.value;
        });
    }

    document.querySelector('.editBtn').addEventListener('click', () => {
        storeOriginalValues();
        document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
            input.removeAttribute('readonly');
        });
        document.querySelector('.btnDiv').style.display = 'block';
        document.querySelector('.editBtn').style.display = 'none';
    });

    document.querySelector('.cancelBtn').addEventListener('click', () => {
        restoreOriginalValues();
        document.querySelectorAll('.resumeInfo input, .resumeInfo textarea').forEach(input => {
            input.setAttribute('readonly', true);
        });
        document.querySelector('.btnDiv').style.display = 'none';
        document.querySelector('.editBtn').style.display = 'inline-block';
    });

    function createRemoveButton(containerSelector, blockClass) {
        document.querySelectorAll(`${containerSelector} .${blockClass}`).forEach(block => {
            let btn = block.querySelector('.removeBtn');
            if (!btn) {
                btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = 'Remove';
                btn.classList.add('removeBtn');
                block.appendChild(btn);
            }

            btn.onclick = () => {
                let container = document.querySelector(containerSelector);
                let blocks = container.querySelectorAll(`.${blockClass}`);
                if (blocks.length > 1) {
                    block.remove();
                } else {
                    block.querySelectorAll('input, textarea').forEach(input => {
                        if (input.type === 'hidden') {
                            input.value = '';
                        } else {
                            input.value = '';
                        }
                    });
                }
            };
        });
    }

    document.querySelector('#addEducationBtn').addEventListener('click', () => {
        let container = document.querySelector('#educationSection');
        let first = container.querySelector('.educationBlock');
        let clone = first.cloneNode(true);

        clone.querySelectorAll('input, textarea').forEach(input => {
            input.value = '';
            input.removeAttribute('readonly');
        });

        clone.querySelector('input[name="educationId[]"]').value = ''; // clear ID
        container.appendChild(clone);
        createRemoveButton('#educationSection', 'educationBlock');
    });

    document.querySelector('#addWorkBtn').addEventListener('click', () => {
        let container = document.querySelector('#workSection');
        let first = container.querySelector('.workBlock');
        let clone = first.cloneNode(true);

        clone.querySelectorAll('input, textarea').forEach(input => {
            input.value = '';
            input.removeAttribute('readonly');
        });

        clone.querySelector('input[name="workId[]"]').value = ''; // clear ID
        container.appendChild(clone);
        createRemoveButton('#workSection', 'workBlock');
    });

    // Attach remove buttons to all blocks on initial page load
    createRemoveButton('#educationSection', 'educationBlock');
    createRemoveButton('#workSection', 'workBlock');
</script>