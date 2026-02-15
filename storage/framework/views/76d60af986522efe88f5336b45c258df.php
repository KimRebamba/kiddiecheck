

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Users</h1>
    <p class="text-muted mb-0">Manage system accounts, roles, and access.</p>
  </div>
  <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary btn-sm">Create New User</a>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-md-3">
    <div class="card h-100">
      <div class="card-body py-2">
        <div class="small text-muted mb-1">Total Teachers</div>
        <div class="h5 mb-0"><?php echo e($totalTeachers); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card h-100">
      <div class="card-body py-2">
        <div class="small text-muted mb-1">Total Families</div>
        <div class="h5 mb-0"><?php echo e($totalFamilies); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card h-100">
      <div class="card-body py-2">
        <div class="small text-muted mb-1">Total Active Users</div>
        <div class="h5 mb-0"><?php echo e($totalActiveUsers); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card h-100">
      <div class="card-body py-2">
        <div class="small text-muted mb-1">Recently Registered</div>
        <div class="h5 mb-0"><?php echo e($recentUsers->count()); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Role</label>
        <select name="role" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="admin" <?php echo e($filters['role'] === 'admin' ? 'selected' : ''); ?>>Admin</option>
          <option value="teacher" <?php echo e($filters['role'] === 'teacher' ? 'selected' : ''); ?>>Teacher</option>
          <option value="family" <?php echo e($filters['role'] === 'family' ? 'selected' : ''); ?>>Family</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Username</label>
        <input type="text" name="username" value="<?php echo e($filters['username']); ?>" class="form-control form-control-sm" placeholder="Search username">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Email</label>
        <input type="text" name="email" value="<?php echo e($filters['email']); ?>" class="form-control form-control-sm" placeholder="Search email">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Teacher Name</label>
        <input type="text" name="teacher_name" value="<?php echo e($filters['teacher_name']); ?>" class="form-control form-control-sm" placeholder="Search teacher">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Family Name</label>
        <input type="text" name="family_name" value="<?php echo e($filters['family_name']); ?>" class="form-control form-control-sm" placeholder="Search family">
      </div>
      <div class="col-6 col-md-2">
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="recent_only" id="recent_only" value="1" <?php echo e($filters['recent_only'] ? 'checked' : ''); ?>>
          <label class="form-check-label" for="recent_only">Recently added</label>
        </div>
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="incomplete_only" id="incomplete_only" value="1" <?php echo e($filters['incomplete_only'] ? 'checked' : ''); ?>>
          <label class="form-check-label" for="incomplete_only">Incomplete profiles</label>
        </div>
      </div>
      <div class="col-12 col-md-2 mt-2 mt-md-0 text-md-end">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:48px;">Profile</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Updated</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td>
                <?php if($user->profile_path): ?>
                  <img src="<?php echo e(asset($user->profile_path)); ?>" alt="Profile" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                <?php else: ?>
                  <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:0.75rem;">
                    <?php echo e(strtoupper(substr($user->username, 0, 1))); ?>

                  </div>
                <?php endif; ?>
              </td>
              <td><?php echo e($user->username); ?></td>
              <td><?php echo e($user->email); ?></td>
              <td class="text-capitalize"><?php echo e($user->role); ?></td>
              <td>
                <?php
                  $status = $user->status ?? 'active';
                ?>
                <?php if($status === 'disabled'): ?>
                  <span class="badge bg-secondary">Disabled</span>
                <?php elseif($status === 'reset_required'): ?>
                  <span class="badge bg-warning text-dark">Reset Required</span>
                <?php else: ?>
                  <span class="badge bg-success">Active</span>
                <?php endif; ?>
              </td>
              <td><?php echo e(optional($user->created_at)->format('Y-m-d')); ?></td>
              <td><?php echo e(optional($user->updated_at)->format('Y-m-d')); ?></td>
              <td class="text-end">
                <div class="btn-group btn-group-sm" role="group">
                  <a href="<?php echo e(route('admin.users.show', $user->user_id)); ?>" class="btn btn-outline-secondary">View</a>
                  <a href="<?php echo e(route('admin.users.edit', $user->user_id)); ?>" class="btn btn-outline-secondary">Edit</a>
                  <form method="post" action="<?php echo e(route('admin.users.status', $user->user_id)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="<?php echo e(($status === 'disabled') ? 'active' : 'disabled'); ?>">
                    <button type="submit" class="btn btn-outline-secondary"><?php echo e($status === 'disabled' ? 'Enable' : 'Disable'); ?></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-3">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="p-2">
      <?php echo e($users->links()); ?>

    </div>
  </div>
</div>

<div class="mt-3">
  <h2 class="h6 mb-2">Recently Registered Users</h2>
  <ul class="list-group list-group-flush small">
    <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <li class="list-group-item px-0 d-flex justify-content-between">
        <span><?php echo e($u->username); ?> (<?php echo e($u->role); ?>)</span>
        <span class="text-muted"><?php echo e(optional($u->created_at)->diffForHumans()); ?></span>
      </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <li class="list-group-item px-0 text-muted">No recent registrations.</li>
    <?php endif; ?>
  </ul>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views/admin/users.blade.php ENDPATH**/ ?>