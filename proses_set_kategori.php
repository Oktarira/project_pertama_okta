<?php
    include 'koneksi.php';
    session_start();

    //Isi variabel
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $kategori_id = $_POST['kategori_id'];
        $jumlah_anggaran = $_POST['jumlah_anggaran'];
        $total_anggaran = $_POST['total_anggaran'];

  
    

    // Menghapus semua karakter selain angka
    $jumlah_anggaran = preg_replace("/[^0-9]/", "", $jumlah_anggaran);
    $total_anggaran = preg_replace("/[^0-9]/", "", $total_anggaran);

    // ambil tahun dan bulan dari input bulan_tahun
    list($tahun, $bulan) = explode('-', $_POST['bulan_tahun']);

    // Periksa apakah kategori anggaran sudah ada untuk user yang sedang sesi
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $check_stmt = $conn->prepare("SELECT kategori_id FROM anggaran WHERE kategori_id = ? AND bulan = ? AND tahun = ? AND user_id = ?");
    $check_stmt->bind_param("ssss", $kategori_id, $bulan, $tahun, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $existing_data = $check_result->fetch_assoc();


    if ($existing_data) {
        header("Location: set_budget.php?pesan=Anggaran sudah ada!");
        exit();
    }

    $insert_query = $conn->prepare("INSERT INTO anggaran (jumlah_anggaran, total_anggaran, bulan, tahun, kategori_id, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param("iissss", $jumlah_anggaran, $total_anggaran, $bulan, $tahun, $kategori_id, $user_id);

    if ($insert_query->execute()) {
        header("Location: user_page.php?pesan=Anggaran berhasil dibuat!&bulan=$bulan&tahun=$tahun");
        exit();
    } else {
        echo "Error: " . $insert_query->error;
        exit();
    }
    } else {
        echo "Invalid request!";
        exit();
    }
        
?>