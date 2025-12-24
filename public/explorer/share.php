<?php
require_once '../../src/auth.php';
requireLogin();
$file = $_GET['file'];
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isPublic = isset($_POST['is_public']);
    $shareName = $_POST['share_name'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // If public, we generate a random share name to identify it easily or use filename
    if ($isPublic) {
        $shareName = "public_" . uniqid(); 
        $password = "";
    }

    $shares = getShares();
    $shares[] = [
        'owner' => $user,
        'filename' => $file,
        'share_name' => $shareName, // The "username/alias" you give to a friend
        'password' => $password,
        'permission' => $_POST['permission'], // view or edit
        'is_public' => $isPublic
    ];
    saveShares($shares);
    
    if($isPublic) {
        $success = "File is Public! Share Name ID: $shareName";
    } else {
        $success = "Shared! Give your friend this Name: '$shareName' and Password: '$password'";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/app.js"></script>
</head>
<body>
<div class="container">
    <h2>Share: <?php echo htmlspecialchars($file); ?></h2>
    <?php if(isset($success)) echo "<div class='alert' style='background:#d4edda;color:#155724'>$success</div>"; ?>
    
    <form method="POST">
        <label>Permission:</label>
        <select name="permission">
            <option value="view">View Only</option>
            <option value="edit">Edit</option>
        </select>
        <br><br>
        
        <label>
            <input type="checkbox" id="is_public" name="is_public" onclick="toggleShareFields()"> 
            Make Public (No password needed)
        </label>
        <br><br>

        <div id="private_fields">
            <label>Share Name (Give this name to your friend):</label>
            <input type="text" id="share_name" name="share_name" placeholder="e.g. myproject">
            
            <label>Password (Optional):</label>
            <input type="text" id="share_pass" name="password" placeholder="Secret123">
        </div>
        
        <button type="submit" class="btn">Share File</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
