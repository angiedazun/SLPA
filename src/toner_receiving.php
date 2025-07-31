<?php
require 'session.php';
require '../config/db.php';

$addError = '';
$showToast = false;

// Add new receiving record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    // Validate required fields (you can expand as needed)
    if (empty($_POST['item']) || empty($_POST['qty']) || empty($_POST['date'])) {
        $addError = "Please fill all required fields!";
        $showToast = true;
    } else {
        $stmt = $pdo->prepare("INSERT INTO Toner_Receiving (Item, NoOfUnits, Supplier, TenderFileNo, InvoiceNo, UnitPrice, Date)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['item'], $_POST['qty'], $_POST['supplier'],
            $_POST['tender'], $_POST['invoice'], $_POST['price'], $_POST['date']
        ]);
        header("Location: toner_receiving.php");
        exit;
    }
}

// Edit existing record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    if (empty($_POST['qty']) || empty($_POST['date'])) {
        $addError = "Quantity and Date are required!";
        $showToast = true;
    } else {
        $stmt = $pdo->prepare("UPDATE Toner_Receiving SET NoOfUnits=?, Supplier=?, TenderFileNo=?, InvoiceNo=?, UnitPrice=?, Date=? WHERE ID=?");
        $stmt->execute([
            $_POST['qty'], $_POST['supplier'], $_POST['tender'], $_POST['invoice'], $_POST['price'], $_POST['date'], $_POST['id']
        ]);
        header("Location: toner_receiving.php");
        exit;
    }
}

// Delete record
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM Toner_Receiving WHERE ID=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: toner_receiving.php");
    exit;
}

// Fetch all receiving records
$data = $pdo->query("SELECT * FROM Toner_Receiving ORDER BY Date DESC")->fetchAll(PDO::FETCH_ASSOC);
// Fetch toner models for dropdown
$models = $pdo->query("SELECT TonerModelNo FROM Toner_Master ORDER BY TonerModelNo")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Toner Receiving</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

<?php if ($showToast): ?>
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999; width: 100%; display: flex; justify-content: center;">
  <div id="errorToast" class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 320px; max-width: 90vw;">
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
    var toastEl = document.getElementById('errorToast');
    if (toastEl) {
      var toast = new bootstrap.Toast(toastEl);
      toast.show();
    }
  };
</script>
<?php endif; ?>

<div class="container mt-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="text-primary mb-2">Toner Receiving</h4>
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
          <th>Item</th>
          <th>Quantity</th>
          <th>Supplier</th>
          <th>Tender File No</th>
          <th>Invoice No</th>
          <th>Unit Price</th>
          <th>Date</th>
          <th style="width:120px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['Item']) ?></td>
            <td><?= (int)$row['NoOfUnits'] ?></td>
            <td><?= htmlspecialchars($row['Supplier']) ?></td>
            <td><?= htmlspecialchars($row['TenderFileNo']) ?></td>
            <td><?= htmlspecialchars($row['InvoiceNo']) ?></td>
            <td><?= number_format($row['UnitPrice'], 2) ?></td>
            <td><?= htmlspecialchars($row['Date']) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editModal"
                data-id="<?= $row['ID'] ?>"
                data-item="<?= htmlspecialchars($row['Item']) ?>"
                data-qty="<?= (int)$row['NoOfUnits'] ?>"
                data-supplier="<?= htmlspecialchars($row['Supplier']) ?>"
                data-tender="<?= htmlspecialchars($row['TenderFileNo']) ?>"
                data-invoice="<?= htmlspecialchars($row['InvoiceNo']) ?>"
                data-price="<?= number_format($row['UnitPrice'], 2) ?>"
                data-date="<?= htmlspecialchars($row['Date']) ?>">
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
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" novalidate>
      <input type="hidden" name="action" value="add" />
      <div class="modal-header bg-light">
        <h5 class="modal-title">Add Toner Receiving</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Toner Model</label>
          <select name="item" class="form-select" required>
            <option value="">Select Model</option>
            <?php foreach ($models as $model): ?>
              <option value="<?= htmlspecialchars($model) ?>"><?= htmlspecialchars($model) ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Quantity</label>
          <input type="number" name="qty" class="form-control" min="1" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Supplier</label>
          <input type="text" name="supplier" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Tender File No</label>
          <input type="text" name="tender" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Invoice No</label>
          <input type="text" name="invoice" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit Price</label>
          <input type="number" step="0.01" name="price" class="form-control" />
        </div>
        <div class="col-md-12">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control" required />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" novalidate>
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="id" id="edit-id" />
      <div class="modal-header bg-light">
        <h5 class="modal-title">Edit Toner Receiving</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Toner Model</label>
          <input name="item" id="edit-item" class="form-control" readonly />
        </div>
        <div class="col-md-6">
          <label class="form-label">Quantity</label>
          <input type="number" name="qty" id="edit-qty" class="form-control" min="1" required />
        </div>
        <div class="col-md-6">
          <label class="form-label">Supplier</label>
          <input type="text" name="supplier" id="edit-supplier" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Tender File No</label>
          <input type="text" name="tender" id="edit-tender" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Invoice No</label>
          <input type="text" name="invoice" id="edit-invoice" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Unit Price</label>
          <input type="number" step="0.01" name="price" id="edit-price" class="form-control" />
        </div>
        <div class="col-md-12">
          <label class="form-label">Date</label>
          <input type="date" name="date" id="edit-date" class="form-control" required />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" type="submit">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function(event) {
  const btn = event.relatedTarget;
  document.getElementById('edit-id').value = btn.dataset.id;
  document.getElementById('edit-item').value = btn.dataset.item;
  document.getElementById('edit-qty').value = btn.dataset.qty;
  document.getElementById('edit-supplier').value = btn.dataset.supplier;
  document.getElementById('edit-tender').value = btn.dataset.tender;
  document.getElementById('edit-invoice').value = btn.dataset.invoice;
  document.getElementById('edit-price').value = btn.dataset.price;
  document.getElementById('edit-date').value = btn.dataset.date;
});
</script>
</body>
</html>
