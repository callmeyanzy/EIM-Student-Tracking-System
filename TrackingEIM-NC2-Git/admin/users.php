<?php
require_once __DIR__ . '/../bootstrap.php';

// admin only
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../models/UserModel.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_check($token)) { $msg = 'Invalid token.'; }
    else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            UserModel::create($_POST['name'], $_POST['email'], $_POST['password'], $_POST['role']);
            $msg = 'User created.';
        } elseif ($action === 'update') {
            UserModel::update((int)$_POST['id'], $_POST['name'], $_POST['email'], $_POST['role']);
            $msg = 'User updated.';
        } elseif ($action === 'delete') {
            UserModel::delete((int)$_POST['id']);
            $msg = 'User deleted.';
        }
    }
}

$users = UserModel::all();
require __DIR__ . '/../views/layouts/header.php';
?>
<div class="row">
  <div class="col-md-8">
    <h4>Users</h4>
    <?php if ($msg): ?><div class="alert alert-info"><?php echo e($msg); ?></div><?php endif; ?>
    <table class="table table-striped">
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?php echo e($u['name']); ?></td>
            <td><?php echo e($u['email']); ?></td>
            <td><?php echo e($u['role']); ?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="?edit=<?php echo $u['id']; ?>">Edit</a>
              <form style="display:inline" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-4">
    <?php
      $editing = null;
      if (!empty($_GET['edit'])) { $editing = UserModel::find((int)$_GET['edit']); }
    ?>
    <h4><?php echo $editing ? 'Edit User' : 'Create User'; ?></h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
      <?php if ($editing): ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?php echo $editing['id']; ?>"><?php else: ?><input type="hidden" name="action" value="create"><?php endif; ?>
      <div class="mb-2"><label class="form-label">Name</label><input class="form-control" name="name" value="<?php echo $editing ? e($editing['name']) : ''; ?>" required></div>
      <div class="mb-2"><label class="form-label">Email</label><input class="form-control" name="email" value="<?php echo $editing ? e($editing['email']) : ''; ?>" required></div>
      <?php if (!$editing): ?><div class="mb-2"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div><?php endif; ?>
      <div class="mb-2"><label class="form-label">Role</label>
        <select class="form-select" name="role"><option value="student">Student</option><option value="instructor">Instructor</option><option value="admin">Administrator</option></select>
      </div>
      <div><button class="btn btn-success"><?php echo $editing ? 'Update' : 'Create'; ?></button></div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
