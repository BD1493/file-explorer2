<?php
require_once '../../src/json.php'; session_start();
$id = $_GET['id'];
$chats = getJSON('chats');
if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['m'])){
    $chats[$id][] = ['u'=>$_SESSION['user'], 'm'=>$_POST['m'], 't'=>date('H:i')];
    saveJSON('chats', $chats);
    header("Location: chat.php?id=".urlencode($id));
    exit;
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><b>Chat: <?=$id?></b> <a href="dashboard.php" class="btn">Back</a></div>
<div class="container">
    <div class="card" id="cb" style="height:400px; overflow-y:auto; background:#f1f1f1; display:flex; flex-direction:column; gap:10px;">
        <?php if(isset($chats[$id])) foreach($chats[$id] as $msg): ?>
            <div style="background:white; padding:10px; border-radius:8px; align-self:<?=$msg['u']==$_SESSION['user']?'flex-end':'flex-start'?>;">
                <small><?=$msg['u']?> - <?=$msg['t']?></small><div><?=htmlspecialchars($msg['m'])?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <form method="POST"><input name="m" placeholder="Type a message..." autofocus autocomplete="off"></form>
</div>
<script>var b=document.getElementById('cb'); b.scrollTop=b.scrollHeight; setTimeout(()=>location.reload(), 4000);</script>
</body></html>
