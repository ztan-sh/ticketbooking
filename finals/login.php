<?php

$conn = mysqli_connect("localhost","root","","finals");

if (!$conn){
    die("Connection failed: " . mysqli_connect_error());
}


$username = $_POST['username'];
$pass = $_POST['pass'];


$sql = "SELECT * FROM users WHERE username = '$username' AND pass ='$pass'";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) > 0){

    header("Location: booking.html");
    exit();

}else{
     echo "<script>
            alert('Invalid username or password');
            window.location.href = 'login.html';
          </script>";

}
 mysqli_close($conn);

 ?>