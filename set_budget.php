<?php

    include 'koneksi.php';  
    session_start();

    // cek sesi
    if (!isset($_SESSION['user_id'])) {
        header('location: login.php');
    
        exit();
    }

    // Inisialisasi pesan
    $pesan = '';

    // Periksa submit formulir
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['jumlah_anggaran'])) {
        $jumlah_anggaran = $_POST['jumlah_anggaran'];
        $total_anggaran = $_POST['total_anggaran'];

    
        // Validasi input dan ubah database
        $kategori_id = $_POST['kategori_id'];
        $bulan_anggaran_baru = $_POST['bulan_tahun'];
        list($tahun_anggaran_baru, $bulan_anggaran_baru) = explode('-', $bulan_anggaran_baru);

         // Update data anggaran hanya jika sesuai dengan bulan dan tahun yang baru
        
        $stmt = $conn->prepare("UPDATE anggaran SET jumlah_anggaran = ?, total_anggaran = ? 
        WHERE user_id = ? AND kategori_id = ? AND bulan = ? AND tahun = ?");

        // Bind parameters
        $stmt->bind_param("iisiss", $jumlah_anggaran, $total_anggaran, $_SESSION['user_id'], $kategori_id, $bulan_anggaran_baru, $tahun_anggaran_baru);

        // Execute statement
        $stmt->execute();

        // Close statement
        $stmt->close();

    // Redirect kembali ke user_page.php setelah mengedit
    header("Location: user_page.php?pesan=Budget edited successfully");
    exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Atur Anggaran</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
   
   <style>
      body {
         font-family: 'Open Sans', sans-serif;
         color: #444444;
         margin: 0;
         padding: 0;
         box-sizing: border-box;
         outline: none;
         border: none;
         text-decoration: none;
         background-color: #eee; 
      }

      .container {
         max-width: 600px;
         margin-top: 50px;
      }
      .navbar {
         width: 250px;
         background: #333;
         color: #fff;
         transition: 0.3s;
      }

      .navbar a {
         padding: 15px;
         display: block;
         text-decoration: none;
         color: #fff;
         transition: 0.3s;
      }

      .navbar a:hover {
         background: crimson;
      }
   </style>
   <link rel="icon" href="/IDK/img/log.png" type="image/png">
</head>
<body>

    <?php
    if (isset($_GET['pesan'])) {
        echo '<div class="alert alert-warning" role="alert">' . $_GET['pesan'] . '</div>';
    }
    ?>
    
    <!-- form untuk isi deskripsi anggaran -->
    <div class="container">
        <h1>Atur Anggaran</h1>
        <form action="proses_set_kategori.php" method="POST">
        <div class="mb-3">
        <b><label for="jumlah_anggaran" class="form-label">Jumlah Anggaran:</label></b>
        <div class="input-group">
            <input type="text" name="jumlah_anggaran" oninput="formatCurrency(this)" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="total_anggaran"><b>Verifikasi Jumlah Anggaran: </b></br> <i>(Isi dengan nilai yang sama seperti di atas)</i></label></br>
        <div class="input-group">
            
            <input type="text" name="total_anggaran" oninput="formatCurrency(this)" required>
        </div>
    </div>
            <div class="mb-3">
                <b><label for="bulan_tahun" class="form-label">Periode Anggaran (Bulan dan Tahun):</label></b>
                <input type="month" class="form-control" name="bulan_tahun" required>
            </div>
            <div class="mb-3">
                <b><label for="kategori_anggaran" class="form-label">Kategori Anggaran:</label></b>
                <select class="form-select" name="kategori_id" required>
                    <?php
                    include 'koneksi.php';

                    // Ambil daftar kategori dari tabel kategori_anggaran
                    $kategori_result = $conn->query("SELECT id_kategori, nama_kategori FROM kategori_anggaran");

                    while ($kategori = $kategori_result->fetch_assoc()) {
                        echo "<option value=\"{$kategori['id_kategori']}\">{$kategori['nama_kategori']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a class="btn btn-danger float-right" href="user_page.php"
            onclick="return confirm('Apakah Anda yakin ingin membatalkan pengaturan anggaran?')">Batal</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-e3I3cEmh7C2y9Sn9LlG6L91bGBIHpJPeZzl/dRfzs/Rw8U1NhAwG1zIdd4DnmYcK" crossorigin="anonymous"></script>

    <script>
        function formatCurrency(input) {
            // Menghapus semua karakter selain angka
            var value = input.value.replace(/[^0-9]/g, '');
        
            // Menambahkan titik sebagai pemisah ribuan
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
            // Mengatur nilai input dengan format yang diinginkan
            input.value = 'Rp ' + value;
        }
    </script>

        
</body>
</html>
