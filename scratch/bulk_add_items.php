<?php
require 'db.php';

$items_to_add = [
    ['name' => 'Kasa steril terbungkus', 'satuan' => 'box'],
    ['name' => 'Perban (lebar 5 cm)', 'satuan' => 'roll'],
    ['name' => 'Perban (lebar 10 cm)', 'satuan' => 'roll'],
    ['name' => 'Plester (lebar 1,25 cm)', 'satuan' => 'roll'],
    ['name' => 'Plester cepat', 'satuan' => 'box'],
    ['name' => 'Kapas 25 gr', 'satuan' => 'bungkus'],
    ['name' => 'Kain segitiga (mitella)', 'satuan' => 'pcs'],
    ['name' => 'Gunting', 'satuan' => 'pcs'],
    ['name' => 'Peniti', 'satuan' => 'set'],
    ['name' => 'Sarung tangan sekali pakai', 'satuan' => 'pasang'],
    ['name' => 'Masker', 'satuan' => 'box'],
    ['name' => 'Pinset', 'satuan' => 'pcs'],
    ['name' => 'Lampu senter', 'satuan' => 'pcs'],
    ['name' => 'Gelas cuci mata', 'satuan' => 'pcs'],
    ['name' => 'Kantong plastik bersih', 'satuan' => 'pack'],
    ['name' => 'Aquades (100ml lar. saline)', 'satuan' => 'botol'],
    ['name' => 'Povidon Iodin (60 ml)', 'satuan' => 'botol'],
    ['name' => 'Alkohol 70%', 'satuan' => 'botol'],
    ['name' => 'Buku panduan P3K di tempat kerja', 'satuan' => 'pcs'],
    ['name' => 'Buku catatan', 'satuan' => 'pcs']
];

$exp_date = date('Y-m-d', strtotime('+1 year'));

// Get all locations
$locations = mysqli_query($conn, "SELECT id FROM p3k_lokasi");

while ($loc = mysqli_fetch_assoc($locations)) {
    $loc_id = $loc['id'];
    echo "Processing Location ID: $loc_id...\n";
    
    foreach ($items_to_add as $item) {
        $name = mysqli_real_escape_string($conn, $item['name']);
        $satuan = mysqli_real_escape_string($conn, $item['satuan']);
        
        // Check if item already exists in this location to avoid duplicates
        $check = mysqli_query($conn, "SELECT id FROM p3k_items WHERE lokasi_id = '$loc_id' AND nama_item = '$name'");
        
        if (mysqli_num_rows($check) == 0) {
            $insert = mysqli_query($conn, "INSERT INTO p3k_items (lokasi_id, nama_item, stok, min_stok, satuan, tgl_kadaluarsa) 
                VALUES ('$loc_id', '$name', 1, 0, '$satuan', '$exp_date')");
            
            if ($insert) {
                echo "  Added: $name\n";
            } else {
                echo "  Error adding $name: " . mysqli_error($conn) . "\n";
            }
        } else {
            echo "  Skipped: $name (already exists)\n";
        }
    }
}

echo "\nBulk import completed!";
?>
