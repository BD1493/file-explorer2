<?php
require_once '../../src/json.php'; session_start();
if(!isset($_SESSION['user'])) die("Login required");

$file = $_GET['file'];
$owner = $_GET['owner'];
$path = $_GET['path'] ?? $owner; 
$realPath = STORAGE_PATH . "/users/" . $path . "/" . $file;

// Permissions
$canEdit = false;
if($_SESSION['user'] === $owner) {
    $canEdit = true;
} else {
    $shares = getJSON('shares');
    foreach($shares as $s) {
        if($s['file'] === $file && $s['owner'] === $owner) {
            if(isset($s['is_public']) && $s['is_public'] && $s['perm'] === 'edit') $canEdit = true;
            if(isset($_SESSION['unlocked'][$s['alias']]) && $s['perm'] === 'edit') $canEdit = true;
        }
    }
}

$content = file_exists($realPath) ? file_get_contents($realPath) : "";
$ext = pathinfo($file, PATHINFO_EXTENSION);
$langMap = ['html'=>'html', 'php'=>'php', 'js'=>'javascript', 'css'=>'css', 'json'=>'json'];
$lang = $langMap[$ext] ?? 'plaintext';
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script><title>Editor</title></head><body>
<div class="nav">
    <div style="font-weight:bold;"><?php echo $file; ?> <span style="font-size:12px; font-weight:normal"><?php echo $canEdit ? '(Editing)' : '(Read Only)'; ?></span></div>
    <div>
        <span id="status" style="margin-right:15px; font-size:12px; color:green;"></span>
        <?php if($canEdit): ?><button onclick="save()" class="btn btn-primary">Save Changes</button><?php endif; ?>
        <button onclick="history.back()" class="btn">Close</button>
    </div>
</div>
<div id="editor"></div>
<script>
    var editor;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('editor'), {
            value: <?php echo json_encode($content); ?>, language: '<?php echo $lang; ?>', readOnly: <?php echo $canEdit ? 'false' : 'true'; ?>, theme: 'vs-light', automaticLayout: true
        });
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyK, function() {
            let url = prompt("Enter Link URL:");
            if(url) {
                let sel = editor.getSelection();
                let text = editor.getModel().getValueInRange(sel);
                editor.executeEdits("", [{ range: sel, text: `<a href="${url}">${text}</a>` }]);
            }
        });
    });
    function save() {
        document.getElementById('status').innerText = "Saving...";
        let fd = new FormData();
        fd.append('path', '<?php echo $realPath; ?>');
        fd.append('content', editor.getValue());
        fetch('api_save.php', { method: 'POST', body: fd }).then(() => {
            document.getElementById('status').innerText = "Saved!";
            setTimeout(() => document.getElementById('status').innerText = "", 2000);
        });
    }
</script></body></html>
