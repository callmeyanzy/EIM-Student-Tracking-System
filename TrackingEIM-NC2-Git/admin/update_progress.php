<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Competency.php';
require_once __DIR__ . '/../models/StudentCompetency.php';
require_once __DIR__ . '/../models/Assessment.php';

$msg = '';
$error = '';

// Pre-selected values from URL
$preSelectedStudent = (int)($_GET['student'] ?? 0);
$preSelectedCompetency = (int)($_GET['competency'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_check($token)) {
        $error = 'Invalid token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_progress') {
            $studentId = (int)$_POST['student_id'];
            $competencyId = (int)$_POST['competency_id'];
            $status = $_POST['status'];
            $practicalScore = $_POST['practical_score'] ? (int)$_POST['practical_score'] : null;
            $remarks = $_POST['remarks'] ?? null;
            
            // Update competency status
            StudentCompetency::updateStatus($studentId, $competencyId, $status, $practicalScore, $remarks);
            
            // Also create an assessment record if score is provided
            if ($practicalScore !== null) {
                $result = $practicalScore >= 75 ? 'pass' : 'fail';
                Assessment::create(
                    $studentId,
                    $competencyId,
                    $_POST['assessment_type'] ?? 'Practical',
                    $practicalScore,
                    $result,
                    $_SESSION['user']['id'],
                    $remarks
                );
            }
            
            $msg = 'Progress updated successfully.';
            $preSelectedStudent = $studentId;
            $preSelectedCompetency = $competencyId;
        }
    }
}

$students = Student::all();
$competencies = Competency::all();

// Get current progress if both student and competency are selected
$currentProgress = null;
if ($preSelectedStudent && $preSelectedCompetency) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('
        SELECT * FROM student_competencies 
        WHERE student_id = :student_id AND competency_id = :competency_id
    ');
    $stmt->execute([
        ':student_id' => $preSelectedStudent,
        ':competency_id' => $preSelectedCompetency
    ]);
    $currentProgress = $stmt->fetch();
}

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="fas fa-edit me-2 text-success"></i>Update Progress</h3>
        <p class="text-muted mb-0">Update student competency status and record assessments</p>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e($msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0">Update Competency Status</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="update_progress">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" id="studentSelect" required 
                                    onchange="updateCompetencies()">
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>" 
                                            <?php echo $preSelectedStudent == $student['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($student['name']); ?> (<?php echo e($student['batch_name'] ?? 'No Batch'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Competency</label>
                            <select class="form-select" name="competency_id" id="competencySelect" required>
                                <option value="">Select Competency</option>
                                <?php foreach ($competencies as $comp): ?>
                                    <option value="<?php echo $comp['id']; ?>"
                                            <?php echo $preSelectedCompetency == $comp['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($comp['code']); ?> - <?php echo e($comp['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="not_started" <?php echo ($currentProgress['status'] ?? '') === 'not_started' ? 'selected' : ''; ?>>
                                    Not Yet Competent
                                </option>
                                <option value="in_progress" <?php echo ($currentProgress['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>
                                    Ongoing
                                </option>
                                <option value="completed" <?php echo ($currentProgress['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>
                                    Competent
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Practical Score (0-100)</label>
                            <input type="number" class="form-control" name="practical_score" min="0" max="100"
                                   value="<?php echo $currentProgress['practical_score'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Assessment Type</label>
                            <select class="form-select" name="assessment_type">
                                <option value="Practical">Practical</option>
                                <option value="Written">Written</option>
                                <option value="Oral">Oral</option>
                                <option value="Project">Project</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="1"><?php echo e($currentProgress['remarks'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Update Progress
                        </button>
                        <a href="batch_progress.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Quick Guide</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <strong class="text-danger">Not Yet Competent</strong>
                        <p class="text-muted small mb-0">Student has not started or is struggling with this competency.</p>
                    </li>
                    <li class="mb-3">
                        <strong class="text-warning">Ongoing</strong>
                        <p class="text-muted small mb-0">Student is actively working on this competency.</p>
                    </li>
                    <li class="mb-3">
                        <strong class="text-success">Competent</strong>
                        <p class="text-muted small mb-0">Student has successfully demonstrated this competency.</p>
                    </li>
                </ul>
                <hr>
                <p class="text-muted small mb-0">
                    <i class="fas fa-lightbulb text-warning me-1"></i>
                    <strong>Tip:</strong> Adding a practical score will automatically create an assessment record.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
