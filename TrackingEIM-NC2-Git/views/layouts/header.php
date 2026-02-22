<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Progress - EIM NC II</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
<!-- Admin Layout with Sidebar -->
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="bg-dark text-white">
    <div class="sidebar-header p-3">
      <h4 class="m-0"><i class="fas fa-bolt me-2"></i>EIM NC II</h4>
      <small class="text-muted">Progress Tracker</small>
    </div>
    <ul class="list-unstyled components">
      <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
        <a href="/dashboard.php" class="nav-link text-white px-3 py-2"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/students') !== false ? 'active' : ''; ?>">
        <a href="/admin/students.php" class="nav-link text-white px-3 py-2"><i class="fas fa-users me-2"></i>Students</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/batch_progress') !== false ? 'active' : ''; ?>">
        <a href="/admin/batch_progress.php" class="nav-link text-white px-3 py-2"><i class="fas fa-chart-bar me-2"></i>Batch Progress</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/competencies') !== false ? 'active' : ''; ?>">
        <a href="/admin/competencies.php" class="nav-link text-white px-3 py-2"><i class="fas fa-tasks me-2"></i>Competencies</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/batches') !== false ? 'active' : ''; ?>">
        <a href="/admin/batches.php" class="nav-link text-white px-3 py-2"><i class="fas fa-layer-group me-2"></i>Batches</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/assessments') !== false ? 'active' : ''; ?>">
        <a href="/admin/assessments.php" class="nav-link text-white px-3 py-2"><i class="fas fa-clipboard-check me-2"></i>Assessments</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/reports') !== false ? 'active' : ''; ?>">
        <a href="/admin/reports.php" class="nav-link text-white px-3 py-2"><i class="fas fa-file-alt me-2"></i>Reports</a>
      </li>
      <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/users') !== false ? 'active' : ''; ?>">
        <a href="/admin/users.php" class="nav-link text-white px-3 py-2"><i class="fas fa-user-cog me-2"></i>Users</a>
      </li>
    </ul>
  </nav>

  <!-- Page Content -->
  <div id="content">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
      <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
          <i class="fas fa-bars"></i>
        </button>
        <div class="ms-auto d-flex align-items-center">
          <span class="me-3 text-muted"><i class="fas fa-user-circle me-1"></i><?php echo e($_SESSION['user']['name']); ?></span>
          <a href="/controllers/auth.php?action=logout" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      </div>
    </nav>
    <div class="container-fluid p-4">
<?php else: ?>
<!-- Non-Admin Layout -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/dashboard.php"><i class="fas fa-bolt me-2"></i>EIM NC II Tracker</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="/dashboard.php"><i class="fas fa-user me-1"></i><?php echo e($_SESSION['user']['name']); ?></a></li>
          <li class="nav-item"><a class="nav-link" href="/controllers/auth.php?action=logout"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
<?php endif; ?>
