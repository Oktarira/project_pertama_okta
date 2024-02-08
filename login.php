<?php
    include 'koneksi.php';
    session_start();

    //simpan data ke variabel
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(mysqli_real_escape_string($conn, $_POST['email']), FILTER_VALIDATE_EMAIL); 
    $password = $_POST['password'];

    $query = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Periksa kata sandi 
        if (password_verify($password, $row['password'])) {
            // Simpan user_id ke dalam sesi
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];

            // Arahkan ke halaman user_page.php
            header('location: user_page.php');
            exit(); 
        } else {
            $error = 'Kata sandi  tidak sesuai!';
        }
    } else {
        $error = 'Email tidak sesuai / belum terdaftar !';
    }

    if (!$email) {
        $error = 'Email tidak valid!';
    }
    }

    // Tampilkan pesan registrasi
    if (isset($_SESSION['registration_message'])) {
    echo '<p style="margin-bottom: 490px;
    position: absolute;
    
    left: 50%;
    transform: translateX(-50%); 
    color: green; ">' . $_SESSION['registration_message'] . '</p>';
    unset($_SESSION['registration_message']); // Hapus pesan registrasi dari sesi agar tidak ditampilkan lagi
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #eee;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: crimson;
        }

        .error-msg {
            color: crimson;
            margin-bottom: 480px;
        }

        p {
        margin-top: 15px; 
        position: absolute;
        bottom: 10px; 
        left: 50%;
        transform: translateX(-50%); 
        }
    </style>
    <!-- logo di tab -->
    <link rel="icon" href="/IDK/img/log.png" type="image/png">
</head>
<body>
    
    <form method="post" action="">
        <h2>Masuk</h2>

        <?php
        if (isset($error)) {
            echo '<p class="error-msg">' . $error . '</p>';
        }
        ?>

       <label for="email">Email:</label> 
        <input type="email" name="email" required>

        <label for="password">Kata sandi:</label>
        <input type="password" name="password" required>

        <button type="submit">Masuk</button>
    </form>
    <p >Belum memiliki akun? <a href="/IDK/register.php">Daftar sekarang</a></p>

</body>
</html>
