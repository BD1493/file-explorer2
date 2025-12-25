<?php require_once '../../src/json.php'; session_start();
$id = $_GET['id']; $chats = getJSON('chats');
if(isset($_POST['m'])) { $chats[$id][] = ['u'=>$_SESSION['user'],'m'=>$_POST['m'],'t'=>date('H:i')]; saveJSON('chats', $chats); }
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav"><strong>Chat: <?php echo $id; ?></strong> <a href="dashboard.php" class="btn">Back</a></div>
<div class="container">
    <div id="c" class="card" style="height:60vh; overflow-y:auto; margin-bottom:10px;">
        <?php foreach($chats[$id] ?? [] as $m): ?>
            <p><b><?php echo $m['u']; ?>:</b> <?php echo htmlspecialchars($m['m']); ?> <small><?php echo $m['t']; ?></small></p>
        <?php endforeach; ?>
    </div>
    <form method="POST" style="display:flex; gap:10px;"><input type="text" name="m" placeholder="Message" autocomplete="off" style="margin:0"><button class="btn btn-primary">Send</button></form>
</div>
<script>let x=document.getElementById('c'); x.scrollTop=x.scrollHeight; setTimeout(()=>location.reload(), 2500);</script>
</body></html>
