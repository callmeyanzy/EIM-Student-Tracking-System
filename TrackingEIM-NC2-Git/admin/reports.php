<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Competency.php';

$batches = Batch::all();
$competencies = Competency::all();

// Get filter parameters
$batchFilter = (int)($_GET['batch'] ?? 0);
$competencyFilter = (int)($_GET['competency'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

$reportData = [];
$batchName = 'All Batches';

if ($batchFilter) {
    $batch = Batch::find($batchFilter);
    $batchName = $batch['name'] ?? 'Unknown Batch';
    $students = Student::getByBatch($batchFilter);
    
    foreach ($students as $student) {
        $competencyList = Competency::getByStudent($student['id']);
        
        foreach ($competencyList as $comp) {
            // Apply filters
            if ($competencyFilter && $comp['id'] != $competencyFilter) continue;
            if ($statusFilter && $comp['status'] != $statusFilter) continue;
            
            $reportData[] = [
                'student' => $student,
                'competency' => $comp
            ];
        }
    }
}

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="fas fa-file-alt me-2 text-warning"></i>Reports</h3>
        <p class="text-muted mb-0">Generate and export progress reports</p>
    </div>
</div>

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Batch</label>
                <select class="form-select" name="batch">
                    <option value="">All Batches</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?php echo $batch['id']; ?>" <?php echo $batchFilter == $batch['id'] ? 'selected' : ''; ?>>
                            <?php echo e($batch['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Competency</label>
                <select class="form-select" name="competency">
                    <option value="">All Competencies</option>
                    <?php foreach ($competencies as $comp): ?>
                        <option value="<?php echo $comp['id']; ?>" <?php echo $competencyFilter == $comp['id'] ? 'selected' : ''; ?>>
                            <?php echo e($comp['code']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Competent</option>
                    <option value="in_progress" <?php echo $statusFilter === 'in_progress' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="not_started" <?php echo $statusFilter === 'not_started' ? 'selected' : ''; ?>>Not Yet Competent</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i>Generate Report
                </button>
                <?php if (!empty($reportData)): ?>
                <button type="button" class="btn btn-success ms-2" onclick="exportToExcel()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if ($batchFilter): ?>
<!-- Report Summary -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Records</h6>
                <h3 class="mb-0"><?php echo count($reportData); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h6 class="text-muted">Competent</h6>
                <h3 class="mb-0 text-success">
                    <?php echo count(array_filter($reportData, fn($r) => $r['competency']['status'] === 'completed')); ?>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h6 class="text-muted">Ongoing</h6>
                <h3 class="mb-0 text-warning">
                    <?php echo count(array_filter($reportData, fn($r) => $r['competency']['status'] === 'in_progress')); ?>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <h6 class="text-muted">Not Yet Competent</h6>
                <h3 class="mb-0 text-danger">
                    <?php echo count(array_filter($reportData, fn($r) => $r['competency']['status'] === 'not_started')); ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card dashboard-card">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0">Progress Report - <?php echo e($batchName); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reportTable">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Competency</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Assessment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $row): 
                        $statusClass = $row['competency']['status'] === 'completed' ? 'status-completed' : 
                                      ($row['competency']['status'] === 'in_progress' ? 'status-in_progress' : 'status-not_started');
                        $statusText = $row['competency']['status'] === 'completed' ? 'Competent' : 
                                      ($row['competency']['status'] === 'in_progress' ? 'Ongoing' : 'Not Yet Competent');
                    ?>
                    <tr>
                        <td><?php echo e($row['student']['name']); ?></td>
                        <td><?php echo e($row['competency']['code']); ?> - <?php echo e($row['competency']['title']); ?></td>
                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                        <td><?php echo $row['competency']['practical_score'] ?? '-'; ?></td>
                        <td><?php echo $row['competency']['assessment_date'] ? date('M d, Y', strtotime($row['competency']['assessment_date'])) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>Select a batch and click "Generate Report" to view progress data.
</div>
<?php endif; ?>

<script>
function exportToExcel() {
    const table = document.getElementById('reportTable');
    let csv = [];
    
    // Headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push('"' + th.textContent.trim() + '"');
    });
    csv.push(headers.join(','));
    
    // Data
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push('"' + td.textContent.trim() + '"');
        });
        csv.push(row.join(','));
    });
    
    // Download
    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'progress_report_<?php echo date('Y-m-d'); ?>.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
