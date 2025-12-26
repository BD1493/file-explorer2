<?php
require_once '../../src/json.php'; session_start();
$id = $_GET['id'];
$chats = getJSON('chats'); // Loads existing chats
if(isset($_POST['m'])) {
    if(!isset($chats[$id])) $chats[$id] = [];
    $chats[$id][] = ['u'=>$_SESSION['user'], 'm'=>$_POST['m'], 't'=>date('H:i')];
    saveJSON('chats', $chats);
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><strong>í²¬ Chat: <?php echo $id; ?></strong> <a href="dashboard.php" class="btn">Exit</a></div>
<div class="container">
    <div id="box" class="card" style="height:60vh; overflow-y:auto; background:#f1f3f4;">
        <?php if(isset($chats[$id])) foreach($chats[$id] as $msg): ?>
            <div style="background:white; padding:8px; border-radius:4px; margin-bottom:8px; border:1px solid #ddd;">
                <b><?php echo $msg['u']; ?>:</b> <?php echo htmlspecialchars($msg['m']); ?>
                <span style="font-size:10px; color:#666; float:right;"><?php echo $msg['t']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" style="display:flex; gap:10px;">
        <input type="text" name="m" placeholder="Type a message..." autocomplete="off" autofocus>
        <button class="btn btn-primary">Send</button>
    </form>
</div>
<script>let b=document.getElementById('box'); b.scrollTop=b.scrollHeight; setTimeout(()=>location.reload(), 3000);</script>
</body></html>
