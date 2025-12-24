<?php
require_once '../../src/auth.php';
require_once '../../src/json.php';
requireLogin();

$user = currentUser();
$requests = loadJson('../../data/requests.json');

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileId = $_POST['file_id'] ?? '';
    $permission = $_POST['permission'] ?? 'view';
    $note = trim($_POST['note'] ?? '');

    if ($fileId === '') die("File ID required");

    $requests[] = [
        'file_id'=>$fileId,
        'requested_by'=>$user,
        'permission'=>$permission,
        'note'=>$note,
        'created_at'=>date('c')
    ];
    saveJson('../../data/requests.json', $requests);
    $success = "Request submitted successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Request Access</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<h2>Request Access</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post">
    <input name="file_id" placeholder="File ID to request access" required><br>
    <select name="permission">
        <option value="view">View only</option>
        <option value="edit">Edit</option>
    </select><br>
    <textarea name="note" placeholder="Optional note"></textarea><br>
    <button>Request</button>
</form>
<a href="dashboard.php" class="btn">Back to Dashboard</a>
</body>
</html>
