<?php require_once '../../src/auth.php'; require_once '../../src/permissions.php'; requireLogin();
$user = currentUser(); $file = $_GET['file']; $owner = $_GET['owner'] ?? $user;
$access = canAccess($user, $file, $owner);
if (!$access) die("Denied.");
$path = STORAGE_PATH . "/users/$owner/$file";
$content = file_exists($path) ? file_get_contents($path) : ""; ?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="../assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script></head>
<body><div class="container" style="max-width: 1100px;">
    <div class="nav">
        <h2><?php echo htmlspecialchars($file); ?> <small id="status" style="font-weight:normal; font-size:12px; color:gray;"><?php echo ($access==='view')?'(View Only)':''; ?></small></h2>
        <div><button onclick="dlPDF()" class="btn btn-secondary">Download PDF</button> <a href="dashboard.php" class="btn btn-secondary">Close</a></div>
    </div>
    <div id="pdf-area" style="display:none; padding:50px; white-space:pre-wrap; font-family: serif;"></div>
    <div class="editor-container"><textarea id="editor" class="editor-textarea" <?php echo ($access==='view')?'readonly':''; ?>><?php echo htmlspecialchars($content); ?></textarea></div>
</div>
<script>
function dlPDF() { const e = document.getElementById('pdf-area'); e.innerText = document.getElementById('editor').value; e.style.display='block'; html2pdf().from(e).save().then(()=>e.style.display='none'); }
<?php if($access === 'edit'): ?>
let t; document.getElementById('editor').addEventListener('input', () => {
    document.getElementById('status').innerText = "Typing...";
    clearTimeout(t);
    t = setTimeout(() => {
        document.getElementById('status').innerText = "Saving...";
        let fd = new FormData(); fd.append('file','<?php echo $file; ?>'); fd.append('owner','<?php echo $owner; ?>'); fd.append('content', document.getElementById('editor').value);
        fetch('api_save.php', {method:'POST', body:fd}).then(r=>r.json()).then(d=>{ document.getElementById('status').innerText = "Saved at " + d.time; });
    }, 1500);
});
<?php endif; ?>
</script></body></html>
