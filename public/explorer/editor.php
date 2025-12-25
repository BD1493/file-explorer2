<?php require_once '../../src/json.php'; session_start();
$file = $_GET['file']; $path = $_GET['path']; 
$owner = explode('/', $path)[0];
$perm = canAccess($_SESSION['user'], $file, $owner);
if(!$perm) die("Access Denied");
$full = STORAGE_PATH . "/users/" . $path . "/" . $file;
$content = file_exists($full) ? file_get_contents($full) : "";
$ext = pathinfo($file, PATHINFO_EXTENSION);
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script></head><body>
<div class="nav">
    <span>File: <b><?php echo $file; ?></b> (<?php echo $perm; ?>)</span>
    <div><button onclick="save()" class="btn btn-primary" id="save-btn">Save</button> <a href="dashboard.php?path=<?php echo $path; ?>" class="btn">Close</a></div>
</div>
<div id="editor-main"></div>
<script>
    var editor;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('editor-main'), {
            value: <?php echo json_encode($content); ?>,
            language: '<?php echo (in_array($ext, ['html','css','php','js'])) ? ($ext=='js'?'javascript':$ext) : 'plaintext'; ?>',
            readOnly: <?php echo $perm == 'view' ? 'true' : 'false'; ?>,
            automaticLayout: true, fontSize: 16
        });
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyK, function() {
            let url = prompt("Link URL:"); if(url) {
                let s = editor.getSelection(); let t = editor.getModel().getValueInRange(s);
                editor.executeEdits("", [{ range: s, text: `<a href="${url}">${t}</a>` }]);
            }
        });
    });
    function save() {
        let fd = new FormData(); fd.append('c', editor.getValue()); fd.append('p', '<?php echo $full; ?>');
        document.getElementById('save-btn').innerText = "Saving...";
        fetch('api_save.php', { method: 'POST', body: fd }).then(() => { document.getElementById('save-btn').innerText = "Save"; });
    }
</script></body></html>
