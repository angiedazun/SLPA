<?php
require 'session.php';
require '../config/db.php';

// Add Toner Issuing Record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO Toner_Issuing (TonerModel, PrinterModel, Division, Section, PRNo, RequestOfficer, ReceiverName, ReceiverEmpNo, Remarks, Date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['tonermodel'], $_POST['printermodel'], $_POST['division'], $_POST['section'], $_POST['prno'],
        $_POST['requestofficer'], $_POST['receivername'], $_POST['receiverempno'], $_POST['remarks'], $_POST['date']
    ]);
}

// Edit Toner Issuing Record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE Toner_Issuing SET TonerModel=?, PrinterModel=?, Division=?, Section=?, PRNo=?, RequestOfficer=?, ReceiverName=?, ReceiverEmpNo=?, Remarks=?, Date=? WHERE ID=?");
    $stmt->execute([
        $_POST['tonermodel'], $_POST['printermodel'], $_POST['division'], $_POST['section'], $_POST['prno'],
        $_POST['requestofficer'], $_POST['receivername'], $_POST['receiverempno'], $_POST['remarks'], $_POST['date'], $_POST['id']
    ]);
}

// Delete Toner Issuing Record
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM Toner_Issuing WHERE ID=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: toner_issuing.php");
    exit;
}

// Fetch all issuing records
$records = $pdo->query("SELECT * FROM Toner_Issuing ORDER BY Date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch toner models from Toner_Master for dropdown
$tonerModels = $pdo->query("SELECT TonerModelNo FROM Toner_Master ORDER BY TonerModelNo")->fetchAll(PDO::FETCH_COLUMN);

// Fetch printer models from Printer_History for dropdown
$printerModels = $pdo->query("SELECT DISTINCT PrinterModel FROM Printer_History ORDER BY PrinterModel")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Toner Issuing</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<div class="container mt-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="text-primary mb-2">Toner Issuing</h4>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
      </a>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Add New
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Toner Model</th>
          <th>Printer Model</th>
          <th>Division</th>
          <th>Section</th>
          <th>PR No</th>
          <th>Request Officer</th>
          <th>Receiver Name</th>
          <th>Receiver Emp No</th>
          <th>Remarks</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($records as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['TonerModel']) ?></td>
          <td><?= htmlspecialchars($row['PrinterModel']) ?></td>
          <td><?= htmlspecialchars($row['Division']) ?></td>
          <td><?= htmlspecialchars($row['Section']) ?></td>
          <td><?= htmlspecialchars($row['PRNo']) ?></td>
          <td><?= htmlspecialchars($row['RequestOfficer']) ?></td>
          <td><?= htmlspecialchars($row['ReceiverName']) ?></td>
          <td><?= htmlspecialchars($row['ReceiverEmpNo']) ?></td>
          <td><?= htmlspecialchars($row['Remarks']) ?></td>
          <td><?= $row['Date'] ?></td>
          <td>
            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editModal"
              data-id="<?= $row['ID'] ?>"
              data-tonermodel="<?= htmlspecialchars($row['TonerModel']) ?>"
              data-printermodel="<?= htmlspecialchars($row['PrinterModel']) ?>"
              data-division="<?= htmlspecialchars($row['Division']) ?>"
              data-section="<?= htmlspecialchars($row['Section']) ?>"
              data-prno="<?= htmlspecialchars($row['PRNo']) ?>"
              data-requestofficer="<?= htmlspecialchars($row['RequestOfficer']) ?>"
              data-receivername="<?= htmlspecialchars($row['ReceiverName']) ?>"
              data-receiverempno="<?= htmlspecialchars($row['ReceiverEmpNo']) ?>"
              data-remarks="<?= htmlspecialchars($row['Remarks']) ?>"
              data-date="<?= $row['Date'] ?>"
              >
              <i class="bi bi-pencil-fill"></i>
            </button>
            <a href="?delete=<?= $row['ID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?');">
              <i class="bi bi-trash-fill"></i>
            </a>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="add" />
      <div class="modal-header bg-lightblue">
        <h5 class="modal-title">Add Toner Issuing Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Toner Model</label>
          <select name="tonermodel" class="form-select" required>
            <option value="">Select Toner Model</option>
            <?php foreach ($tonerModels as $model): ?>
              <option value="<?= $model ?>"><?= $model ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Printer Model</label>
          <select name="printermodel" class="form-select" required>
            <option value="">Select Printer Model</option>
            <?php foreach ($printerModels as $model): ?>
              <option value="<?= $model ?>"><?= $model ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Division</label><input name="division" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Section</label><input name="section" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">PR No</label><input name="prno" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Request Officer</label><input name="requestofficer" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Receiver Name</label><input name="receivername" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Receiver Emp No</label><input name="receiverempno" class="form-control"></div>
        <div class="col-md-12"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control" rows="2"></textarea></div>
        <div class="col-md-12"><label class="form-label">Date</label><input name="date" type="date" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="id" id="edit-id" />
      <div class="modal-header bg-lightblue">
        <h5 class="modal-title">Edit Toner Issuing Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Toner Model</label><input name="tonermodel" id="edit-tonermodel" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Printer Model</label><input name="printermodel" id="edit-printermodel" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Division</label><input name="division" id="edit-division" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Section</label><input name="section" id="edit-section" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">PR No</label><input name="prno" id="edit-prno" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Request Officer</label><input name="requestofficer" id="edit-requestofficer" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Receiver Name</label><input name="receivername" id="edit-receivername" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Receiver Emp No</label><input name="receiverempno" id="edit-receiverempno" class="form-control"></div>
        <div class="col-md-12"><label class="form-label">Remarks</label><textarea name="remarks" id="edit-remarks" class="form-control" rows="2"></textarea></div>
        <div class="col-md-12"><label class="form-label">Date</label><input name="date" id="edit-date" type="date" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const editModal = document.getElementById('editModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('edit-id').value = btn.dataset.id;
    document.getElementById('edit-tonermodel').value = btn.dataset.tonermodel;
    document.getElementById('edit-printermodel').value = btn.dataset.printermodel;
    document.getElementById('edit-division').value = btn.dataset.division;
    document.getElementById('edit-section').value = btn.dataset.section;
    document.getElementById('edit-prno').value = btn.dataset.prno;
    document.getElementById('edit-requestofficer').value = btn.dataset.requestofficer;
    document.getElementById('edit-receivername').value = btn.dataset.receivername;
    document.getElementById('edit-receiverempno').value = btn.dataset.receiverempno;
    document.getElementById('edit-remarks').value = btn.dataset.remarks;
    document.getElementById('edit-date').value = btn.dataset.date;
  });
</script>

</body>
</html>
