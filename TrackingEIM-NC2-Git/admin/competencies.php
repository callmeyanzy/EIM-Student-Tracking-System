<?php
require_once __DIR__ . '/../bootstrap.php';
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: /login.php'); exit; }
require_once __DIR__ . '/../models/Competency.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_check($token)) { $msg = 'Invalid token.'; }
    else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            Competency::create($_POST['code'], $_POST['title'], $_POST['description']);
            $msg = 'Competency created.';
        } elseif ($action === 'update') {
            Competency::update((int)$_POST['id'], $_POST['code'], $_POST['title'], $_POST['description']);
            $msg = 'Competency updated.';
        } elseif ($action === 'delete') {
            Competency::delete((int)$_POST['id']);
            $msg = 'Competency deleted.';
        }
    }
}
$items = Competency::all();
require __DIR__ . '/../views/layouts/header.php';
?>
<div class="row">
  <div class="col-md-8">
    <h4>Competencies</h4>
    <?php if ($msg): ?><div class="alert alert-info"><?php echo e($msg); ?></div><?php endif; ?>
    <table class="table table-bordered">
      <thead><tr><th>Code</th><th>Title</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($items as $it): ?>
        <tr>
          <td><?php echo e($it['code']); ?></td>
          <td><?php echo e($it['title']); ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="?edit=<?php echo $it['id']; ?>">Edit</a>
            <form style="display:inline" method="post"><input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $it['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-4">
    <?php $editing = !empty($_GET['edit']) ? Competency::find((int)$_GET['edit']) : null; ?>
    <h4><?php echo $editing ? 'Edit' : 'Create'; ?> Competency</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
      <?php if ($editing): ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?php echo $editing['id']; ?>"><?php else: ?><input type="hidden" name="action" value="create"><?php endif; ?>
      <div class="mb-2"><label class="form-label">Code</label><input class="form-control" name="code" value="<?php echo $editing ? e($editing['code']) : ''; ?>" required></div>
      <div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="title" value="<?php echo $editing ? e($editing['title']) : ''; ?>" required></div>
      <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="description"><?php echo $editing ? e($editing['description']) : ''; ?></textarea></div>
      <div><button class="btn btn-success"><?php echo $editing ? 'Update' : 'Create'; ?></button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../views/layouts/footer.php'; ?>
