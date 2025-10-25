<?php
$conn = new mysqli ('localhost', 'root','','finals');
if ($conn->connect_error) {
die("Connection failed: ".$conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$username = $_POST['username'];
$pass = $_POST['pass'];
$conpass = $_POST['conpass'];

if ($pass !== $conpass) {
    echo "<script>alert('❌ Passwords do not match!'); window.history.back();</script>";
    exit();
}
$sql = "INSERT INTO users (firstName, lastName, username, pass, conpass) VALUES ('$firstName', '$lastName', '$username', '$pass', '$conpass')";

if ($conn->query($sql) === TRUE) {
  
    echo "
    <html>
    <head>
      <!-- SweetAlert2 CSS + JS -->
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
      <script>
        Swal.fire({
          title: 'Success!',
          text: 'Account created successfully!',
          icon: 'success',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'Continue'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'login.html';
          }
        });
      </script>
    </body>
    </html>
    ";
} else {
  
    echo "
    <html>
    <head>
      <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
      <script>
        Swal.fire({
          title: 'Error!',
          text: '" . addslashes($conn->error) . "',
          icon: 'error',
          confirmButtonColor: '#d33',
          confirmButtonText: 'Try Again'
        });
      </script>
    </body>
    </html>
    ";
}

$conn->close();
}
?>