<?php
$conn = new mysqli('localhost', 'root', '', 'finals');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get booking ID
if (!isset($_GET['id'])) {
    die("No booking ID provided.");
}
$id = intval($_GET['id']);

// Fetch booking
$result = $conn->query("SELECT * FROM bookings WHERE id='$id'");
if ($result->num_rows == 0) {
    die("Booking not found.");
}
$row = $result->fetch_assoc();

$fullName    = $row['fullName'];
$email       = $row['email'];
$phone       = $row['phone'];
$concert     = $row['concert'];
$concertDate = $row['concertDate'];
$tickets     = $row['tickets'];
$seatType    = $row['seatType'];
$extrasArr   = explode(',', $row['extras'] ?? '');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName    = $_POST['fullName'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $concert     = $_POST['concert'];
    $concertDate = $_POST['concertDate'];
    $tickets     = $_POST['tickets'];
    $seatType    = $_POST['seatType'];
    $extras      = isset($_POST['extras']) ? implode(',', $_POST['extras']) : '';

    $stmt = $conn->prepare("UPDATE bookings SET fullName=?, email=?, phone=?, concert=?, concertDate=?, tickets=?, seatType=?, extras=? WHERE id=?");
    $stmt->bind_param("sssssissi", $fullName, $email, $phone, $concert, $concertDate, $tickets, $seatType, $extras, $id);

    if ($stmt->execute()) {
    echo "
    <html>
    <head>
        <link rel='stylesheet' href='booking.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
      <script>
        Swal.fire({
          title: '✅ Booking Updated!',
          text: 'What would you like to do next?',
          icon: 'success',
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Go to Homepage',
          denyButtonText: 'Update Again',
          cancelButtonText: 'Delete Booking',
          confirmButtonColor: '#3085d6',  // blue
          denyButtonColor: '#17a2b8',     // teal/secondary
          cancelButtonColor: '#d33',      // red
          background: '#1e1e1e',          // dark card background
          color: '#fff',                  // white text
          iconColor: '#3085d6'            // blue icon for consistency
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'booking.html';
          } else if (result.isDenied) {
            window.location.href = 'update.php?id=$id';
          } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = 'delete.php?id=$id';
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
            <link rel='stylesheet' href='booking.css'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
          <script>
            Swal.fire({
              title: '❌ Error!',
              text: '" . addslashes($stmt->error) . "',
              icon: 'error',
              confirmButtonColor: '#d33',
              background: '#1e1e1e',
              color: '#fff',
              confirmButtonText: 'Try Again'
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
    <h1>Update Your Booking</h1>
    <form action="update.php?id=<?php echo $id; ?>" method="post">
        <label for="fullName">Full Name:</label>
        <input type="text" name="fullName" id="fullName" value="<?php echo htmlspecialchars($fullName); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" required><br><br>

        <label for="concert">Select Concert:</label>
        <select name="concert" id="concert" required>
            <option value="">--Select--</option>
            <option value="bini" <?php echo ($concert=='bini')?'selected':''; ?>>Bini Live Concert</option>
            <option value="coldplay" <?php echo ($concert=='coldplay')?'selected':''; ?>>Coldplay Live Concert</option>
            <option value="eminem" <?php echo ($concert=='eminem')?'selected':''; ?>>Eminem Live Concert</option>
            <option value="parokya" <?php echo ($concert=='parokya')?'selected':''; ?>>Parokya Ni Edgar Live Concert</option>
            <option value="maroon5" <?php echo ($concert=='maroon5')?'selected':''; ?>>Maroon 5 Live Concert</option>
            <option value="adele" <?php echo ($concert=='adele')?'selected':''; ?>>Adele Live Concert</option>
            <option value="taylor" <?php echo ($concert=='taylor')?'selected':''; ?>>Taylor Swift Live Concert</option>
        </select><br><br>

        <label for="concertDate">Select Date:</label>
        <input type="date" name="concertDate" id="concertDate" value="<?php echo $concertDate; ?>" required><br><br>

        <label for="tickets">Number of Tickets:</label>
        <input type="number" name="tickets" id="tickets" value="<?php echo $tickets; ?>" min="1" max="100" required><br><br>

        <p>Seat Type:</p>
        <input type="radio" id="vip" name="seatType" value="VIP" <?php echo ($seatType=='VIP')?'checked':''; ?> required>
        <label for="vip">VIP</label><br>
        <input type="radio" id="regular" name="seatType" value="Regular" <?php echo ($seatType=='Regular')?'checked':''; ?> required>
        <label for="regular">Regular</label><br><br>

        <p>Extras:</p>
        <input type="checkbox" id="parking" name="extras[]" value="Parking" <?php echo in_array('Parking', $extrasArr)?'checked':''; ?>>
        <label for="parking">Parking</label><br>
        <input type="checkbox" id="merch" name="extras[]" value="Merchandise" <?php echo in_array('Merchandise', $extrasArr)?'checked':''; ?>>
        <label for="merch">Merchandise</label><br><br>

        <button type="submit">Update Booking</button>
    </form>
</body>
</html>
