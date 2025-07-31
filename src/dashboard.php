<?php
require 'session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - Toner System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../public/css/dashboard.css" />
</head>
<body>

<div class="d-flex" id="wrapper">

  <!-- Sidebar -->
  <nav id="sidebar" class="bg-lightblue d-flex flex-column">
    <div class="sidebar-header text-center py-4">
      <img src="../public/img/logo new.png" alt="Logo" class="img-fluid" style="max-width: 120px;">
    </div>
    <ul class="nav flex-column px-3">
      <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i>Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="toner_master.php"><i class="bi bi-archive me-2"></i>Toner Master</a></li>
      <li class="nav-item"><a class="nav-link" href="toner_receiving.php"><i class="bi bi-download me-2"></i>Toner Receiving</a></li>
      <li class="nav-item"><a class="nav-link" href="toner_issuing.php"><i class="bi bi-upload me-2"></i>Toner Issuing</a></li>
      <li class="nav-item"><a class="nav-link" href="toner_returns.php"><i class="bi bi-arrow-counterclockwise me-2"></i>Toner Returns</a></li>
      <li class="nav-item"><a class="nav-link" href="toner_compatible.php"><i class="bi bi-link-45deg me-2"></i>Compatibility</a></li>
      <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
    </ul>
    <div class="mt-auto px-3 pb-3">
      <a href="../public/logout.php" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    </div>
  </nav>

  <!-- Main Content -->
  <div id="page-content-wrapper" class="flex-grow-1">
    <!-- Topbar -->
    <nav class="navbar navbar-light bg-white shadow-sm px-4">
      <span class="navbar-brand fw-semibold">Dashboard</span>
      <span class="ms-auto text-muted">👋 Welcome, <?= htmlspecialchars($_SESSION['user']) ?></span>
    </nav>

    <main class="container-fluid py-4 px-4">
      <div class="row g-4">

        <div class="col-md-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-primary"><i class="bi bi-box-seam me-2"></i>Toners in Stock</h5>
              <p class="card-text text-muted">Check current stock and reorder status.</p>
              <a href="toner_master.php" class="btn btn-outline-primary mt-auto">View Master</a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-success"><i class="bi bi-upload me-2"></i>Issue Toners</h5>
              <p class="card-text text-muted">Distribute toners and track usage.</p>
              <a href="toner_issuing.php" class="btn btn-outline-success mt-auto">Issue Now</a>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h5 class="card-title text-warning"><i class="bi bi-graph-up-arrow me-2"></i>Reports</h5>
              <p class="card-text text-muted">Generate monthly and yearly reports.</p>
              <a href="reports.php" class="btn btn-outline-warning mt-auto">Generate</a>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
