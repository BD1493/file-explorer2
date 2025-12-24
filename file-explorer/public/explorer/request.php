<?php
require_once '../../src/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Just save request to json (Mock functionality)
    $requests = getJSON('requests.json');
    $requests[] = [
        'from' => currentUser(),
        'to' => $_POST['owner'],
        'note' => $_POST['note'],
        'access' => $_POST['access_type']
    ];
    saveJSON('requests.json', $requests);
    $success = "Request sent!";
}

$owner = $_GET['owner'] ?? '';
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
    <h2>Request Access from <?php echo htmlspecialchars($owner); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    <form method="POST">
        <input type="hidden" name="owner" value="<?php echo htmlspecialchars($owner); ?>">
        <label>Access Level:</label>
        <select name="access_type">
            <option value="view">View Only</option>
            <option value="edit">Edit</option>
        </select>
        <label>Note (Why?):</label>
        <textarea name="note"></textarea>
        <button type="submit" class="btn">Send Request</button>
        <a href="shared.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
