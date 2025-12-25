<?php
require_once '../../src/json.php'; session_start();
$id = $_GET['id'];
$allChats = getJSON('chats');
if(isset($_POST['m'])) {
    $allChats[$id][] = ['u'=>$_SESSION['user'], 'm'=>$_POST['m'], 't'=>date('H:i')];
    saveJSON('chats', $allChats);
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><strong>Chat Room: <?php echo $id; ?></strong> <a href="dashboard.php" class="btn">Exit</a></div>
<div class="container">
    <div id="box" class="card" style="height:60vh; overflow-y:auto;">
        <?php foreach($allChats[$id] ?? [] as $line): ?>
            <p><b><?php echo $line['u']; ?>:</b> <?php echo htmlspecialchars($line['m']); ?> <small style="color:gray"><?php echo $line['t']; ?></small></p>
        <?php endforeach; ?>
    </div>
    <form method="POST" style="display:flex; gap:10px;">
        <input type="text" name="m" placeholder="Message..." autocomplete="off" autofocus>
        <button class="btn btn-primary">Send</button>
    </form>
</div>
<script>let b=document.getElementById('box'); b.scrollTop=b.scrollHeight; setTimeout(()=>location.reload(), 3000);</script>
</body></html>
