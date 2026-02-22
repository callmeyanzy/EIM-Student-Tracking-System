<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/StudentCompetency.php';

$selectedBatch = (int)($_GET['batch'] ?? 0);
$batches = Batch::all();

$progressData = [];
$competencies = [];
$students = [];

if ($selectedBatch) {
    $progressData = StudentCompetency::getBatchProgress($selectedBatch);
    
    // Organize data by student
    foreach ($progressData as $row) {
        if (!isset($students[$row['student_id']])) {
            $students[$row['student_id']] = [
                'name' => $row['student_name'],
                'competencies' => []
            ];
        }
        $students[$row['student_id']]['competencies'][$row['competency_id']] = [
            'code' => $row['competency_code'],
            'status' => $row['status'],
            'score' => $row['practical_score']
        ];
        
        if (!isset($competencies[$row['competency_id']])) {
            $competencies[$row['competency_id']] = [
                'code' => $row['competency_code'],
                'title' => $row['competency_title']
            ];
        }
    }
}

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="fas fa-chart-bar me-2 text-primary"></i>Batch Progress</h3>
        <p class="text-muted mb-0">View and track progress for all students in a batch</p>
    </div>
    <?php if ($selectedBatch): ?>
    <button class="btn btn-success" onclick="exportToExcel()">
        <i class="fas fa-file-excel me-1"></i>Export to Excel
    </button>
    <?php endif; ?>
</div>

<!-- Batch Selector -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Select Batch</label>
                <select class="form-select" name="batch" onchange="this.form.submit()">
                    <option value="">Choose a batch...</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?php echo $batch['id']; ?>" <?php echo $selectedBatch == $batch['id'] ? 'selected' : ''; ?>>
                            <?php echo e($batch['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedBatch && !empty($students)): ?>
<!-- Progress Matrix -->
<div class="card dashboard-card">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0">Progress Matrix</h5>
        <small class="text-muted">Click on a cell to update progress</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="progressTable">
                <thead class="table-dark">
                    <tr>
                        <th class="sticky-col" style="min-width: 200px;">Student</th>
                        <?php foreach ($competencies as $compId => $comp): ?>
                            <th class="text-center" style="min-width: 100px;">
                                <small><?php echo e($comp['code']); ?></small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $studentId => $student): ?>
                    <tr>
                        <td class="sticky-col bg-white fw-medium">
                            <a href="student_profile.php?id=<?php echo $studentId; ?>" class="text-decoration-none">
                                <?php echo e($student['name']); ?>
                            </a>
                        </td>
                        <?php foreach ($competencies as $compId => $comp): 
                            $status = $student['competencies'][$compId]['status'] ?? 'not_started';
                            $score = $student['competencies'][$compId]['score'] ?? null;
                            
                            $cellClass = $status === 'completed' ? 'bg-success bg-opacity-25' : 
                                        ($status === 'in_progress' ? 'bg-warning bg-opacity-25' : 'bg-danger bg-opacity-10');
                            $icon = $status === 'completed' ? '<i class="fas fa-check text-success"></i>' : 
                                   ($status === 'in_progress' ? '<i class="fas fa-spinner text-warning"></i>' : '<i class="fas fa-minus text-muted"></i>');
                        ?>
                        <td class="text-center <?php echo $cellClass; ?>" style="cursor: pointer;" 
                            onclick="window.location='update_progress.php?student=<?php echo $studentId; ?>&competency=<?php echo $compId; ?>'">
                            <?php echo $icon; ?>
                            <?php if ($score): ?>
                                <small class="d-block text-muted"><?php echo $score; ?>%</small>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="mt-3 d-flex gap-3">
    <div class="d-flex align-items-center">
        <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
        <small>Competent</small>
    </div>
    <div class="d-flex align-items-center">
        <span class="badge bg-warning me-2"><i class="fas fa-spinner"></i></span>
        <small>Ongoing</small>
    </div>
    <div class="d-flex align-items-center">
        <span class="badge bg-danger me-2"><i class="fas fa-minus"></i></span>
        <small>Not Yet Competent</small>
    </div>
</div>

<?php elseif ($selectedBatch): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>No students found in this batch.
</div>
<?php else: ?>
<div class="alert alert-secondary">
    <i class="fas fa-arrow-up me-2"></i>Select a batch to view progress.
</div>
<?php endif; ?>

<script>
function exportToExcel() {
    const table = document.getElementById('progressTable');
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push('"' + th.textContent.trim() + '"');
    });
    csv.push(headers.join(','));
    
    // Get rows
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
    link.setAttribute('download', 'batch_progress.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
