<?php
require_once '../../src/json.php'; session_start();
$file = $_GET['file']; $owner = $_GET['owner']; $path = $_GET['path'];
$realPath = STORAGE_PATH . "/users/" . $path . "/" . $file;
$content = file_exists($realPath) ? file_get_contents($realPath) : "";
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script></head><body>
<div class="nav"><b>Editing: <?=$file?></b> <div><span id="st" style="color:green;"></span> <button onclick="save()" class="btn btn-primary">Save</button> <button onclick="history.back()" class="btn">Back</button></div></div>
<div id="editor"></div>
<script>
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        window.ed = monaco.editor.create(document.getElementById('editor'), { value: <?=json_encode($content)?>, language: 'html', theme: 'vs-light' });
    });
    function save() {
        let fd = new FormData(); fd.append('p', '<?=$realPath?>'); fd.append('c', window.ed.getValue());
        fetch('api_save.php', { method:'POST', body:fd }).then(()=>{ document.getElementById('st').innerText="Saved!"; setTimeout(()=>document.getElementById('st').innerText="", 2000); });
    }
</script></body></html>
