@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <h3>Help & Guidance</h3>
      <p class="text-muted">Learn how the assessment system works</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <!-- How Scoring Works -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">How Scoring Works</h5>
        </div>
        <div class="card-body">
          <p>The assessment system uses a multi-step scoring process:</p>
          <ol>
            <li><strong>Raw Scores:</strong> Based on yes/no responses to questions in each domain</li>
            <li><strong>Scaled Scores:</strong> Raw scores are converted to scaled scores based on the child's age</li>
            <li><strong>Standard Score:</strong> All scaled scores are summed to create a composite standard score</li>
            <li><strong>Interpretation:</strong> Standard score is converted to a developmental interpretation</li>
          </ol>
        </div>
      </div>

      <!-- What Discrepancies Mean -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Understanding Discrepancies</h5>
        </div>
        <div class="card-body">
          <p>Discrepancies occur when there are differences between assessments:</p>
          <ul>
            <li><strong>No Discrepancy:</strong> Scores are consistent across assessments</li>
            <li><strong>Minor Discrepancy:</strong> Small differences in scores (< 10 points)</li>
            <li><strong>Major Discrepancy:</strong> Significant differences in scores (≥ 10 points)</li>
          </ul>
          <p class="text-muted mb-0">Discrepancies may indicate different testing environments or child behavior changes</p>
        </div>
      </div>

      <!-- Assessment Flow -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Assessment Flow</h5>
        </div>
        <div class="card-body">
          <p>The assessment process follows these steps:</p>
          <ol>
            <li><strong>Enrollment:</strong> Student is enrolled and assigned to a teacher</li>
            <li><strong>Assessment Periods:</strong> Three periods are automatically created (6 months apart)</li>
            <li><strong>Teacher Test:</strong> Conduct assessments during scheduled periods</li>
            <li><strong>Family Test:</strong> Family may conduct complementary assessments</li>
            <li><strong>Finalization:</strong> Results are finalized after test completion</li>
            <li><strong>Reporting:</strong> Combined scores and interpretations are generated</li>
          </ol>
        </div>
      </div>

      <!-- FAQ -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Frequently Asked Questions</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Q: When can I start a test?</strong>
            <p class="mb-0 text-muted">A: Only during active assessment periods. A student is eligible if at least 6 months have passed since the last finalized test.</p>
          </div>
          <div class="mb-3">
            <strong>Q: What if I make a mistake during testing?</strong>
            <p class="mb-0 text-muted">A: You can pause and save your progress, then continue later. If needed, you can cancel the test and start over.</p>
          </div>
          <div class="mb-3">
            <strong>Q: What's the difference between completed and finalized?</strong>
            <p class="mb-0 text-muted">A: Completed means you've finished answering all questions. Finalized means the test is locked and scores are calculated.</p>
          </div>
          <div class="mb-3">
            <strong>Q: Can I view reports for incomplete tests?</strong>
            <p class="mb-0 text-muted">A: No, only finalized tests appear in reports.</p>
          </div>
          <div>
            <strong>Q: How are weighted averages calculated?</strong>
            <p class="mb-0 text-muted">A: Final score = (Teacher Score × 70%) + (Family Score × 30%)</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
