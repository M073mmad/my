<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
if (!isset($_SESSION['access_token'])) {
    header('Location: auth.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>الأجانب</title>
  <style>
    body {
      background: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .dashboard {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    h1 {
      margin-bottom: 20px;
      color: #333;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      color: white;
      background: #007bff;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h1>الأجانب</h1>
  <a href="imgagn.php" class="btn">صور الأجانب</a>
  <a href="videosagn.php" class="btn">فيديوهات الأجانب</a>
  <a href="agn.html" class="btn">صفحات الأجانب</a>
  <a href="dashboard.php" class="btn" style="background-color: #33E010;">العودة</a>
</div>

</body>
</html>
