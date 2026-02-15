

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Help & Resources</h1>
</div>

<div class="row g-4">
  <!-- How Scoring Works -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">How Scoring Works</h5>
      </div>
      <div class="card-body">
        <p>The KiddieCheck assessment uses a multi-step scoring process:</p>
        <ol>
          <li><strong>Raw Score:</strong> Each question is scored as "Yes" or "No". The raw score for each domain is the total number of "Yes" responses.</li>
          <li><strong>Scaled Score:</strong> Raw scores are converted to scaled scores based on the child's age. This allows comparison across different age groups.</li>
          <li><strong>Sum of Scaled Scores:</strong> All domain scaled scores are added together.</li>
          <li><strong>Standard Score:</strong> The sum of scaled scores is converted to a standard score using an age-appropriate conversion table.</li>
          <li><strong>Interpretation:</strong> The standard score is interpreted based on the child's age group (e.g., "Average Development", "Advanced Development").</li>
          <li><strong>Period Comparison:</strong> If multiple teachers assess the same child, their scores are averaged and compared for consistency.</li>
          <li><strong>Weighted Final Score:</strong> If both teacher and family assessments are completed, the final score is calculated as: <br>
            <code>(Teacher Score × 70%) + (Family Score × 30%)</code>
          </li>
        </ol>
      </div>
    </div>
  </div>

  <!-- Understanding Discrepancies -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Understanding Discrepancies</h5>
      </div>
      <div class="card-body">
        <p>Discrepancies indicate differences in assessment scores between observers:</p>

        <h6 class="mt-3">Teacher Discrepancy</h6>
        <p>Compares scores from different teachers assessing the same child:</p>
        <ul>
          <li><strong>None:</strong> Teachers' scores align well (difference ≤ 5 points)</li>
          <li><strong>Minor:</strong> Small differences in assessment (6-10 points difference)</li>
          <li><strong>Major:</strong> Significant differences (11+ points difference)</li>
        </ul>

        <h6 class="mt-3">Teacher-Family Discrepancy</h6>
        <p>Compares the average teacher score with the family's assessment:</p>
        <ul>
          <li><strong>None:</strong> Teacher and family assessments align (difference ≤ 5 points)</li>
          <li><strong>Minor:</strong> Small differences between teacher and family views (6-10 points)</li>
          <li><strong>Major:</strong> Significant differences (11+ points) - may warrant further investigation</li>
        </ul>

        <div class="alert alert-info mt-3">
          <strong>Tip:</strong> Significant discrepancies may indicate the child performs differently in different settings or that additional observation is needed.
        </div>
      </div>
    </div>
  </div>

  <!-- Assessment Flow -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Assessment Flow</h5>
      </div>
      <div class="card-body">
        <p><strong>Overview:</strong> Each child has 3 assessment periods (typically at enrollment, 6 months, and 12 months).</p>

        <h6 class="mt-3">For Each Assessment Period:</h6>
        <ol>
          <li><strong>Preparation:</strong> Review the child's information and previous assessments (if any).</li>
          <li><strong>Start Test:</strong> Begin the assessment by selecting the assessment period.
            <ul>
              <li>Test status becomes "In Progress"</li>
              <li>You can pause the test if needed</li>
            </ul>
          </li>
          <li><strong>Answer Questions:</strong> Go through each domain's questions one by one.
            <ul>
              <li>Mark each question as "Yes" or "No"</li>
              <li>Add notes if needed</li>
              <li>You can scroll back to review previous answers</li>
            </ul>
          </li>
          <li><strong>Complete Test:</strong> After all questions are answered:
            <ul>
              <li>Test status becomes "Completed"</li>
              <li>Scores are automatically calculated</li>
              <li>You can still cancel at this point if needed</li>
            </ul>
          </li>
          <li><strong>Finalize Test:</strong> Click "Finalize" to lock the test.
            <ul>
              <li>Test status becomes "Finalized"</li>
              <li>No changes can be made after finalization</li>
              <li>The test is counted toward the assessment period</li>
            </ul>
          </li>
          <li><strong>Review Results:</strong> Access the detailed report in the Reports section.</li>
        </ol>

        <div class="alert alert-warning mt-3">
          <strong>Important:</strong> Once a test is finalized, it cannot be changed. Make sure all answers are correct before finalizing.
        </div>
      </div>
    </div>
  </div>

  <!-- Test Status States -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Test Status States</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="p-3" style="background-color: #e3f2fd; border-radius: 8px;">
              <h6>In Progress</h6>
              <p class="small mb-0">Test is currently being taken. Can be paused or canceled.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3" style="background-color: #fff3e0; border-radius: 8px;">
              <h6>Completed</h6>
              <p class="small mb-0">All questions answered. Scores calculated. Can still be canceled before finalizing.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3" style="background-color: #e8f5e9; border-radius: 8px;">
              <h6>Finalized</h6>
              <p class="small mb-0">Test is locked and counts toward the period. Cannot be changed.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Frequently Asked Questions -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Frequently Asked Questions</h5>
      </div>
      <div class="card-body">
        <div class="accordion" id="faqAccordion">
          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                Can I go back and change my answers?
              </button>
            </h6>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Yes, you can scroll through and change answers while the test is in progress or completed status. However, once you finalize the test, no changes are allowed.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                What should I do if I need to pause the test?
              </button>
            </h6>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                You can pause the test at any time, and your progress will be saved. The test will remain in "In Progress" status and can be resumed later.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                Is there a time limit for completing an assessment?
              </button>
            </h6>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                There is no strict time limit, but assessments should be completed within the specified assessment period dates. You can pause and resume as needed.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                What does a "major discrepancy" mean?
              </button>
            </h6>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                A major discrepancy indicates a significant difference (11+ points) between assessments. This could mean the child performs very differently in different settings or with different observers. It may warrant additional investigation or re-assessment.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                How often should a child be assessed?
              </button>
            </h6>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Each child should have 3 assessment periods: at enrollment, 6 months after enrollment, and 12 months after enrollment. Students can be re-tested after 6 months from their last completed test.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                Can a test be canceled after it's finalized?
              </button>
            </h6>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                No, finalized tests cannot be canceled or modified. If you need to redo an assessment, you must contact an administrator.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact & Support -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Need Help?</h5>
      </div>
      <div class="card-body">
        <p>If you encounter any issues or have questions not covered in this help section:</p>
        <ul>
          <li>Contact your school administrator</li>
          <li>Refer to the assessment manual for detailed guidance</li>
          <li>Email support@kiddiecheck.com for technical issues</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
  }
  .card-header {
    border-bottom: 2px solid rgba(231, 122, 116, 0.3);
  }
  .accordion-button:not(.collapsed) {
    background-color: rgba(231, 122, 116, 0.1);
    color: #333;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\help.blade.php ENDPATH**/ ?>