<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Competency.php';
require_once __DIR__ . '/../models/Assessment.php';

$studentId = (int)($_GET['id'] ?? 0);
$student = Student::find($studentId);

if (!$student) {
    header('Location: students.php');
    exit;
}

$competencies = Competency::getByStudent($studentId);
$assessments = Assessment::getByStudent($studentId);
$progressStats = Student::getProgressStats($studentId);
$assessmentStats = Assessment::getStudentAssessmentStats($studentId);
$overallProgress = Student::getOverallProgressPercentage($studentId);

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
        <h3 class="mb-0"><?php echo e($student['name']); ?></h3>
    </div>
    <div>
        <a href="update_progress.php?student=<?php echo $studentId; ?>" class="btn btn-success me-2">
            <i class="fas fa-edit me-1"></i>Update Progress
        </a>
        <a href="students.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 100px; height: 100px; font-size: 3rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="mb-1"><?php echo e($student['name']); ?></h4>
                <p class="text-muted mb-3"><?php echo e($student['email']); ?></p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-primary"><?php echo e($student['tesda_qualification']); ?></span>
                    <span class="badge <?php echo $student['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                        <?php echo ucfirst($student['status']); ?>
                    </span>
                </div>
                
                <hr>
                
                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted">Batch</small>
                        <p class="mb-0 fw-medium"><?php echo e($student['batch_name'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Enrollment Date</small>
                        <p class="mb-0 fw-medium"><?php echo $student['enrollment_date'] ? date('F d, Y', strtotime($student['enrollment_date'])) : 'N/A'; ?></p>
                    </div>
                    <div>
                        <small class="text-muted">Overall Progress</small>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 progress-thin me-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $overallProgress; ?>%"></div>
                            </div>
                            <span class="fw-bold text-success"><?php echo $overallProgress; ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Progress Stats -->
        <div class="card dashboard-card mt-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Progress Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-check-circle text-success me-2"></i>Completed</span>
                    <span class="fw-bold"><?php echo $progressStats['completed']; ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-spinner text-warning me-2"></i>In Progress</span>
                    <span class="fw-bold"><?php echo $progressStats['in_progress']; ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clock text-danger me-2"></i>Not Started</span>
                    <span class="fw-bold"><?php echo $progressStats['not_started']; ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Competencies -->
    <div class="col-md-8">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tasks me-2 text-success"></i>Competency Checklist</h5>
                <span class="badge bg-light text-dark"><?php echo count($competencies); ?> Units</span>
            </div>
            <div class="card-body">
                <?php foreach ($competencies as $comp): 
                    $statusClass = $comp['status'] === 'completed' ? 'status-completed' : 
                                  ($comp['status'] === 'in_progress' ? 'status-in_progress' : 'status-not_started');
                    $statusText = $comp['status'] === 'completed' ? 'Competent' : 
                                  ($comp['status'] === 'in_progress' ? 'Ongoing' : 'Not Yet Competent');
                ?>
                <div class="d-flex align-items-center p-3 mb-2 bg-light rounded">
                    <div class="flex-grow-1">
                        <h6 class="mb-1"><?php echo e($comp['code']); ?> - <?php echo e($comp['title']); ?></h6>
                        <p class="text-muted mb-0 small"><?php echo e($comp['description']); ?></p>
                        <?php if ($comp['practical_score']): ?>
                            <small class="text-info">Score: <?php echo $comp['practical_score']; ?>/100</small>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <span class="status-badge <?php echo $statusClass; ?> mb-1 d-block"><?php echo $statusText; ?></span>
                        <?php if ($comp['assessment_date']): ?>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($comp['assessment_date'])); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Assessment History -->
        <div class="card dashboard-card mt-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2 text-info"></i>Assessment History</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($assessments)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fs-1 mb-2"></i>
                        <p>No assessments recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Competency</th>
                                    <th>Type</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assessments as $assessment): 
                                    $resultClass = $assessment['result'] === 'pass' ? 'bg-success' : 
                                                  ($assessment['result'] === 'fail' ? 'bg-danger' : 'bg-warning');
                                ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($assessment['assessed_at'])); ?></td>
                                    <td><?php echo e($assessment['competency_code']); ?></td>
                                    <td><?php echo e($assessment['assessment_type']); ?></td>
                                    <td><?php echo $assessment['score']; ?>/100</td>
                                    <td><span class="badge <?php echo $resultClass; ?>"><?php echo ucfirst($assessment['result']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
