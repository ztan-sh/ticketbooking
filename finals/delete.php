<?php
$conn = new mysqli('localhost', 'root', '', 'finals');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    die("No booking ID provided.");
}

$id = intval($_GET['id']);

// Delete booking
$stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "
    <html>
    <head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
    <body style='background-color:#111;'>
      <script>
        Swal.fire({
          title: 'Booking Deleted!',
          text: 'Your booking has been permanently removed.',
          icon: 'success',
          confirmButtonText: 'HOME',
          confirmButtonColor: '#1f6feb',
          background: '#1e1e1e',
          color: '#fff'
        }).then(() => {
          window.location.href = 'booking.html'; // Redirect after delete
        });
      </script>
    </body>
    </html>
    ";
} else {
    echo "
    <html>
    <head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head>
    <body style='background-color:#111;'>
      <script>
        Swal.fire({
          title: 'Error!',
          text: 'Unable to delete booking. Please try again later.',
          icon: 'error',
          confirmButtonColor: '#d33',
          background: '#1e1e1e',
          color: '#fff'
        }).then(() => {
          window.history.back();
        });
      </script>
    </body>
    </html>
    ";
}

$stmt->close();
$conn->close();
?>
