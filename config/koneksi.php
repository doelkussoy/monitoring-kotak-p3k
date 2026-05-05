<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "saranaprasarana";

date_default_timezone_set('Asia/Jakarta');

$conn = mysqli_connect($host, $user, $pass, $db) or die("Koneksi gagal!" . mysqli_connect_error());