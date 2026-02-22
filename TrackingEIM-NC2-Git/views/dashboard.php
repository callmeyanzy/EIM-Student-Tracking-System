<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Welcome back, <?php echo e($user['name']); ?>!</p>
    </div>
    <span class="badge bg-primary fs-6"><?php echo date('F j, Y'); ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <h3 class="mb-0"><?php echo Student::getTotalStudents(); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Active Trainees</h6>
                    <h3 class="mb-0"><?php echo Student::getActiveStudentsCount(); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Completed Competencies</h6>
                    <h3 class="mb-0"><?php echo StudentCompetency::getCompletedCompetenciesCount(); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Pass Rate</h6>
                    <h3 class="mb-0"><?php echo Assessment::getPassRatePercentage(); ?>%</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="/admin/students.php" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-users mb-2 d-block fs-4"></i>
                            Students
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/competencies.php" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-tasks mb-2 d-block fs-4"></i>
                            Competencies
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/batches.php" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-layer-group mb-2 d-block fs-4"></i>
                            Batches
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/admin/users.php" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-user-cog mb-2 d-block fs-4"></i>
                            Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
