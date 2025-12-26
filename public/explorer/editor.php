<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) die("Please Login");

$file = $_GET['file'];
$owner = $_GET['owner'];
$path = $_GET['path'] ?? $owner;
$realPath = STORAGE_PATH . "/users/" . $path . "/" . $file;

// Permissions Logic
$canEdit = false;
if($_SESSION['user'] === $owner) $canEdit = true;
else {
    // Check Shares
    $shares = getJSON('shares');
    if(isset($_SESSION['unlocked_shares'])) {
        foreach($_SESSION['unlocked_shares'] as $alias) {
            foreach($shares as $s) {
                if($s['alias'] === $alias && $s['file'] === $file && $s['perm'] === 'edit') $canEdit = true;
            }
        }
    }
    // Check Public
    foreach($shares as $s) {
        if($s['is_public'] && $s['file'] === $file && $s['owner'] === $owner && $s['perm'] === 'edit') $canEdit = true;
    }
}

$content = file_exists($realPath) ? file_get_contents($realPath) : "";
$ext = pathinfo($file, PATHINFO_EXTENSION);
$lang = match($ext) { 'html'=>'html','js'=>'javascript','css'=>'css','php'=>'php','json'=>'json',default=>'plaintext' };
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script></head><body>
<div class="nav">
    <div><?php echo $file; ?> <?php if(!$canEdit) echo "(Read Only)"; ?></div>
    <div>
        <span id="msg" style="margin-right:10px; color:#188038;"></span>
        <?php if($canEdit): ?><button onclick="save()" class="btn btn-primary">Save</button><?php endif; ?>
        <button onclick="history.back()" class="btn">Close</button>
    </div>
</div>
<div id="editor"></div>
<script>
    var editor;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('editor'), {
            value: <?php echo json_encode($content); ?>, language: '<?php echo $lang; ?>', readOnly: <?php echo $canEdit?'false':'true'; ?>, theme: 'vs-light', automaticLayout: true
        });
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyK, function() {
            let u = prompt("Link URL:"); if(u) {
                let s = editor.getSelection(); let t = editor.getModel().getValueInRange(s);
                editor.executeEdits("", [{ range: s, text: `<a href="${u}">${t}</a>` }]);
            }
        });
    });
    function save() {
        document.getElementById('msg').innerText = "Saving...";
        let fd = new FormData(); fd.append('p', '<?php echo $realPath; ?>'); fd.append('c', editor.getValue());
        fetch('api_save.php', { method:'POST', body:fd }).then(()=>{
            document.getElementById('msg').innerText = "Saved!"; setTimeout(()=>document.getElementById('msg').innerText="", 2000);
        });
    }
</script></body></html>
