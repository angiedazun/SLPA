<?php
include('../config/db.php');

function fetchReport($pdo, $filter) {
    $query = "SELECT * FROM Printer_History";

    switch ($filter) {
        case 'weekly':
            $query .= " WHERE YEARWEEK(CreatedAt) = YEARWEEK(NOW())";
            break;
        case 'monthly':
            $query .= " WHERE MONTH(CreatedAt) = MONTH(NOW()) AND YEAR(CreatedAt) = YEAR(NOW())";
            break;
        case 'yearly':
            $query .= " WHERE YEAR(CreatedAt) = YEAR(NOW())";
            break;
        default:
            // No filter = all
            break;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

$filter = $_GET['filter'] ?? 'all';
$data = fetchReport($pdo, $filter);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Printer History Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>Printer History Report</h2>

    <form method="get" class="mb-3">
        <label for="filter">Select Report Type:</label>
        <select name="filter" id="filter" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All</option>
            <option value="weekly" <?= $filter == 'weekly' ? 'selected' : '' ?>>Weekly</option>
            <option value="monthly" <?= $filter == 'monthly' ? 'selected' : '' ?>>Monthly</option>
            <option value="yearly" <?= $filter == 'yearly' ? 'selected' : '' ?>>Yearly</option>
        </select>
    </form>

    <table class="table table-bordered" id="reportTable">
        <thead>
            <tr>
                <th>PR No</th>
                <th>Printer Model</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($data) > 0): ?>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['PRNo']) ?></td>
                    <td><?= htmlspecialchars($row['PrinterModel']) ?></td>
                    <td><?= htmlspecialchars($row['CreatedAt']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No data found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <button onclick="exportToPDF()" class="btn btn-danger">Download as PDF</button>
    <button onclick="exportToExcel()" class="btn btn-success">Download as Excel</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function exportToExcel() {
            const table = document.getElementById("reportTable");
            const wb = XLSX.utils.table_to_book(table, {sheet: "Report"});
            XLSX.writeFile(wb, "Printer_History_Report.xlsx");
        }

        async function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Printer History Report", 10, 10);

            const rows = [];
            const headers = ["PR No", "Printer Model", "Created At"];
            const table = document.querySelectorAll("#reportTable tbody tr");

            table.forEach(row => {
                const cells = row.querySelectorAll("td");
                const rowData = Array.from(cells).map(cell => cell.innerText);
                rows.push(rowData);
            });

            doc.autoTable({
                head: [headers],
                body: rows,
                startY: 20
            });

            doc.save("Printer_History_Report.pdf");
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>
</html>
