<?php

if(!isset($_SESSION['username'])) {
    echo "<script>alert('Anda belum login. Silahkan login terlebih dahulu!');
    window.location='index.php'</script>";
    exit;
}

?>
