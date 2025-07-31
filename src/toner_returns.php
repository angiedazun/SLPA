<?php
require 'session.php';
require '../config/db.php';

// Insert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    try {
        $stmt = $pdo->prepare("INSERT INTO toner_returns (TonerModel, Supplier, NoOfUnits, TenderFileNo, ReturnDate, ReceivingDate, ReceivingQty, Remarks)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['model'], $_POST['supplier'], $_POST['units'],
            $_POST['tender'], $_POST['returndate'], $_POST['recvdate'],
            $_POST['recvqty'], $_POST['remarks']
        ]);
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM toner_returns WHERE ID = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: toner_returns.php");
    exit;
}

// Get Toner Models for dropdown
$models = $pdo->query("SELECT TonerModelNo FROM toner_master")->fetchAll(PDO::FETCH_ASSOC);

// Fetch toner returns
$returns = $pdo->query("SELECT * FROM toner_returns ORDER BY ID DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Toner Returns</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary">Toner Returns</h4>
    <div>
      <a href="dashboard.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
      </a>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Add Return
      </button>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif ?>

  <div class="table-responsive">
    <table class="table table-bordered table-hover table-sm">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Model</th>
          <th>Supplier</th>
          <th>Units</th>
          <th>Tender File</th>
          <th>Return Date</th>
          <th>Receiving Date</th>
          <th>Receiving Qty</th>
          <th>Remarks</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($returns as $row): ?>
        <tr>
          <td><?= $row['ID'] ?? '' ?></td>
          <td><?= htmlspecialchars($row['TonerModel']) ?></td>
          <td><?= htmlspecialchars($row['Supplier']) ?></td>
          <td><?= $row['NoOfUnits'] ?></td>
          <td><?= htmlspecialchars($row['TenderFileNo']) ?></td>
          <td><?= $row['ReturnDate'] ?></td>
          <td><?= $row['ReceivingDate'] ?></td>
          <td><?= $row['ReceivingQty'] ?></td>
          <td><?= htmlspecialchars($row['Remarks']) ?></td>
          <td>
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
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="add" />
      <div class="modal-header bg-light">
        <h5 class="modal-title">Add Toner Return</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Toner Model</label>
          <select name="model" class="form-control" required>
            <option value="">-- Select Model --</option>
            <?php foreach ($models as $m): ?>
              <option value="<?= $m['TonerModelNo'] ?>"><?= $m['TonerModelNo'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Supplier</label><input name="supplier" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">No. of Units</label><input name="units" type="number" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Tender File No</label><input name="tender" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Return Date</label><input name="returndate" type="date" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Receiving Date</label><input name="recvdate" type="date" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Receiving Qty</label><input name="recvqty" type="number" class="form-control" required></div>
        <div class="col-md-12"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
