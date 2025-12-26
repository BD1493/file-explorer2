<?php
require_once '../../src/json.php'; session_start();
$id = $_GET['id'];
$allChats = getJSON('chats');

if(isset($_POST['msg']) && !empty($_POST['msg'])) {
    if(!isset($allChats[$id])) $allChats[$id] = [];
    $allChats[$id][] = [
        'u' => $_SESSION['user'],
        'm' => $_POST['msg'],
        't' => date('H:i')
    ];
    saveJSON('chats', $allChats);
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<div class="nav">
    <div class="logo">Chat: <?php echo $id; ?></div>
    <a href="dashboard.php" class="btn">Exit</a>
</div>
<div class="container">
    <div id="chatbox" class="card" style="height: calc(100vh - 160px); overflow-y: auto; background:#f5f5f5; display:flex; flex-direction:column; gap:10px;">
        <?php 
        if(isset($allChats[$id])) {
            foreach($allChats[$id] as $msg) {
                $isMe = $msg['u'] === $_SESSION['user'];
                echo "<div style='align-self:" . ($isMe ? 'flex-end' : 'flex-start') . "; background:" . ($isMe ? '#d2e3fc' : 'white') . "; padding:8px 12px; border-radius:12px; max-width:70%; box-shadow:0 1px 2px rgba(0,0,0,0.1);'>";
                echo "<div style='font-size:10px; color:#555; margin-bottom:2px;'><b>" . $msg['u'] . "</b> " . $msg['t'] . "</div>";
                echo "<div>" . htmlspecialchars($msg['m']) . "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>
    <form method="POST" style="display:flex; gap:10px;">
        <input type="text" name="msg" placeholder="Type a message..." autocomplete="off" style="margin:0;" autofocus required>
        <button class="btn btn-primary">Send</button>
    </form>
</div>
<script>
    var box = document.getElementById('chatbox');
    box.scrollTop = box.scrollHeight;
    setTimeout(() => location.reload(), 3000);
</script></body></html>
