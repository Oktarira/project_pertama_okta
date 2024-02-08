<?php
   @include 'koneksi.php';
   
   //inisiasi variabel 
   $cur_bul = 0;
   
   session_start();

   // cek sesi
   if (!isset($_SESSION['user_id'])) {
      header('location: login.php');
      exit();
   }

   //ambil data bulan tahun
   $selectedMonth = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
   $selectedYear = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

   //ambil jumlah anggaran sesuai bulan
   $cur_bul_stmt = $conn->prepare("SELECT SUM(jumlah_anggaran) AS total FROM `anggaran` WHERE bulan = ? AND tahun = ? AND user_id = ?");
   $cur_bul_stmt->bind_param("sss", $selectedMonth, $selectedYear, $_SESSION['user_id']);
   $cur_bul_stmt->execute();
   $cur_bul_result = $cur_bul_stmt->get_result();
   
   // Periksa apakah query berhasil dijalankan
   if ($cur_bul_result) {
      // Ambil nilai total
      $cur_bul_data = $cur_bul_result->fetch_assoc();
      $cur_bul = $cur_bul_data['total'];
   } else {
      // Handle kesalahan query jika diperlukan
      echo "Error in query: " . $conn->error;
   } 

   $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';

   // Query untuk mengambil data anggaran dengan filter bulan dan tahun
  $categories_stmt = $conn->prepare("SELECT anggaran.*, kategori_anggaran.nama_kategori 
                              FROM `anggaran` 
                              LEFT JOIN `kategori_anggaran` ON anggaran.kategori_id = kategori_anggaran.id_kategori
                              WHERE anggaran.bulan = ? AND anggaran.tahun = ? AND anggaran.user_id = ?");
   $categories_stmt->bind_param("sss", $selectedMonth, $selectedYear, $_SESSION['user_id']);
   $categories_stmt->execute();
   $categories = $categories_stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YooBudget Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
   <!-- script untuk konfirmasi logout -->
   <script>
   function confirmLogout() {
      var result = confirm("Apakah Anda yakin ingin keluar?");
      if (result) {
         window.location.href = "/IDK/logout.php";
      }
   }
   </script>

   <!-- CSS start -->
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

            

               .logo-main {
                  font-size: 1.2em;
                  font-weight: bold;
               }

               .logo-sub {
                  font-size: 0.8em;
                  color: #ccc;
               }

               .navbar b a {
                  padding: 15px;
                  display: block;
                  text-decoration: none;
                  color: #fff;
                  transition: 0.3s;
               }

               .navbar b a:hover {
                  background: crimson;
               }

               .content {
                  flex: 1;
                  padding: 20px;
                  transition: margin-left 0.3s;
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
         
      
         <b><a href="/IDK/user_page.php">Dasbor</a>
         <a href="/IDK/set_budget.php">Atur Anggaran</a>
         <a href="/IDK/set_expense.php">Catat Pengeluaran</a>
         <a href="/IDK/reports.php">Laporan</a>
         <a href="/IDK/grafik.php">Grafik</a>
         <a href="javascript:void(0);" onclick="confirmLogout()">Keluar</a></b>
      </div>
      <div class="content">
         <h1>Selamat datang di YooBudget!</h1>

         <!-- Tampilkan pesan  -->
         <?php
            if (!empty($pesan)) {
               echo "<div class='alert alert-success' role='alert'>$pesan</div>";
            }
         ?>



         <!-- Info Box untuk anggaran per kategori-->
         <b> <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
               <div class="info-box">
                  <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-money-bill-alt"></i></span>
                  <div class="info-box-content">
                     <span class="info-box-text">Total Anggaran Bulan Ini : </span>
                     <span class="info-box-number text-right">
                       <b>Rp<?php echo number_format($cur_bul); ?></b>
                     </span>
                  </div>
               </div>
            </div>
         </div>
         </b>

        
         <!-- Form untuk memilih bulan dan tahun -->
<form action="user_page.php" method="GET">
   <b><label for="bulan">Pilih Bulan:</label>
   <select name="bulan" id="bulan">
      <?php
         for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $selected = ($month == $selectedMonth) ? 'selected' : '';
            echo "<option value='$month' $selected>$month</option>";
         }
      ?>
  </b> </select> 

            <b><label for="tahun">Pilih Tahun:</label>
   <select name="tahun" id="tahun">
      <?php
         $currentYear = date('Y');
         for ($i = $currentYear; $i <= 2040; $i++) {
            $selected = ($i == $selectedYear) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
         }
      ?>
   </select></b>

            <button type="submit">Tampilkan</button>
         </form>

         <!-- Box anggaran per kategori -->
         <h4>Jumlah Anggaran per Kategori</h4>
         <hr>

         <div class="row row-cols-4 row-cols-sm-1 row-cols-md-4 row-cols-lg-4">
            <?php 
               while($row = $categories->fetch_assoc()):
            ?>
            <div class="col p-2 cat-items">
               <div class="callout callout-info">
                  <span class="float-right ml-1">
                     <!-- Tombol Edit -->
                     <a href="edit_budget.php?id=<?php echo $row['kategori_id']; ?>&bulan=<?php echo $selectedMonth; ?>&tahun=<?php echo $selectedYear; ?>" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                     <!-- Tombol Delete -->
                     <a href="delete_budget.php?id=<?php echo $row['kategori_id']; ?>&bulan=<?php echo $row['bulan']; ?>&tahun=<?php echo $row['tahun']; ?>" data-toggle="tooltip" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus anggaran ini?')"><i class="fas fa-trash-alt"></i></a>

                  </span>
                  <h5 class="mr-4"><b><?php echo $row['nama_kategori'] ?></b></h5>
                  <p class="mb-2">Bulan: <?php echo $row['bulan']; ?>, Tahun: <?php echo $row['tahun']; ?></p>
                  <div class="d-flex justify-content-end">
                     <b>Rp<?php echo number_format($row['jumlah_anggaran']) ?></b>
                  </div>
               </div>
            </div>
            <?php endwhile; ?>
         </div>

         <div class="col-md-12">
            <h3 class="text-center" id="noData">No Data to display.</h3>
         </div>
      </div>
   </div>

   <script>
      function check_cats(){
         if($('.cat-items:visible').length > 0){
            $('#noData').hide('slow')
         } else {
            $('#noData').show('slow')
         }

         // Menghitung jumlah kategori yang visible dan mengatur tinggi .dashboard
         var visibleCategories = $('.cat-items:visible').length;
         var newHeight = visibleCategories * 50 + 200; 
         $('.dashboard').css('height', newHeight + 'vh');
      }

      $(function(){
         $('[data-toggle="tooltip"]').tooltip({
            html:true
         })

         check_cats()

         $('#search').on('input',function(){
            var _f = $(this).val().toLowerCase()
            $('.cat-items').each(function(){
               var _c = $(this).text().toLowerCase()
               if(_c.includes(_f) == true)
                  $(this).toggle(true);
               else
                  $(this).toggle(false);
            })

            check_cats()
         })
      });

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
