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
			@foreach ($scaleVersions as $sv)
				<option value="{{ $sv->id }}" {{ (string)$sv->id === (string)$selectedScaleId ? 'selected' : '' }}>
					{{ $sv->name }}
				</option>
			@endforeach
		</select>
		<button type="submit">Load</button>
	</form>

	@if ($selectedScaleId)

		<h2>Questions by Domain</h2>
		@if ($questionsByDomain->isEmpty())
			<p class="muted">No questions found for this scale version.</p>
		@else
			@foreach ($questionsByDomain as $domainName => $items)
				<div class="domain-section">
					<h3>{{ $domainName }}</h3>
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
						@foreach ($items as $q)
							<tr>
								<td>{{ $q->question_order }}</td>
								<td>{{ $q->question_text }}</td>
								<td>{{ $q->question_display_text ?? '-' }}</td>
								<td>{{ $q->question_type }}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			@endforeach
		@endif

		<h2>Scale Lookup Data for Selected Version</h2>

		<h3>Domain Scaled Scores (raw → scaled per age range)</h3>
		@if ($domainScaledScores->isEmpty())
			<p class="muted">No domain scaled scores for this scale version.</p>
		@else
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
				@foreach ($domainScaledScores as $row)
					<tr>
						<td>{{ $row->domain_name }}</td>
						<td>{{ $row->age_min }}</td>
						<td>{{ $row->age_max }}</td>
						<td>{{ $row->raw_min }}</td>
						<td>{{ $row->raw_max }}</td>
						<td>{{ $row->scaled_score }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif

		<h3>Standard Score Scales (sum of scaled → standard score)</h3>
		@if ($standardScoreScales->isEmpty())
			<p class="muted">No standard score scales for this scale version.</p>
		@else
			<table>
				<thead>
				<tr>
					<th>Sum Scaled Min</th>
					<th>Sum Scaled Max</th>
					<th>Standard Score</th>
				</tr>
				</thead>
				<tbody>
				@foreach ($standardScoreScales as $row)
					<tr>
						<td>{{ $row->sum_scaled_min }}</td>
						<td>{{ $row->sum_scaled_max }}</td>
						<td>{{ $row->standard_score }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif

		<h3>Test Domain Scores (per test and domain)</h3>
		@if ($testDomainScores->isEmpty())
			<p class="muted">No test domain scores for this scale version.</p>
		@else
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
				@foreach ($testDomainScores as $row)
					<tr>
						<td>{{ $row->test_id }}</td>
						<td>{{ $row->student_name }}</td>
						<td>{{ $row->domain_name }}</td>
						<td>{{ $row->raw_score }}</td>
						<td>{{ $row->scaled_score }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif

		<h3>Test Standard Scores (per test)</h3>
		@if ($testStandardScores->isEmpty())
			<p class="muted">No test standard scores for this scale version.</p>
		@else
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
				@foreach ($testStandardScores as $row)
					<tr>
						<td>{{ $row->test_id }}</td>
						<td>{{ $row->student_name }}</td>
						<td>{{ $row->sum_scaled_scores }}</td>
						<td>{{ $row->standard_score }}</td>
						<td>{{ $row->interpretation }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		@endif

	@else
		<p class="muted">Select a scale version above to view its questions and related data.</p>
	@endif

</body>
</html>

