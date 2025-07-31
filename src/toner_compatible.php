<?php
require 'session.php';
require '../config/db.php';

// Add record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO Toner_Compatible (TonerModelNo, PrinterModel) VALUES (?, ?)");
    $stmt->execute([$_POST['toner_model'], $_POST['printer_model']]);
}

// Edit record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE Toner_Compatible SET PrinterModel = ? WHERE TonerModelNo = ? AND PrinterModel = ?");
    $stmt->execute([$_POST['new_printer_model'], $_POST['toner_model'], $_POST['original_printer_model']]);
}

// Delete record
if (isset($_GET['delete_model']) && isset($_GET['delete_printer'])) {
    $stmt = $pdo->prepare("DELETE FROM Toner_Compatible WHERE TonerModelNo = ? AND PrinterModel = ?");
    $stmt->execute([$_GET['delete_model'], $_GET['delete_printer']]);
    header("Location: toner_compatible.php");
    exit;
}

// Fetch
$compatibles = $pdo->query("SELECT * FROM Toner_Compatible ORDER BY TonerModelNo, PrinterModel")->fetchAll(PDO::FETCH_ASSOC);
$models = $pdo->query("SELECT TonerModelNo FROM Toner_Master")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Toner Compatible</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../public/css/toner_compatible.css">
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h4 class="text-primary">Toner Compatible Models</h4>
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
    <table class="table table-bordered table-hover table-sm">
      <thead class="table-light">
        <tr>
          <th>Toner Model No</th>
          <th>Compatible Printer Model</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($compatibles as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['TonerModelNo']) ?></td>
          <td><?= htmlspecialchars($row['PrinterModel']) ?></td>
          <td>
            <button class="btn btn-sm btn-outline-secondary me-1"
              data-bs-toggle="modal" data-bs-target="#editModal"
              data-toner="<?= $row['TonerModelNo'] ?>"
              data-printer="<?= $row['PrinterModel'] ?>">
              <i class="bi bi-pencil-fill"></i>
            </button>
            <a href="?delete_model=<?= $row['TonerModelNo'] ?>&delete_printer=<?= urlencode($row['PrinterModel']) ?>"
              class="btn btn-sm btn-outline-danger"
              onclick="return confirm('Delete this compatibility?');">
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
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="add" />
      <div class="modal-header bg-lightblue">
        <h5 class="modal-title">Add Compatible Printer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-12">
          <label class="form-label">Toner Model No</label>
          <select name="toner_model" class="form-control" required>
            <option value="">-- Select Toner Model --</option>
            <?php foreach ($models as $model): ?>
              <option value="<?= $model ?>"><?= $model ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Printer Model</label>
          <input type="text" name="printer_model" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="toner_model" id="edit-toner-model" />
      <input type="hidden" name="original_printer_model" id="edit-original-printer-model" />
      <div class="modal-header bg-lightblue">
        <h5 class="modal-title">Edit Compatible Printer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-12">
          <label class="form-label">Printer Model</label>
          <input type="text" name="new_printer_model" id="edit-printer-model" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  const tonerModel = button.getAttribute('data-toner');
  const printerModel = button.getAttribute('data-printer');

  document.getElementById('edit-toner-model').value = tonerModel;
  document.getElementById('edit-original-printer-model').value = printerModel;
  document.getElementById('edit-printer-model').value = printerModel;
});
</script>
</body>
</html>
