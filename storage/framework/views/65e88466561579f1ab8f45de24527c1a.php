<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>ECCD Scale Explorer</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 16px; }
		h1, h2, h3 { margin-top: 24px; }
		table { border-collapse: collapse; width: 100%; margin-top: 8px; }
		th, td { border: 1px solid #ccc; padding: 4px 6px; font-size: 13px; }
		th { background-color: #f5f5f5; }
		.domain-section { margin-top: 16px; }
		.muted { color: #777; font-size: 12px; }
		.form-inline { margin-bottom: 16px; }
		.form-inline label { margin-right: 8px; }
		.form-inline select { padding: 4px; }
		.form-inline button { padding: 4px 10px; }
	</style>
</head>
<body>

	<h1>ECCD Scale Versions</h1>

	<form method="GET" class="form-inline">
		<label for="scale_version_id">Select scale version:</label>
		<select id="scale_version_id" name="scale_version_id">
			<option value="">-- choose --</option>
			<?php $__currentLoopData = $scaleVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<option value="<?php echo e($sv->id); ?>" <?php echo e((string)$sv->id === (string)$selectedScaleId ? 'selected' : ''); ?>>
					<?php echo e($sv->name); ?>

				</option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
		<button type="submit">Load</button>
	</form>

	<?php if($selectedScaleId): ?>

		<h2>Questions by Domain</h2>
		<?php if($questionsByDomain->isEmpty()): ?>
			<p class="muted">No questions found for this scale version.</p>
		<?php else: ?>
			<?php $__currentLoopData = $questionsByDomain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domainName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<div class="domain-section">
					<h3><?php echo e($domainName); ?></h3>
					<table>
						<thead>
						<tr>
							<th style="width:60px;">Order</th>
							<th>Question</th>
							<th>Display Text</th>
							<th style="width:90px;">Type</th>
						</tr>
						</thead>
						<tbody>
						<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($q->question_order); ?></td>
								<td><?php echo e($q->question_text); ?></td>
								<td><?php echo e($q->question_display_text ?? '-'); ?></td>
								<td><?php echo e($q->question_type); ?></td>
							</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<?php endif; ?>

		<h2>Scale Lookup Data for Selected Version</h2>

		<h3>Domain Scaled Scores (raw → scaled per age range)</h3>
		<?php if($domainScaledScores->isEmpty()): ?>
			<p class="muted">No domain scaled scores for this scale version.</p>
		<?php else: ?>
			<table>
				<thead>
				<tr>
					<th>Domain</th>
					<th>Age Min (months)</th>
					<th>Age Max (months)</th>
					<th>Raw Min</th>
					<th>Raw Max</th>
					<th>Scaled Score</th>
				</tr>
				</thead>
				<tbody>
				<?php $__currentLoopData = $domainScaledScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($row->domain_name); ?></td>
						<td><?php echo e($row->age_min); ?></td>
						<td><?php echo e($row->age_max); ?></td>
						<td><?php echo e($row->raw_min); ?></td>
						<td><?php echo e($row->raw_max); ?></td>
						<td><?php echo e($row->scaled_score); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h3>Standard Score Scales (sum of scaled → standard score)</h3>
		<?php if($standardScoreScales->isEmpty()): ?>
			<p class="muted">No standard score scales for this scale version.</p>
		<?php else: ?>
			<table>
				<thead>
				<tr>
					<th>Sum Scaled Min</th>
					<th>Sum Scaled Max</th>
					<th>Standard Score</th>
				</tr>
				</thead>
				<tbody>
				<?php $__currentLoopData = $standardScoreScales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($row->sum_scaled_min); ?></td>
						<td><?php echo e($row->sum_scaled_max); ?></td>
						<td><?php echo e($row->standard_score); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h3>Test Domain Scores (per test and domain)</h3>
		<?php if($testDomainScores->isEmpty()): ?>
			<p class="muted">No test domain scores for this scale version.</p>
		<?php else: ?>
			<table>
				<thead>
				<tr>
					<th>Test ID</th>
					<th>Student</th>
					<th>Domain</th>
					<th>Raw Score</th>
					<th>Scaled Score</th>
				</tr>
				</thead>
				<tbody>
				<?php $__currentLoopData = $testDomainScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($row->test_id); ?></td>
						<td><?php echo e($row->student_name); ?></td>
						<td><?php echo e($row->domain_name); ?></td>
						<td><?php echo e($row->raw_score); ?></td>
						<td><?php echo e($row->scaled_score); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h3>Test Standard Scores (per test)</h3>
		<?php if($testStandardScores->isEmpty()): ?>
			<p class="muted">No test standard scores for this scale version.</p>
		<?php else: ?>
			<table>
				<thead>
				<tr>
					<th>Test ID</th>
					<th>Student</th>
					<th>Sum of Scaled Scores</th>
					<th>Standard Score</th>
					<th>Interpretation</th>
				</tr>
				</thead>
				<tbody>
				<?php $__currentLoopData = $testStandardScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($row->test_id); ?></td>
						<td><?php echo e($row->student_name); ?></td>
						<td><?php echo e($row->sum_scaled_scores); ?></td>
						<td><?php echo e($row->standard_score); ?></td>
						<td><?php echo e($row->interpretation); ?></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php else: ?>
		<p class="muted">Select a scale version above to view its questions and related data.</p>
	<?php endif; ?>

</body>
</html>

<?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views/admin/eccd.blade.php ENDPATH**/ ?>