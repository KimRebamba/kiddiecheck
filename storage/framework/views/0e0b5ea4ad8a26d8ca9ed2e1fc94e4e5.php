<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h1 class="mb-4">Create Account</h1>
  <form method="POST" action="<?php echo e(route('register')); ?>" class="row g-3">
    <?php echo csrf_field(); ?>
    <div class="col-md-6">
      <label class="form-label">Name</label>
      <input class="form-control" type="text" name="name" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Password</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Role</label>
      <select class="form-select" name="role" required>
        <option value="family">Family</option>
        <option value="teacher">Teacher</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Profile Path (optional)</label>
      <input class="form-control" type="text" name="profile_path" placeholder="/storage/public/avatar.jpg">
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit">Register</button>
      <a class="btn btn-link" href="<?php echo e(route('login')); ?>">Already have an account? Login</a>
    </div>
  </form>
  <!-- Fallback CDN while Node/Vite is not running -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\auth\register.blade.php ENDPATH**/ ?>