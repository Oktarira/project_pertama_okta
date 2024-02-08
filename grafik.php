<?php
    // Include koneksi php
    @include 'koneksi.php';

    $totalPengeluaran = 0;
    $totalSisaAnggaran = 0;

    session_start();

    // Cek status login
    if (!isset($_SESSION['user_id'])) {
       
        header('location: login.php');
        exit();
    }

    // menghitung total pengeluran per kategori
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

    // pesan
    $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';

    // Ambil bulan dan tahun 
    $selectedMonth = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
    $selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

    // Ambil data anggaran per tanggal
    $categories = $conn->query("SELECT anggaran.*, kategori_anggaran.nama_kategori 
                                FROM `anggaran` 
                                LEFT JOIN `kategori_anggaran` ON anggaran.kategori_id = kategori_anggaran.id_kategori
                                WHERE  anggaran.user_id = '{$_SESSION['user_id']}'
                                AND anggaran.bulan = '$selectedMonth' AND anggaran.tahun = '$selectedYear'");

    // Ambil total_anggaran
    $totalAnggaran = $conn->query("SELECT SUM(total_anggaran) AS total_anggaran
    FROM `anggaran` 
    WHERE anggaran.user_id = '{$_SESSION['user_id']}'
    AND bulan = '$selectedMonth' AND tahun = '$selectedYear'");

    // Fetch data total anggaran
    $totalAnggaranData = $totalAnggaran->fetch_assoc();
    $totalAnggaran = $totalAnggaranData['total_anggaran'];

    // ambil data bulan-bulan sebelumnya
    $bulanSebelumnya = $conn->query("SELECT DISTINCT bulan, tahun FROM `anggaran`
                            WHERE anggaran.user_id = '{$_SESSION['user_id']}'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YooBudget Charts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">

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

        form {
            margin-top: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
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
            
            <b> <a href="user_page.php">Dasbor</a>
            <a href="set_budget.php">Atur Anggaran</a>
            <a href="set_expense.php">Catat Pengeluaran</a>
            <a href="reports.php">Laporan</a>
            <a href="grafik.php">Grafik</a>
            <a href="javascript:void(0);" onclick="confirmLogout()">Keluar</a></b>
        </div>
        <div class="content">
            <h1>Grafik & Laporan Keuangan Bulanan</h1>

            <!-- Tampilkan pesan  -->
            <?php
                if (!empty($pesan)) {
                    echo "<div class='alert alert-success' role='alert'>$pesan</div>";
                }
            ?>

            <!-- Form untuk memilih bulan dan tahun -->
           <form action="" method="GET">
              <b><label for="bulan">Pilih Bulan:</label></b>  
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

                <b><label for="tahun">Pilih Tahun:</label></b>
                <select name="tahun" id="tahun">
                    <?php
                        // opsi tahun
                        $currentYear = date('Y');
                        for ($i = $currentYear; $i <= 2040; $i++) {
                            $selected = ($i == $selectedYear) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                    ?>
                </select>

                <button type="submit">Tampilkan</button>
            </form>

            <!-- grafik -->
            <div style="width:80%; margin:auto;">
                <canvas id="myPieChart"></canvas>
            </div>

            <!-- Box untuk Laporan Anggaran -->
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
                                    // total anggaran per kategori
                                    $totalAnggaranKategori = $row['total_anggaran'];

                                    //  total pengeluaran per kategori
                                    $totalPengeluaranKategori = getPengeluaranByKategori($row['kategori_id'], $selectedMonth, $selectedYear);

                                    // sisa anggaran per kategori
                                    $sisaAnggaranKategori = $totalAnggaranKategori - $totalPengeluaranKategori;

                                    echo number_format($sisaAnggaranKategori);

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
        </div>
    </div>


    <!-- kode untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('myPieChart').getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Total sisa anggaran', 'Total digunakan'],
                    datasets: [{
                        data: [<?php echo $totalSisaAnggaran; ?>, <?php echo $totalPengeluaran; ?>],
                        backgroundColor: ['#36a2eb', '#ff6384']
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'bottom'
                    },
                    aspectRatio : 3
                }
            });
        });

        function toggleNavbar() {
            var navbar = document.getElementById('navbar');
            navbar.style.marginLeft = navbar.style.marginLeft === '0px' ? '-250px' : '0px';
        }
    </script>
</body>
</html>
