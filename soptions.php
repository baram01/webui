<?php
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 15) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>
<div id="sbackup">
<fieldset class=" collapsible"><legend>Options</legend>
<p class="p_body"><?php echo "Feature Not Available"; ?></p>
</fieldset>
</div>
