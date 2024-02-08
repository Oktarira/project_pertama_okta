<?php
    include 'koneksi.php';
    session_start();


    // Cek sesi
    if (!isset($_SESSION['user_id'])) {
        header('location: login.php');
        exit();
    }

    $pesan = ''; 



        // Periksa apakah formulir disubmit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil nilai dari formulir
            $jumlah_pengeluaran = $_POST['jumlah_pengeluaran'];
            $tanggal_pengeluaran = $_POST['tanggal_pengeluaran'];
            $kategori_pengeluaran = $_POST['kategori_pengeluaran'];

        //bersihkan input user
        $jumlah_pengeluaran = preg_replace("/[^0-9]/", "", $jumlah_pengeluaran);

        // Periksa apakah sisa anggaran mencukupi
        $cek_sisa_anggaran_stmt = $conn->prepare("SELECT id_anggaran, jumlah_anggaran FROM anggaran WHERE user_id = ? AND kategori_id = ? AND bulan = ? AND tahun = ?");
        $user_id = $_SESSION['user_id']; 
        $bulan_pengeluaran = date('m', strtotime($tanggal_pengeluaran));
        $tahun_pengeluaran = date('Y', strtotime($tanggal_pengeluaran));

        $cek_sisa_anggaran_stmt->bind_param("ssss", $user_id, $kategori_pengeluaran, $bulan_pengeluaran, $tahun_pengeluaran);
        
        $cek_sisa_anggaran_stmt->execute();
        $cek_sisa_anggaran_result = $cek_sisa_anggaran_stmt->get_result();

        if ($cek_sisa_anggaran_result) {
            $data_sisa_anggaran = $cek_sisa_anggaran_result->fetch_assoc();

            if ($data_sisa_anggaran) {
                // Kategori anggaran telah diatur
                $sisa_anggaran = $data_sisa_anggaran['jumlah_anggaran'];

                if ($jumlah_pengeluaran > $sisa_anggaran) {
                    // Sisa anggaran tidak mencukupi
                    $pesan = "Kesalahan: Sisa anggaran tidak mencukupi!";
                } else {
                    // Mulai transaksi
                    $conn->begin_transaction();

                    try {
                        // Simpan data pengeluaran ke dalam tabel pengeluaran
                        $insert_pengeluaran_stmt = $conn->prepare("INSERT INTO pengeluaran (jumlah_pengeluaran, tanggal_pengeluaran, kategori_pengeluaran, user_id) VALUES (?, ?, ?, ?)");
                        $insert_pengeluaran_stmt->bind_param("isss", $jumlah_pengeluaran, $tanggal_pengeluaran, $kategori_pengeluaran, $user_id);
                        $insert_pengeluaran_stmt->execute();


                        // Update sisa anggaran pada tabel anggaran
                        $sisa_anggaran -= $jumlah_pengeluaran;
                        $update_anggaran_stmt = $conn->prepare("UPDATE anggaran SET jumlah_anggaran = ? WHERE id_anggaran = ?");
                        $update_anggaran_stmt->bind_param("si", $sisa_anggaran, $data_sisa_anggaran['id_anggaran']);
                        $update_anggaran_stmt->execute();

                        // Commit transaksi
                        $conn->commit();

                        // Berikan pesan sukses
                        $pesan = "Data pengeluaran berhasil disimpan!";

                        // kembali ke user_page.php setelah 2 
                        header("Location: user_page.php?bulan=$bulan_pengeluaran&tahun=$tahun_pengeluaran&pesan=$pesan");
                        exit();
                    } catch (Exception $e) {
                        // Rollback jika terjadi kesalahan
                        $conn->rollback();
                        $pesan = "Error: " . $e->getMessage();
                    }
                }
            } else {
                // Handle ketika kategori anggaran belum diatur
                $pesan = "Kesalahan: Anggaran belum dibuat!";
            }
        } else {
            // Handle kesalahan query jika diperlukan
            $pesan = "Error in query: " . $conn->error;
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Catat Pengeluaran</title>
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
   </style>
   <link rel="icon" href="/IDK/img/log.png" type="image/png">
</head>
<body>
   <div class="container">
      <h1>Catat Pengeluaran</h1>
      
      <?php
      if (!empty($pesan)) {
         echo "<div class='alert alert-success' role='alert'>$pesan</div>";
      }
      ?>
      <form action="" method="POST">
        
        <b> <div class="mb-3">
    <label for="jumlah_pengeluaran" class="form-label">Jumlah Pengeluaran:</label>
    <div class="input-group">
        <input type="text"  name=" jumlah_pengeluaran" oninput="formatCurrency(this)" required>
    </div>
    </div>
    <div class="mb-3">
        <label for="tanggal_pengeluaran" class="form-label">Tanggal Pengeluaran:</label>
        <input type="date" class="form-control" name="tanggal_pengeluaran" required>
    </div>
    <div class="mb-3">
        <label for="kategori_pengeluaran" class="form-label">Kategori Pengeluaran:</label>
        <select class="form-select" name="kategori_pengeluaran" required>
            <?php
            // Ambil daftar kategori dari tabel kategori_anggaran
            $kategori_result = $conn->query("SELECT id_kategori, nama_kategori FROM kategori_anggaran");

            while ($kategori = $kategori_result->fetch_assoc()) {
                echo "<option value=\"{$kategori['id_kategori']}\">{$kategori['nama_kategori']}</option>";
            }
            ?>
    </select>
         </div>
         
         <button type="submit" class="btn btn-primary" onclick="return simpanData()">Simpan</button>
         <a class="btn btn-danger float-right" href="user_page.php" onclick="return confirm('Apakah Anda yakin ingin membatalkan pencatatan pengeluaran?')">Batal</a>
         </b>
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
