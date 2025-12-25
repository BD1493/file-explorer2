<?php
require_once '../../src/json.php'; session_start();
$user = $_SESSION['user'] ?? null;
if(!$user) die("Login required");

$file = $_GET['file'];
$owner = $_GET['owner']; // We must know who owns the file to find it
$subpath = $_GET['path'] ?? $owner; // The folder path inside user dir

// Permission Check
$access = 'denied';
if($user === $owner) {
    $access = 'edit';
} else {
    // Check shared access
    if(isset($_SESSION['access_list'])) {
        foreach($_SESSION['access_list'] as $alias => $allowed) {
            if($allowed) {
                // Find this alias in database to confirm file match
                $shares = getJSON('shares');
                foreach($shares as $s) {
                    if($s['alias'] === $alias && $s['file'] === $file && $s['owner'] === $owner) {
                        $access = $s['perm'];
                    }
                }
            }
        }
    }
}
if($access === 'denied') die("Access Denied. Go to 'Find Shared' and enter credentials.");

// Load Content
$realPath = STORAGE_PATH . "/users/" . $subpath . "/" . $file;
$content = file_exists($realPath) ? file_get_contents($realPath) : "";
$ext = pathinfo($file, PATHINFO_EXTENSION);
$lang = match($ext) { 'html'=>'html', 'js'=>'javascript', 'css'=>'css', 'php'=>'php', 'json'=>'json', default=>'plaintext' };
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script></head><body>
<div class="nav">
    <span><b><?php echo $file; ?></b> (<?php echo strtoupper($access); ?> Mode)</span>
    <div>
        <span id="stat" style="margin-right:10px; color:gray"></span>
        <?php if($access === 'edit'): ?>
            <button onclick="save()" class="btn btn-primary">Save</button>
        <?php endif; ?>
        <button onclick="history.back()" class="btn">Close</button>
    </div>
</div>
<div id="editor"></div>
<script>
    var editor;
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('editor'), {
            value: <?php echo json_encode($content); ?>,
            language: '<?php echo $lang; ?>',
            readOnly: <?php echo $access==='view'?'true':'false'; ?>,
            theme: 'vs-light', automaticLayout: true
        });
        // CTRL + K Logic
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyK, function() {
            let u = prompt("Enter URL:");
            if(u){
                let sel = editor.getSelection();
                let txt = editor.getModel().getValueInRange(sel);
                editor.executeEdits("", [{ range: sel, text: `<a href="${u}">${txt}</a>` }]);
            }
        });
    });
    function save(){
        document.getElementById('stat').innerText = "Saving...";
        let fd = new FormData();
        fd.append('p', '<?php echo $realPath; ?>');
        fd.append('c', editor.getValue());
        fetch('api_save.php', { method:'POST', body:fd }).then(()=>{
            document.getElementById('stat').innerText = "Saved!";
            setTimeout(()=>document.getElementById('stat').innerText="", 2000);
        });
    }
</script></body></html>
