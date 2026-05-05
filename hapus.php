<?php include_once "config/koneksi.php";

if (isset($_GET['id'])) {

    if (isset($_GET['checklist'])) {
        $result = mysqli_query($conn, "DELETE FROM checklist WHERE id = '$_GET[id]' ");
        
        if ($result) {
            echo "<script>alert('Data berhasil dihapus!')</script>";
            echo "<script>window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Data gagal dihapus!');</script>";
            echo "<script>window.location.href='home.php';</script>";
        }  
    }

    elseif (isset($_GET['apar'])) {
        $result = mysqli_query($conn, "DELETE FROM apar_data WHERE id = '$_GET[id]' ");
        
        if ($result) {
            echo "<script>alert('Data berhasil dihapus!')</script>";
            echo "<script>window.location.href='data_apar.php';</script>";
        } else {
            echo "<script>alert('Data gagal dihapus!');</script>";
            echo "<script>window.location.href='data_apar.php';</script>";
        }  
    }

    elseif (isset($_GET['hapus_apar'])) {
        $id = (int)$_GET['id'];
        $res1 = mysqli_query($conn, "DELETE FROM apar WHERE id = $id");
        $res2 = mysqli_query($conn, "DELETE FROM checklist_apar WHERE apar_id = $id");
        
        if ($res1) {
            header("Location: apar_home.php?pesan=hapus_sukses");
            exit;
        } else {
            header("Location: apar_home.php?pesan=hapus_gagal");
            exit;
        }  
    }

    elseif (isset($_GET['hapus_gedung'])) {
        $id = (int)$_GET['id'];
        $res1 = mysqli_query($conn, "DELETE FROM gedung WHERE id = $id");
        $res2 = mysqli_query($conn, "DELETE FROM checklist_gedung WHERE gedung_id = $id");
        
        if ($res1) {
            header("Location: gedung_home.php?pesan=hapus_sukses");
            exit;
        } else {
            header("Location: gedung_home.php?pesan=hapus_gagal");
            exit;
        }  
    }

    elseif (isset($_GET['hapus_hydrant'])) {
        $id = (int)$_GET['id'];
        $res1 = mysqli_query($conn, "DELETE FROM hydrant WHERE id = $id");
        $res2 = mysqli_query($conn, "DELETE FROM checklist_hydrant WHERE hydrant_id = $id");
        
        if ($res1) {
            header("Location: hydrant_home.php?pesan=hapus_sukses");
            exit;
        } else {
            header("Location: hydrant_home.php?pesan=hapus_gagal");
            exit;
        }  
    }

    elseif (isset($_GET['hapus_grease_trap'])) {
        $id = (int)$_GET['id'];
        $res1 = mysqli_query($conn, "DELETE FROM grease_trap WHERE id = $id");
        $res2 = mysqli_query($conn, "DELETE FROM checklist_grease_trap WHERE grease_trap_id = $id");
        
        if ($res1) {
            header("Location: grease_trap_home.php?pesan=hapus_sukses");
            exit;
        } else {
            header("Location: grease_trap_home.php?pesan=hapus_gagal");
            exit;
        }  
    }
}

$conn->close();
?>