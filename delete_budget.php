<?php
    include 'koneksi.php';

    session_start();

    //Isi variabel
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
        $kategori_id = $_GET['id'];
        $selectedMonth = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
        $selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

    
        $conn->begin_transaction();

        try {
            // Hapus data pengeluaran 
            $delete_pengeluaran_stmt = $conn->prepare("DELETE FROM pengeluaran WHERE kategori_pengeluaran = ? AND MONTH(tanggal_pengeluaran) = ? AND YEAR(tanggal_pengeluaran) = ? AND user_id = ? ");
            $delete_pengeluaran_stmt->bind_param("isss", $kategori_id, $selectedMonth, $selectedYear, $_SESSION['user_id']);
            $delete_pengeluaran_stmt->execute();

            // Hapus data anggaran
            $delete_anggaran_stmt = $conn->prepare("DELETE FROM anggaran WHERE kategori_id = ? AND bulan = ? AND tahun = ? AND user_id = ? ");
            $delete_anggaran_stmt->bind_param("isss", $kategori_id, $selectedMonth, $selectedYear, $_SESSION['user_id'] );
            $delete_anggaran_stmt->execute();

            // Commit transaksi
            $conn->commit();

            // Redirect kembali ke user_page.php
            header("Location: user_page.php?pesan=Berhasil menghapus Anggaran!&bulan=$selectedMonth&tahun=$selectedYear");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();

            // Redirect kembali ke user_page.php dengan pesan error
            header("Location: user_page.php?pesan=Error deleting budget: " . $e->getMessage());
            exit();
        }
    } else {
        // Redirect kembali ke user_page.php 
        header("Location: user_page.php?pesan=Invalid request");
        exit();
    }
?>
