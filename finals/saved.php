<?php
$conn = new mysqli('localhost', 'root', '', 'finals');
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

// ---- Handle Delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    header('Content-Type: application/json');
    echo json_encode(['success' => $ok]);
    exit;
}

// ---- Handle Update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $concert = $_POST['concert'];
    $concertDate = $_POST['concertDate'];
    $tickets = $_POST['tickets'];
    $seatType = $_POST['seatType'];
    $extras = $_POST['extras'];

    $stmt = $conn->prepare("UPDATE bookings 
        SET fullName=?, email=?, phone=?, concert=?, concertDate=?, tickets=?, seatType=?, extras=? 
        WHERE id=?");
    $stmt->bind_param("ssssssssi", $fullName, $email, $phone, $concert, $concertDate, $tickets, $seatType, $extras, $id);
    $ok = $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode(['success' => $ok, 'error' => $stmt->error]);
    exit;
}

// ---- Fetch data ----
$result = $conn->query("SELECT * FROM bookings");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Saved Bookings</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { 
      font-family: Arial; 
      background: #0a0f1f; 
      color: white; 
      text-align: center;
    }
    table { 
      border-collapse: collapse; 
      width: 90%; 
      margin: 30px auto; 
      background: #142850; 
      border-radius: 10px; 
      overflow: hidden;
    }
    th, td { 
      padding: 12px; 
      border: 1px solid #4ea8ff; 
      text-align: center; 
    }
    th { 
      background: #1e3a8a; 
      color: #b3c4ff; 
    }
    tr:hover { background: #1f3c88; }

    .return-btn, .delete-btn, .edit-btn {
      display: inline-block;
      margin: 5px;
      padding: 8px 15px;
      background-color: #142850;
      color: #fff;
      border: 1px solid #4ea8ff;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
      cursor: pointer;
    }
    .return-btn:hover { background-color: #1e3a8a; color: #b3c4ff; }
    .delete-btn { background-color: #2c2f48; border-color: #ff4e4e; }
    .delete-btn:hover { background-color: #ff4e4e; color: #fff; }
    .edit-btn { background-color: #2c2f48; border-color: #4eff7a; }
    .edit-btn:hover { background-color: #4eff7a; color: #000; }
  </style>
</head>
<body>
  <h1 style="color:#4ea8ff;">🎫 Saved Bookings</h1>
  
  <table id="bookings-table">
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Concert</th>
      <th>Date</th>
      <th>Tickets</th>
      <th>Seat</th>
      <th>Extras</th>
      <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): 
      // Fix date format for date input
      $dateValue = '';
      if (!empty($row['concertDate'])) {
          $timestamp = strtotime($row['concertDate']);
          $dateValue = $timestamp ? date('Y-m-d', $timestamp) : $row['concertDate'];
      }
    ?>
    <tr id="row-<?= (int)$row['id'] ?>">
      <td><?= (int)$row['id'] ?></td>
      <td><?= htmlspecialchars($row['fullName']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['phone']) ?></td>
      <td><?= htmlspecialchars($row['concert']) ?></td>
      <td><?= htmlspecialchars($dateValue) ?></td>
      <td><?= htmlspecialchars($row['tickets']) ?></td>
      <td><?= htmlspecialchars($row['seatType']) ?></td>
      <td><?= htmlspecialchars($row['extras']) ?></td>
      <td>
        <button class="edit-btn" 
          data-id="<?= (int)$row['id'] ?>"
          data-fullname="<?= htmlspecialchars(json_encode($row['fullName']), ENT_QUOTES) ?>"
          data-email="<?= htmlspecialchars(json_encode($row['email']), ENT_QUOTES) ?>"
          data-phone="<?= htmlspecialchars(json_encode($row['phone']), ENT_QUOTES) ?>"
          data-concert="<?= htmlspecialchars(json_encode($row['concert']), ENT_QUOTES) ?>"
          data-concertdate="<?= htmlspecialchars($dateValue) ?>"
          data-tickets="<?= htmlspecialchars($row['tickets']) ?>"
          data-seattype="<?= htmlspecialchars(json_encode($row['seatType']), ENT_QUOTES) ?>"
          data-extras="<?= htmlspecialchars(json_encode($row['extras']), ENT_QUOTES) ?>"
        >✏ Edit</button>
        <button class="delete-btn" data-id="<?= (int)$row['id'] ?>">🗑 Delete</button>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <a href="booking.html" class="return-btn">⬅ Return to Booking Page</a>

<script>
document.addEventListener('click', async (e) => {
  // DELETE booking
  if (e.target.matches('.delete-btn')) {
    const id = e.target.dataset.id;
    Swal.fire({
      title: 'Delete booking?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
      background: '#0f1a2f',
      color: '#fff'
    }).then(res => {
      if (res.isConfirmed) {
        fetch('saved.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: 'delete=' + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(j => {
          if (j.success) {
            document.getElementById('row-' + id)?.remove();
            Swal.fire('Deleted!', 'Booking removed.', 'success');
          } else Swal.fire('Error', j.error || 'Failed to delete.', 'error');
        });
      }
    });
  }

  // EDIT booking
  if (e.target.matches('.edit-btn')) {
    const d = e.target.dataset;

    // Decode JSON-safe strings
    const decode = str => {
      try { return JSON.parse(str); } catch { return str; }
    };

    const { value: formValues } = await Swal.fire({
      title: 'Edit Booking',
      html: `
        <input id="swal-name" class="swal2-input" placeholder="Full Name" value="${decode(d.fullname) || ''}">
        <input id="swal-email" class="swal2-input" placeholder="Email" value="${decode(d.email) || ''}">
        <input id="swal-phone" class="swal2-input" placeholder="Phone" value="${decode(d.phone) || ''}">
        <input id="swal-concert" class="swal2-input" placeholder="Concert" value="${decode(d.concert) || ''}">
        <input id="swal-date" class="swal2-input" type="date" value="${d.concertdate || ''}">
        <input id="swal-tickets" class="swal2-input" type="number" min="1" value="${d.tickets || ''}">
        <input id="swal-seat" class="swal2-input" placeholder="Seat Type" value="${decode(d.seattype) || ''}">
        <input id="swal-extras" class="swal2-input" placeholder="Extras" value="${decode(d.extras) || ''}">
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Save Changes',
      background: '#0f1a2f',
      color: '#fff',
      preConfirm: () => ({
        update_id: d.id,
        fullName: document.getElementById('swal-name').value,
        email: document.getElementById('swal-email').value,
        phone: document.getElementById('swal-phone').value,
        concert: document.getElementById('swal-concert').value,
        concertDate: document.getElementById('swal-date').value,
        tickets: document.getElementById('swal-tickets').value,
        seatType: document.getElementById('swal-seat').value,
        extras: document.getElementById('swal-extras').value
      })
    });

    if (formValues) {
      const data = new URLSearchParams(formValues).toString();
      fetch('saved.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: data
      })
      .then(r => r.json())
      .then(j => {
        if (j.success) {
          Swal.fire('Updated!', 'Booking updated successfully.', 'success');
          const row = document.getElementById('row-' + formValues.update_id);
          if (row) {
            row.cells[1].textContent = formValues.fullName;
            row.cells[2].textContent = formValues.email;
            row.cells[3].textContent = formValues.phone;
            row.cells[4].textContent = formValues.concert;
            row.cells[5].textContent = formValues.concertDate;
            row.cells[6].textContent = formValues.tickets;
            row.cells[7].textContent = formValues.seatType;
            row.cells[8].textContent = formValues.extras;
          }
        } else {
          Swal.fire('Error', j.error || 'Failed to update booking.', 'error');
        }
      });
    }
  }
});
</script>
</body>
</html>
