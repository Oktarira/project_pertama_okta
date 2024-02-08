<?php
    include 'koneksi.php';

    session_start();

    // Cek apakah pengguna sudah login
    if (!isset($_SESSION['user_id'])) {
        header('location: login.php');
        exit();
    }

    //isi variabel
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['bulan']) && isset($_GET['tahun'])) {
        $kategori_id = $_GET['id'];
        $selectedMonth = $_GET['bulan'];
        $selectedYear = $_GET['tahun'];

        // Ambil data anggaran dari database 
        $statement = $conn->prepare("SELECT anggaran.*, kategori_anggaran.nama_kategori 
                                FROM anggaran 
                                LEFT JOIN kategori_anggaran ON anggaran.kategori_id = kategori_anggaran.id_kategori 
                                WHERE anggaran.kategori_id = ? 
                                AND anggaran.bulan = ? 
                                AND anggaran.tahun = ? 
                                AND anggaran.user_id = ?");

        // Bind parameter untuk memastikan hanya data yang sesuai dengan user_id yang sedang login yang diambil
        $statement->bind_param("ssss", $kategori_id, $selectedMonth, $selectedYear, $_SESSION['user_id']);
        $statement->execute();

        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nama_anggaran = isset($row['nama_kategori']) ? $row['nama_kategori'] : 'New Budget'; // Menyimpan nama anggaran
        } else {
            echo "Kategori tidak ditemukan!";
            exit();
        }

        $statement->close();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_budget'])) {
        $new_budget = $_POST['new_budget'];
        $total_anggaran = $_POST['total_anggaran'];

        // Menghapus semua karakter selain angka
        $new_budget = preg_replace("/[^0-9]/", "", $new_budget);
        $total_anggaran = preg_replace("/[^0-9]/", "", $total_anggaran);

        // perubahan data anggaran di basis data
        $kategori_id = $_GET['id'];
        $bulan_anggaran_baru = $_POST['bulan_anggaran'];
        $tahun_anggaran_baru = $_POST['tahun_anggaran'];

        // Validasi apakah anggaran untuk bulan dan tahun tersebut sudah ada atau belum
        $statement = $conn->prepare("SELECT * FROM anggaran WHERE kategori_id = ? AND bulan = ? AND tahun = ? AND user_id = ?");
        $statement->bind_param("ssss", $kategori_id, $bulan_anggaran_baru, $tahun_anggaran_baru, $_SESSION['user_id']);
        $statement->execute();
        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            // Anggaran sudah ada, lakukan update
            $conn->query("UPDATE anggaran SET jumlah_anggaran = '$new_budget', total_anggaran = '$total_anggaran' 
                        WHERE kategori_id = '$kategori_id' 
                        AND bulan = '$bulan_anggaran_baru' 
                        AND tahun = '$tahun_anggaran_baru' 
                        AND user_id = '{$_SESSION['user_id']}'");

            // Redirect kembali ke home page
            header("Location: user_page.php?pesan=Anggaran berhasil diedit!&bulan=$bulan_anggaran_baru&tahun=$tahun_anggaran_baru");
            exit();
        } else {
            // Anggaran belum ada, tampilkan pesan kesalahan
            $error_message = "Anggaran belum dibuat di bulan yang Anda pilih!";
        }

        $statement->close();
    }
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Budget</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

            h2 {
                text-align: center; 
            }

            h1 {
                text-align: center;
                margin-top: 50px;
            }

            form {
                max-width: 400px;
                margin: 0 auto;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                margin-top: 20px;
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

            .error-msg {
                color: red;
                margin-bottom: 10px;
                text-align: center;
            }
        </style>
        <!-- logo tab -->
        <link rel="icon" href="/IDK/img/log.png" type="image/png">
    </head>
    <body>
        <h1>Edit Anggaran</h1>
        <h2><?php echo isset($nama_anggaran) ? $nama_anggaran : 'New Budget'; ?></h2>
        
        <?php if (isset($error_message)): ?>
            <p class="error-msg"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Formulir pengeditan -->
            <b><label for="new_budget">Jumlah Anggaran Baru:</label>
            <div class="input-group">                
                <input type="text"  name="new_budget" oninput="formatCurrency(this)" value="<?php echo isset($row['jumlah_anggaran']) ? $row['jumlah_anggaran'] : ''; ?>" required></b> 

            <div class="mb-3">
                <label for="total_anggaran"><b>Verifikasi Jumlah Anggaran Baru: </b></br><i>(Isi dengan nilai yang sama seperti di atas)</i></label>
                <div class="input-group">       
                <input type="text"  name="total_anggaran" oninput="formatCurrency(this)" value="<?php echo isset($row['total_anggaran']) ? $row['total_anggaran'] : ''; ?>" required>
            </div>

            <!--  input untuk bulan dan tahun -->
            <b><label for="bulan_anggaran">Bulan:</label></b>
            <input type="number" name="bulan_anggaran" value="<?php echo isset($row['bulan']) ? $row['bulan'] : ''; ?>" required>

            <b><label for="tahun_anggaran">Tahun:</label></b>
            <input type="number" name="tahun_anggaran" value="<?php echo isset($row['tahun']) ? $row['tahun'] : ''; ?>" required>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a class="btn btn-danger float-right" href="user_page.php" onclick="return confirm('Apakah Anda yakin ingin membatalkan proses ini?')">Batal</a>
        </form>
        
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
