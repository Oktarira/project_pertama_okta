<?php
   @include 'koneksi.php';
   $totalPengeluaran = 0;
   $totalSisaAnggaran = 0;

   session_start();

      // Cek sesi
      if (!isset($_SESSION['user_id'])) {
         
         header('location: login.php');
         exit();
      }



      function getPengeluaranByKategori($kategoriId, $bulan, $tahun) {
         global $conn;
         $result = $conn->query("SELECT SUM(jumlah_pengeluaran) AS total_pengeluaran
                                 FROM `pengeluaran` 
                                 WHERE pengeluaran.user_id = '{$_SESSION['user_id']}'
                                    AND kategori_pengeluaran = '$kategoriId' 
                                    AND MONTH(tanggal_pengeluaran) = '$bulan' 
                                    AND YEAR(tanggal_pengeluaran) = '$tahun'");
         $data = $result->fetch_assoc();
         return $data['total_pengeluaran'] ?: 0;
      }

      $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';

      // Ambil bulan dan tahun dari parameter GET 
      $selectedMonth = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
      $selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

      // mengambil data anggaran dengan filter bulan dan tahun
      $categories = $conn->query("SELECT anggaran.*, kategori_anggaran.nama_kategori 
                            FROM `anggaran` 
                            LEFT JOIN `kategori_anggaran` ON anggaran.kategori_id = kategori_anggaran.id_kategori
                            WHERE anggaran.user_id = '{$_SESSION['user_id']}'
                            AND anggaran.bulan = '$selectedMonth' AND anggaran.tahun = '$selectedYear'");

      /// mengambil total anggaran
      $totalAnggaran = $conn->query("SELECT SUM(total_anggaran) AS total_anggaran
      FROM `anggaran` 
      WHERE anggaran.user_id = '{$_SESSION['user_id']}' 
      AND bulan = '$selectedMonth' AND tahun = '$selectedYear' ");

      // Fetch data total anggaran
      $totalAnggaranData = $totalAnggaran->fetch_assoc();
      $totalAnggaran = $totalAnggaranData['total_anggaran'];

      // mengambil data laporan bulan-bulan sebelumnya
      $bulanSebelumnya = $conn->query("SELECT DISTINCT bulan, tahun FROM `anggaran`
                                       WHERE anggaran.user_id = '{$_SESSION['user_id']}'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Laporan Keuangan</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
   function confirmLogout() {
      var result = confirm("Apakah Anda yakin ingin keluar?");
      if (result) {
         window.location.href = "/IDK/logout.php";
      }
   }
</script>

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

      .dashboard {
         display: flex;
         height: 500vh;
      }

      .navbar {
         width: 250px;
         background: #333;
         color: #fff;
         transition: 0.3s;
         padding: 20px; 
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

      .content {
         flex: 1;
         padding: 20px;
         transition: margin-left 0.3s;
      }

      .menu-toggle {
         font-size: 24px;
         cursor: pointer;
      }

      .row {
         margin-top: 20px;
      }

      .info-box {
         background: #fff;
         border: 1px solid #ddd;
         border-radius: 5px;
         padding: 15px;
         margin-bottom: 20px;
      }

      .callout {
         background: #fff;
         border: 1px solid #ddd;
         border-radius: 5px;
         padding: 15px;
         margin-bottom: 20px;
      }

      .callout h5 {
         margin: 0;
      }

      .callout .float-right {
         margin-left: auto;
      }

      .callout .d-flex {
         justify-content: flex-end;
      }

      .text-center {
         text-align: center;
      }

      #noData {
         display: none;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px;
      }

      table, th, td {
         border: 1px solid #ddd;
      }

      th, td {
         padding: 10px;
         text-align: left;
      }

      th {
         background-color: #f2f2f2;
      }

      tfoot {
         font-weight: bold;
      }
      .logo {
      display: flex;
      align-items: center;
         }
         .logo img {
            width: 50px;
            height: auto;
            margin-right: 10px;
         }

         .logo-text {
            display: flex;
            flex-direction: column;
            align-items: flex-end; 
            color: #ccc;
         }

   </style>
   <link rel="icon" href="/IDK/img/log.png" type="image/png">
</head>
<body>
   <div class="dashboard">
         <div class="navbar" id="navbar">
         <div class="logo">
      <img src="/IDK/img/log.png" alt="YooBudget Logo">
         <div class="logo-text">
            <span class="logo-main">YooBudget</span>
            
         </div>
            
   </div>
            <b><a href="user_page.php">Dasbor</a>
            <a href="set_budget.php">Atur Anggaran</a>
            <a href="set_expense.php">Catat Pengeluaran</a>
            <a href="reports.php">Laporan</a>
            <a href="grafik.php">Grafik</a>
            <a href="javascript:void(0);" onclick="confirmLogout()">Keluar</a></b>
         </div>
         <div class="content">
         <b>  <h1>Laporan Keuangan Bulanan</h1>

            <!-- Tampilkan pesan  -->
            <?php
               if (!empty($pesan)) {
                  echo "<div class='alert alert-success' role='alert'>$pesan</div>";
               }
            ?>

            <!-- Form  memilih bulan dan tahun -->
            <form action="" method="GET">
               <label for="bulan">Pilih Bulan:</label>
               <select name="bulan" id="bulan">
                  <?php
                     // Daftar opsi bulan
                     for ($i = 1; $i <= 12; $i++) {
                        $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $selected = ($month == $selectedMonth) ? 'selected' : '';
                        echo "<option value='$month' $selected>$month</option>";
                     }
                  ?>
               </select>

               <label for="tahun">Pilih Tahun :</label>
               <select name="tahun" id="tahun">
                  <?php
                     // Daftar opsi tahun 
                     $currentYear = date('Y');
                     for ($i = $currentYear; $i <= 2040; $i++) {
                        $selected = ($i == $selectedYear) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                     }
                  ?>
               </select>

               <button type="submit">Tampilkan</button>
            </form>
            </b>
      

            <!-- Box Laporan Anggaran -->
            <h4>Laporan Anggaran dan Pengeluaran</h4>
            <hr>

            <?php if ($categories->num_rows > 0): ?>
      <table>
         <thead>
               <tr>
                  <th>Kategori</th>
                  <th>Anggaran</th>
                  <th>Pengeluaran</th>
                  <th>Sisa Anggaran</th>
               </tr>
         </thead>
         <tbody>
               <?php while ($row = $categories->fetch_assoc()): ?>
                  <tr>
                     <td><?php echo $row['nama_kategori']; ?></td>
                     <td>Rp<?php echo number_format($row['total_anggaran']); ?></td>
                     <td>Rp<?php echo number_format(getPengeluaranByKategori($row['kategori_id'], $selectedMonth, $selectedYear)); ?></td>
                     <td>
                           Rp<?php
                           // Ambil total anggaran per kategori
                           $totalAnggaranKategori = $row['total_anggaran'];

                           // Ambil total pengeluaran per kategori
                           $totalPengeluaranKategori = getPengeluaranByKategori($row['kategori_id'], $selectedMonth, $selectedYear);

                           // Hitung sisa anggaran per kategori
                           $sisaAnggaranKategori = $totalAnggaranKategori - $totalPengeluaranKategori;

                           echo  number_format  ($sisaAnggaranKategori);

                           // Akumulasikan total pengeluaran dan sisa anggaran
                           $totalPengeluaran += $totalPengeluaranKategori;
                           $totalSisaAnggaran += $sisaAnggaranKategori;
                           ?>
                     </td>
                  </tr>
               <?php endwhile; ?>
         </tbody>
         <tfoot>
               <tr>
                  <th>Total</th>
                  <th>Rp<?php echo number_format($totalAnggaran); ?></th>
                  <th>Rp<?php echo number_format($totalPengeluaran); ?></th>
                  <th>Rp<?php echo number_format($totalSisaAnggaran); ?></th>
               </tr>
         </tfoot>
      </table>
   <?php else: ?>
      <div class="col-md-12">
         <h3 class="text-center" id="noData">No Data to display.</h3>
      </div>
   <?php endif; ?>

      <script>
         function toggleNavbar() {
            var x = document.getElementById("navbar");
            if (x.className === "navbar") {
               x.className += " responsive";
            } else {
               x.className = "navbar";
            }
         }
      </script>
  
</body>
</html>
