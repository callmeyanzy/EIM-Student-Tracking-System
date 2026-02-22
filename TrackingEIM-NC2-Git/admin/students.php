<?php
require_once __DIR__ . '/../bootstrap.php';

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Batch.php';
require_once __DIR__ . '/../models/StudentCompetency.php';
require_once __DIR__ . '/../models/UserModel.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_check($token)) {
        $error = 'Invalid token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            // Create user account first
            $email = $_POST['email'];
            $existingUser = UserModel::findByEmail($email);
            
            if ($existingUser) {
                $error = 'A user with this email already exists.';
            } else {
                // Create user with default password
                $defaultPassword = password_hash('student123', PASSWORD_DEFAULT);
                UserModel::create($_POST['name'], $email, $defaultPassword, 'student');
                $user = UserModel::findByEmail($email);
                
                // Create student record
                Student::create($_POST['name'], $email, $_POST['batch_id'], $_POST['enrollment_date']);
                $pdo = Database::getConnection();
                $studentId = $pdo->lastInsertId();
                
                // Link student to user
                $stmt = $pdo->prepare('UPDATE students SET user_id = :user_id WHERE id = :id');
                $stmt->execute([':user_id' => $user['id'], ':id' => $studentId]);
                
                // Initialize competencies for the student
                StudentCompetency::initializeStudentCompetencies($studentId);
                
                $msg = 'Student created successfully. Default password: student123';
            }
        } elseif ($action === 'update') {
            Student::update(
                (int)$_POST['id'],
                $_POST['name'],
                $_POST['email'],
                $_POST['batch_id'],
                $_POST['status'],
                $_POST['enrollment_date']
            );
            $msg = 'Student updated successfully.';
        } elseif ($action === 'delete') {
            Student::delete((int)$_POST['id']);
            $msg = 'Student deleted successfully.';
        }
    }
}

// Get filter parameters
$batchFilter = $_GET['batch'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// Get students with filters
if ($batchFilter) {
    $students = Student::getByBatch((int)$batchFilter);
} else {
    $students = Student::all();
}

// Apply search filter
if ($searchQuery) {
    $students = array_filter($students, function($s) use ($searchQuery) {
        return stripos($s['name'], $searchQuery) !== false || 
               stripos($s['email'], $searchQuery) !== false;
    });
}

$batches = Batch::all();

require __DIR__ . '/../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="fas fa-users me-2 text-primary"></i>Students</h3>
        <p class="text-muted mb-0">Manage student records and track their progress</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="resetForm()">
        <i class="fas fa-plus me-1"></i>Add Student
    </button>
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

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search students..." 
                           value="<?php echo e($searchQuery); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="batch" onchange="this.form.submit()">
                    <option value="">All Batches</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?php echo $batch['id']; ?>" <?php echo $batchFilter == $batch['id'] ? 'selected' : ''; ?>>
                            <?php echo e($batch['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <a href="students.php" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card dashboard-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Batch</th>
                        <th>Enrollment Date</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): 
                        $progress = Student::getOverallProgressPercentage($student['id']);
                        $statusClass = $student['status'] === 'active' ? 'bg-success' : 
                                      ($student['status'] === 'inactive' ? 'bg-secondary' : 'bg-info');
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 35px; height: 35px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <a href="student_profile.php?id=<?php echo $student['id']; ?>" class="text-decoration-none fw-medium">
                                    <?php echo e($student['name']); ?>
                                </a>
                            </div>
                        </td>
                        <td><?php echo e($student['email']); ?></td>
                        <td><?php echo e($student['batch_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $student['enrollment_date'] ? date('M d, Y', strtotime($student['enrollment_date'])) : 'N/A'; ?></td>
                        <td style="width: 150px;">
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 progress-thin me-2">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <small class="text-muted"><?php echo $progress; ?>%</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $statusClass; ?> status-badge"><?php echo ucfirst($student['status']); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="student_profile.php?id=<?php echo $student['id']; ?>" class="btn btn-outline-info" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-outline-primary" title="Edit" 
                                        onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this student?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="studentId">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" id="studentName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="studentEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch</label>
                        <select class="form-select" name="batch_id" id="studentBatch" required>
                            <option value="">Select Batch</option>
                            <?php foreach ($batches as $batch): ?>
                                <option value="<?php echo $batch['id']; ?>"><?php echo e($batch['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enrollment Date</label>
                        <input type="date" class="form-control" name="enrollment_date" id="enrollmentDate">
                    </div>
                    <div class="mb-3" id="statusField" style="display: none;">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="studentStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="graduated">Graduated</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').textContent = 'Add Student';
    document.getElementById('formAction').value = 'create';
    document.getElementById('studentId').value = '';
    document.getElementById('studentName').value = '';
    document.getElementById('studentEmail').value = '';
    document.getElementById('studentBatch').value = '';
    document.getElementById('enrollmentDate').value = '';
    document.getElementById('statusField').style.display = 'none';
    document.getElementById('studentEmail').readOnly = false;
}

function editStudent(student) {
    document.getElementById('modalTitle').textContent = 'Edit Student';
    document.getElementById('formAction').value = 'update';
    document.getElementById('studentId').value = student.id;
    document.getElementById('studentName').value = student.name;
    document.getElementById('studentEmail').value = student.email;
    document.getElementById('studentEmail').readOnly = true;
    document.getElementById('studentBatch').value = student.batch_id || '';
    document.getElementById('enrollmentDate').value = student.enrollment_date || '';
    document.getElementById('studentStatus').value = student.status;
    document.getElementById('statusField').style.display = 'block';
    
    new bootstrap.Modal(document.getElementById('studentModal')).show();
}
</script>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
