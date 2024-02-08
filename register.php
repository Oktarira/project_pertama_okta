<?php
    
    include 'koneksi.php';
    
    //Mulai sesi
    session_start();

    //inisialisasi
    $errorMessage = '';

    //aksi setelah submit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Pengecekan apakah email sudah terdaftar
    $checkEmailQuery = "SELECT * FROM user_form WHERE email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0) {
        // Email sudah terdaftar, pesan eeror
        $errorMessage = "Email sudah terdaftar!";
    } else {
        // Email belum terdaftar, lanjut
        $query = "INSERT INTO user_form (name, email, password) VALUES ('$name', '$email', '$password')";

        //berhasil registrasi atau tidak berhasil
            if (mysqli_query($conn, $query)) {
            $_SESSION['registration_message'] = "Akun Anda berhasil dibuat, silahkan masuk!";
           
                header('Location: /IDK/login.php');
                exit(); 
            } else {
                $errorMessage = "Error: " . $query . "<br>" . mysqli_error($conn);
            }
        }
    }   

    mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        <h2>Daftar</h2>
        <?php if (!empty($errorMessage)): ?>
            <div style="color: red;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <label for="name">Nama:</label>
        <input type="text" name="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Kata sandi:</label>
        <input type="password" name="password" required>

        <button type="submit">Daftar</button>
    </form>
    <p >Sudah memiliki akun? <a href="/IDK/login.php">Masuk</a></p>
</body>
</html>