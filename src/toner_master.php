<?php
require 'session.php';
require '../config/db.php';

// Create
$addError = '';
$showToast = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stock = (int)$_POST['stock'];
    $jct = (int)$_POST['jct'];
    $uct = (int)$_POST['uct'];

    if ($jct + $uct > $stock) {
        $addError = "JCT + UCT cannot exceed total stock!";
        $showToast = true;
    } else {
        $check = $pdo->prepare("SELECT COUNT(*) FROM Toner_Master WHERE TonerModelNo = ?");
        $check->execute([$_POST['model']]);
        if ($check->fetchColumn() > 0) {
            $addError = "Duplicate Model No, can't add!";
            $showToast = true;
        } else {
            $stmt = $pdo->prepare("INSERT INTO Toner_Master (TonerModelNo, Type, RequestOrderLevel, StockInHand, JCTStock, UCTStock, PurchasingDate)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['model'], $_POST['type'], $_POST['reorder'],
                $stock, $jct, $uct, $_POST['date']
            ]);
        }
    }
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stock = (int)$_POST['stock'];
    $jct = (int)$_POST['jct'];
    $uct = (int)$_POST['uct'];

    if ($jct + $uct > $stock) {
        $addError = "JCT + UCT cannot exceed total stock!";
        $showToast = true;
    } else {
        $stmt = $pdo->prepare("UPDATE Toner_Master SET Type=?, RequestOrderLevel=?, StockInHand=?, JCTStock=?, UCTStock=?, PurchasingDate=? WHERE TonerModelNo=?");
        $stmt->execute([
            $_POST['type'], $_POST['reorder'], $stock,
            $jct, $uct, $_POST['date'], $_POST['model']
        ]);
    }
}

// Delete with check for dependencies
if (isset($_GET['delete'])) {
    $modelToDelete = $_GET['delete'];

    // Check if toner model is referenced in Toner_Issuing table
    $check = $pdo->prepare("SELECT COUNT(*) FROM Toner_Issuing WHERE TonerModel = ?");
    $check->execute([$modelToDelete]);
    if ($check->fetchColumn() > 0) {
        // Toner model is referenced, cannot delete
        echo "<script>
            alert('Cannot delete this toner model because it is referenced in issuing records.');
            window.location='toner_master.php';
        </script>";
        exit;
    }

    // Safe to delete
    $stmt = $pdo->prepare("DELETE FROM Toner_Master WHERE TonerModelNo = ?");
    $stmt->execute([$modelToDelete]);
    header("Location: toner_master.php");
    exit;
}

// Fetch records
$toners = $pdo->query("SELECT * FROM Toner_Master ORDER BY TonerModelNo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Toner Master</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php if ($showToast): ?>
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999; width: 100%; display: flex; justify-content: center;">
  <div id="duplicateToast" class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 320px; max-width: 90vw;">
    <div class="d-flex">
      <div class="toast-body text-center w-100">
        <?= htmlspecialchars($addError) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
  window.onload = function() {
    var toastEl = document.getElementById('duplicateToast');
    if (toastEl) {
      var toast = new bootstrap.Toast(toastEl);
      toast.show();
    }
  };
</script>
<?php endif; ?>

<div class="container mt-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="text-primary mb-2">Toner Master</h4>
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
          <th>Model No</th>
          <th>Type</th>
          <th>Reorder</th>
          <th>Stock</th>
          <th>JCT</th>
          <th>UCT</th>
          <th>Purchase Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($toners as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['TonerModelNo']) ?></td>
          <td><?= htmlspecialchars($row['Type']) ?></td>
          <td><?= htmlspecialchars($row['RequestOrderLevel']) ?></td>
          <td><?= htmlspecialchars($row['StockInHand']) ?></td>
          <td><?= htmlspecialchars($row['JCTStock']) ?></td>
          <td><?= htmlspecialchars($row['UCTStock']) ?></td>
          <td><?= htmlspecialchars($row['PurchasingDate']) ?></td>
          <td>
            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editModal"
              data-model="<?= htmlspecialchars($row['TonerModelNo']) ?>" data-type="<?= htmlspecialchars($row['Type']) ?>" data-reorder="<?= htmlspecialchars($row['RequestOrderLevel']) ?>"
              data-stock="<?= htmlspecialchars($row['StockInHand']) ?>" data-jct="<?= htmlspecialchars($row['JCTStock']) ?>" data-uct="<?= htmlspecialchars($row['UCTStock']) ?>"
              data-date="<?= htmlspecialchars($row['PurchasingDate']) ?>">
              <i class="bi bi-pencil-fill"></i>
            </button>
            <a href="?delete=<?= urlencode($row['TonerModelNo']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?');">
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
        <h5 class="modal-title">Add Toner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Model No</label><input name="model" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Type</label><input name="type" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Reorder Level</label><input name="reorder" type="number" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Stock</label><input name="stock" type="number" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">JCT</label><input name="jct" type="number" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">UCT</label><input name="uct" type="number" class="form-control" required></div>
        <div class="col-md-12"><label class="form-label">Purchase Date</label><input name="date" type="date" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="model" id="edit-model" />
      <div class="modal-header bg-light">
        <h5 class="modal-title">Edit Toner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Type</label><input name="type" id="edit-type" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Reorder Level</label><input name="reorder" id="edit-reorder" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Stock</label><input name="stock" id="edit-stock" type="number" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">JCT</label><input name="jct" id="edit-jct" type="number" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">UCT</label><input name="uct" id="edit-uct" type="number" class="form-control" required></div>
        <div class="col-md-12"><label class="form-label">Purchase Date</label><input name="date" id="edit-date" type="date" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  document.getElementById('edit-model').value = btn.dataset.model;
  document.getElementById('edit-type').value = btn.dataset.type;
  document.getElementById('edit-reorder').value = btn.dataset.reorder;
  document.getElementById('edit-stock').value = btn.dataset.stock;
  document.getElementById('edit-jct').value = btn.dataset.jct;
  document.getElementById('edit-uct').value = btn.dataset.uct;
  document.getElementById('edit-date').value = btn.dataset.date;
});

// Optional: Client-side stock validation
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function (e) {
    const stock = parseInt(this.querySelector('[name="stock"]').value);
    const jct = parseInt(this.querySelector('[name="jct"]').value);
    const uct = parseInt(this.querySelector('[name="uct"]').value);

    if (jct + uct > stock) {
      alert("JCT + UCT cannot be more than Stock");
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
