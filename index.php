<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>YooBudget</title>
  <!-- START CSS -->
      <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');
      footer {
         background: #eee;
         padding: 200px 0;
         text-align: center;
            }

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

      header {
         background: #333;
         padding: 15px 0;
      }

      nav {
         display: flex;
         align-items: center;
         justify-content: space-between;
         max-width: 1200px;
         margin: 0 auto;
      }

      .logo {
         display: flex; 
         align-items: center; 
         color: #fff;
         font-size: 24px;
         font-weight: bold;
      }

      .logo img {
         width: 50px; 
         margin-right: 10px; 
      }

      .logo span {
         background: crimson;
         color: #fff;
         border-radius: 5px;
         padding: 0 10px;
      }

      ul {
         list-style: none;
         display: flex;
      }

      ul li {
         margin-right: 20px;
      }

      ul li a {
         color: #fff;
         text-decoration: none;
         font-size: 18px;
         font-weight: 500;
      }

      .register-btn a {
         background: crimson;
         color: #fff;
         padding: 10px 20px;
         border-radius: 5px;
         text-decoration: none;
         font-weight: bold;
         font-size: 16px;
      }

      .main-content {
         background: #eee;
         padding: 50px 0;
      }

      .container {
         display: flex;
         align-items: center;
         justify-content: center;
         padding : above 500px ;
      }

      .content {
         text-align: center;
      }

      h1 {
         font-size: 36px;
         color: #333;
      }

      p {
         font-size: 20px;
         margin-bottom: 20px;
      }

      .btn {
         display: inline-block;
         padding: 10px 20px;
         font-size: 20px;
         background: #333;
         color: #fff;
         margin: 0 5px;
         text-transform: capitalize;
         text-decoration: none;
         border-radius: 5px;
      }

      .btn:hover {
         background: crimson;
      }

      </style>
  <!-- END OF CSS -->
  <link rel="icon" href="/IDK/img/log.png" type="image/png">
</head>
<body>
   <header>
      <nav>
         <div class="logo">
         <img src="/IDK/img/log.png" alt="Logo">
            <span>YooBudget</span>
         </div>
        
         <div class="register-btn">
            <a href="/IDK/login.php">Login</a>
         </div>
      </nav>
   </header>
   <section class="main-content">
      <div class="container">
         <div class="content">
            <h1>Selamat Datang di YooBudget</h1>
            <p>Rencanakan Anggaran Bulanan Dengan Mudah</p>
            <a href="/IDK/login.php" class="btn">Mulai atur anggaran!</a>
         </div>
      </div>
   </section>
   <footer>
    <div class="container" >
       <p>&copy; 2023 YooBudget | Made by Team 1</p>
    </div>
 </footer>
</body>
</html>
