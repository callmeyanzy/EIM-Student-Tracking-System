<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Competency.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/StudentCompetency.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_check($token)) {
        $error = 'Invalid token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            Assessment::create(
                (int)$_POST['student_id'],
                (int)$_POST['competency_id'],
                $_POST['assessment_type'],
                (int)$_POST['score'],
                $_POST['result'],
                $_SESSION['user']['id'],
                $_POST['remarks'] ?? null
            );
            
            // Also update student competency status based on result
            $status = $_POST['result'] === 'pass' ? 'completed' : 
                     ($_POST['result'] === 'fail' ? 'in_progress' : 'not_started');
            StudentCompetency::updateStatus(
                (int)$_POST['student_id'],
                (int)$_POST['competency_id'],
                $status,
                (int)$_POST['score'],
                $_POST['remarks'] ?? null
            );
            
            $msg = 'Assessment recorded successfully.';
        }
    }
}

$students = Student::all();
$competencies = Competency::all();
$recentAssessments = Assessment::getRecent(20);

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="fas fa-clipboard-check me-2 text-info"></i>Assessments</h3>
        <p class="text-muted mb-0">Record and view student assessments</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assessmentModal">
        <i class="fas fa-plus me-1"></i>Record Assessment
    </button>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e($msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Recent Assessments -->
<div class="card dashboard-card">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Recent Assessments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Competency</th>
                        <th>Type</th>
                        <th>Score</th>
                        <th>Result</th>
                        <th>Assessed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAssessments as $assessment): 
                        $resultClass = $assessment['result'] === 'pass' ? 'bg-success' : 
                                      ($assessment['result'] === 'fail' ? 'bg-danger' : 'bg-warning');
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($assessment['assessed_at'])); ?></td>
                        <td><?php echo e($assessment['student_name']); ?></td>
                        <td><?php echo e($assessment['competency_code']); ?></td>
                        <td><?php echo e($assessment['assessment_type']); ?></td>
                        <td><?php echo $assessment['score']; ?>/100</td>
                        <td><span class="badge <?php echo $resultClass; ?>"><?php echo ucfirst($assessment['result']); ?></span></td>
                        <td><?php echo e($assessment['assessed_by_name'] ?? 'System'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Record Assessment Modal -->
<div class="modal fade" id="assessmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record New Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>">
                                        <?php echo e($student['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Competency</label>
                            <select class="form-select" name="competency_id" required>
                                <option value="">Select Competency</option>
                                <?php foreach ($competencies as $comp): ?>
                                    <option value="<?php echo $comp['id']; ?>">
                                        <?php echo e($comp['code']); ?> - <?php echo e($comp['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">Assessment Type</label>
                            <select class="form-select" name="assessment_type" required>
                                <option value="Practical">Practical</option>
                                <option value="Written">Written</option>
                                <option value="Oral">Oral</option>
                                <option value="Project">Project</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Score (0-100)</label>
                            <input type="number" class="form-control" name="score" min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Result</label>
                            <select class="form-select" name="result" required>
                                <option value="pass">Pass</option>
                                <option value="fail">Fail</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="2" placeholder="Optional notes about the assessment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Assessment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
