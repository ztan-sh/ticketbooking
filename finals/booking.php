<?php
$conn = new mysqli('localhost', 'root', '', 'finals');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName    = $_POST['fullName'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $concert     = $_POST['concert'];
    $concertDate = $_POST['concertDate'];
    $tickets     = $_POST['tickets'];
    $seatType    = $_POST['seatType'];
    $extras      = isset($_POST['extras']) ? implode(',', $_POST['extras']) : '';

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO bookings (fullName, email, phone, concert, concertDate, tickets, seatType, extras)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiss", $fullName, $email, $phone, $concert, $concertDate, $tickets, $seatType, $extras);

    if ($stmt->execute()) {
        $lastId = $stmt->insert_id;
        echo "
        <html>
        <head>
            <link rel='stylesheet' href='style.css'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>
                body {
                    background-color: #0d1117;
                    color: white;
                    font-family: 'Poppins', sans-serif;
                    text-align: center;
                }
                .swal2-popup {
                    background: #0d1b2a !important;
                    color: #e0e0e0 !important;
                    border: 1px solid #1e90ff !important;
                    box-shadow: 0 0 15px rgba(30, 144, 255, 0.4) !important;
                    border-radius: 15px !important;
                }
                .swal2-title {
                    color: #58a6ff !important;
                }
                .swal2-confirm {
                    background-color: #1e90ff !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 8px !important;
                }
                .swal2-deny {
                    background-color: #007acc !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 8px !important;
                }
                .swal2-cancel {
                    background-color: #1b263b !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 8px !important;
                }
            </style>
        </head>
        <body>
          <script>
            Swal.fire({
              title: '🎫 Confirmation',
              text: 'Confirm booking?',
              icon: 'success',
              showDenyButton: true,
              showCancelButton: true,
              confirmButtonText: 'Confirm Booking',
              denyButtonText: '✏️ Update Booking',
              cancelButtonText: 'Cancel Booking',
              confirmButtonColor: '#1e90ff',
              denyButtonColor: '#007acc',
              cancelButtonColor: '#1b263b',
              background: '#0d1b2a',
              color: '#e0e0e0'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = 'booking.html';
              } else if (result.isDenied) {
                window.location.href = 'update.php?id=$lastId';
              } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                  title: 'Booking Cancelled',
                  text: 'You have been redirected to the booking page.',
                  icon: 'info',
                  confirmButtonText: 'OK',
                  background: '#0d1b2a',
                  color: '#e0e0e0',
                  confirmButtonColor: '#1e90ff'
                }).then(() => {
                  window.location.href = 'booking.html';
                });
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
            <style>
                body {
                    background-color: #0d1117;
                    color: white;
                    font-family: 'Poppins', sans-serif;
                    text-align: center;
                }
                .swal2-popup {
                    background: #0d1b2a !important;
                    color: #e0e0e0 !important;
                    border: 1px solid #1e90ff !important;
                    box-shadow: 0 0 15px rgba(30, 144, 255, 0.4) !important;
                    border-radius: 15px !important;
                }
                .swal2-title {
                    color: #58a6ff !important;
                }
                .swal2-confirm {
                    background-color: #1e90ff !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 8px !important;
                }
            </style>
        </head>
        <body>
          <script>
            Swal.fire({
              title: '❌ Error!',
              text: '" . addslashes($stmt->error) . "',
              icon: 'error',
              confirmButtonText: 'Try Again',
              background: '#0d1b2a',
              color: '#e0e0e0',
              confirmButtonColor: '#1e90ff'
            }).then(() => {
              window.history.back();
            });
          </script>
        </body>
        </html>
        ";
    }

    $stmt->close();
}

$conn->close();
?>
