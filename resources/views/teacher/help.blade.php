@extends('teacher.layout')

@section('content')

<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Help & Resources</h1>
</div>

<div class="row g-4">

  <!-- How Scoring Works -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-violet">
        <div class="section-icon si-violet">⭐</div>
        <h5 class="mb-0">How Scoring Works</h5>
      </div>
      <div class="card-body">
        <p class="body-lead">The KiddieCheck assessment uses a multi-step scoring process:</p>
        <div class="steps">
          <div class="step">
            <div class="step-num">1</div>
            <div class="step-content">
              <strong>Raw Score</strong>
              <span>Each question is scored as "Yes" or "No". The raw score for each domain is the total number of "Yes" responses.</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">2</div>
            <div class="step-content">
              <strong>Scaled Score</strong>
              <span>Raw scores are converted to scaled scores based on the child's age, allowing fair comparison across different age groups.</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <div class="step-content">
              <strong>Sum of Scaled Scores</strong>
              <span>All domain scaled scores are added together into one total.</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">4</div>
            <div class="step-content">
              <strong>Standard Score</strong>
              <span>The sum is converted to a standard score using an age-appropriate conversion table.</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">5</div>
            <div class="step-content">
              <strong>Interpretation</strong>
              <span>The standard score is interpreted based on the child's age group (e.g., "Average Development", "Advanced Development").</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">6</div>
            <div class="step-content">
              <strong>Period Comparison</strong>
              <span>If multiple teachers assess the same child, their scores are averaged and compared for consistency.</span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">7</div>
            <div class="step-content">
              <strong>Weighted Final Score</strong>
              <span>When both teacher and family assessments are completed: <code>(Teacher Score × 70%) + (Family Score × 30%)</code></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Understanding Discrepancies -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-orange">
        <div class="section-icon si-orange">🔍</div>
        <h5 class="mb-0">Understanding Discrepancies</h5>
      </div>
      <div class="card-body">
        <p class="body-lead">Discrepancies indicate differences in assessment scores between observers:</p>

        <div class="subsection-title mt-3">Teacher Discrepancy</div>
        <p class="body-sub">Compares scores from different teachers assessing the same child:</p>
        <div class="disc-grid">
          <div class="disc-item">
            <span class="disc-badge badge-none">None</span>
            <div><strong>≤ 5 points difference</strong><br><span class="body-sub">Teachers' scores align well.</span></div>
          </div>
          <div class="disc-item">
            <span class="disc-badge badge-minor">Minor</span>
            <div><strong>6–10 points difference</strong><br><span class="body-sub">Small differences in assessment.</span></div>
          </div>
          <div class="disc-item">
            <span class="disc-badge badge-major">Major</span>
            <div><strong>11+ points difference</strong><br><span class="body-sub">Significant differences noted.</span></div>
          </div>
        </div>

        <div class="subsection-title mt-4">Teacher–Family Discrepancy</div>
        <p class="body-sub">Compares the average teacher score with the family's assessment:</p>
        <div class="disc-grid">
          <div class="disc-item">
            <span class="disc-badge badge-none">None</span>
            <div><strong>≤ 5 points difference</strong><br><span class="body-sub">Teacher and family assessments align.</span></div>
          </div>
          <div class="disc-item">
            <span class="disc-badge badge-minor">Minor</span>
            <div><strong>6–10 points difference</strong><br><span class="body-sub">Small differences between teacher and family views.</span></div>
          </div>
          <div class="disc-item">
            <span class="disc-badge badge-major">Major</span>
            <div><strong>11+ points difference</strong><br><span class="body-sub">May warrant further investigation.</span></div>
          </div>
        </div>

        <div class="help-alert alert-info-custom mt-3">
          <span class="alert-icon">💡</span>
          <span><strong>Tip:</strong> Significant discrepancies may indicate the child performs differently in different settings or that additional observation is needed.</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Assessment Flow -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-teal">
        <div class="section-icon si-teal">📋</div>
        <h5 class="mb-0">Assessment Flow</h5>
      </div>
      <div class="card-body">
        <p class="body-lead"><strong>Overview:</strong> Each child has 3 assessment periods — typically at enrollment, 6 months, and 12 months.</p>
        <div class="subsection-title mt-3">For Each Assessment Period:</div>
        <div class="steps mt-2">
          <div class="step">
            <div class="step-num">1</div>
            <div class="step-content"><strong>Preparation</strong><span>Review the child's information and previous assessments (if any).</span></div>
          </div>
          <div class="step">
            <div class="step-num">2</div>
            <div class="step-content"><strong>Start Test</strong><span>Begin by selecting the assessment period. Status becomes "In Progress" and you can pause anytime.</span></div>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <div class="step-content"><strong>Answer Questions</strong><span>Go through each domain, marking "Yes" or "No". Add notes if needed and scroll back to review.</span></div>
          </div>
          <div class="step">
            <div class="step-num">4</div>
            <div class="step-content"><strong>Complete Test</strong><span>After all questions are answered, scores are automatically calculated. You can still cancel at this point.</span></div>
          </div>
          <div class="step">
            <div class="step-num">5</div>
            <div class="step-content"><strong>Finalize Test</strong><span>Click "Finalize" to lock the test. Status becomes "Finalized" and no changes can be made.</span></div>
          </div>
          <div class="step">
            <div class="step-num">6</div>
            <div class="step-content"><strong>Review Results</strong><span>Access the detailed report in the Reports section.</span></div>
          </div>
        </div>
        <div class="help-alert alert-warning-custom mt-3">
          <span class="alert-icon">⚠️</span>
          <span><strong>Important:</strong> Once a test is finalized, it cannot be changed. Make sure all answers are correct before finalizing.</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Test Status States -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-sky">
        <div class="section-icon si-sky">🏷️</div>
        <h5 class="mb-0">Test Status States</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="status-card s-blue">
              <div class="s-icon">🔄</div>
              <h6>In Progress</h6>
              <p class="small mb-0">Test is currently being taken. Can be paused or canceled.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="status-card s-lemon">
              <div class="s-icon">✅</div>
              <h6>Completed</h6>
              <p class="small mb-0">All questions answered. Scores calculated. Can still be canceled before finalizing.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="status-card s-mint">
              <div class="s-icon">🔒</div>
              <h6>Finalized</h6>
              <p class="small mb-0">Test is locked and counts toward the period. Cannot be changed.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-lemon">
        <div class="section-icon si-lemon">❓</div>
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
              <div class="accordion-body">Yes, you can scroll through and change answers while the test is in progress or completed status. However, once you finalize the test, no changes are allowed.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                What should I do if I need to pause the test?
              </button>
            </h6>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">You can pause the test at any time and your progress will be saved. The test will remain "In Progress" and can be resumed later.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                Is there a time limit for completing an assessment?
              </button>
            </h6>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">There is no strict time limit, but assessments should be completed within the specified assessment period dates. You can pause and resume as needed.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                What does a "major discrepancy" mean?
              </button>
            </h6>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">A major discrepancy (11+ points) means the child performs very differently across settings or observers. It may warrant additional investigation or re-assessment.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                How often should a child be assessed?
              </button>
            </h6>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">Each child has 3 assessment periods: at enrollment, 6 months after, and 12 months after. Students can be re-tested after 6 months from their last completed test.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h6 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                Can a test be canceled after it's finalized?
              </button>
            </h6>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">No, finalized tests cannot be canceled or modified. If you need to redo an assessment, you must contact an administrator.</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Contact & Support -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-coral">
        <div class="section-icon si-coral">💬</div>
        <h5 class="mb-0">Need Help?</h5>
      </div>
      <div class="card-body">
        <p class="body-lead">If you encounter any issues or have questions not covered here:</p>
        <div class="contact-grid">
          <div class="contact-item">
            <div class="contact-icon">🏫</div>
            <div class="contact-label">School Administrator</div>
            <div class="contact-sub">Contact your school for assessment-specific questions.</div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">📖</div>
            <div class="contact-label">Assessment Manual</div>
            <div class="contact-sub">Refer to the official manual for detailed domain guidance.</div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">📧</div>
            <div class="contact-label">Email Support</div>
            <div class="contact-sub">support@kiddiecheck.com for technical issues.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --teal:        #2EC4B6;
  --teal-soft:   #C8F4F1;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --lemon:       #F9C74F;
  --lemon-soft:  #FFF6CC;
  --sky:         #4EA8DE;
  --sky-soft:    #D6EEFF;
  --orange:      #FF9A76;
  --orange-soft: #FFE8D6;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── CARDS ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  margin-bottom: 0;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
  transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(100,60,160,0.13) !important; }
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
.col-md-12:nth-child(1) .card { animation-delay: 0.05s; }
.col-md-12:nth-child(2) .card { animation-delay: 0.10s; }
.col-md-12:nth-child(3) .card { animation-delay: 0.15s; }
.col-md-12:nth-child(4) .card { animation-delay: 0.20s; }
.col-md-12:nth-child(5) .card { animation-delay: 0.25s; }
.col-md-12:nth-child(6) .card { animation-delay: 0.30s; }

/* ── CARD HEADERS ── */
.card-header {
  padding: 14px 20px !important;
  display: flex; align-items: center; gap: 10px;
  border-bottom: 2px solid #F0E8FF !important;
}
.card-header h5 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important; font-weight: 700 !important;
  color: var(--text) !important; margin-bottom: 0 !important;
}
.header-violet { background: var(--violet-bg) !important;   border-left: 4px solid var(--violet) !important; }
.header-orange { background: var(--orange-soft) !important; border-left: 4px solid var(--orange) !important; }
.header-teal   { background: var(--teal-soft) !important;   border-left: 4px solid var(--teal)   !important; }
.header-sky    { background: var(--sky-soft) !important;    border-left: 4px solid var(--sky)    !important; }
.header-lemon  { background: var(--lemon-soft) !important;  border-left: 4px solid var(--lemon)  !important; }
.header-coral  { background: var(--coral-soft) !important;  border-left: 4px solid var(--coral)  !important; }

.section-icon {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; flex-shrink: 0;
}
.si-violet { background: var(--violet-soft); }
.si-orange { background: var(--orange-soft); }
.si-teal   { background: var(--teal-soft);   }
.si-sky    { background: var(--sky-soft);     }
.si-lemon  { background: var(--lemon-soft);   }
.si-coral  { background: var(--coral-soft);   }

/* ── BODY TEXT ── */
.card-body { padding: 20px 22px 24px !important; }
.body-lead { font-size: 0.9rem; font-weight: 600; color: var(--text); line-height: 1.7; margin-bottom: 12px; }
.body-sub  { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; line-height: 1.6; }
.subsection-title { font-family: 'Baloo 2', cursive; font-size: 0.88rem; font-weight: 800; color: var(--violet); margin-bottom: 6px; }

/* ── STEPS ── */
.steps { display: flex; flex-direction: column; gap: 11px; }
.step  { display: flex; gap: 13px; align-items: flex-start; }
.step-num {
  width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  color: white; font-weight: 900; font-size: 0.75rem;
  display: flex; align-items: center; justify-content: center; margin-top: 1px;
}
.step-content { flex: 1; }
.step-content strong { display: block; font-size: 0.88rem; color: var(--text); font-weight: 800; margin-bottom: 1px; }
.step-content span   { font-size: 0.83rem; color: var(--text-muted); font-weight: 600; line-height: 1.6; }

code {
  background: var(--violet-soft); color: var(--violet);
  font-size: 0.82rem; padding: 2px 8px; border-radius: 6px;
  font-family: monospace; font-weight: 700;
}

/* ── DISCREPANCY GRID ── */
.disc-grid { display: flex; flex-direction: column; gap: 8px; }
.disc-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 11px 14px; border-radius: 10px;
  background: #FAFAFA; border: 1.5px solid #F0E8FF;
  font-size: 0.85rem;
}
.disc-badge {
  padding: 3px 12px; border-radius: 20px;
  font-size: 0.72rem; font-weight: 800; flex-shrink: 0; margin-top: 2px;
}
.badge-none  { background: var(--mint-soft);   color: #2a7a50; }
.badge-minor { background: var(--lemon-soft);  color: #9a6800; }
.badge-major { background: var(--coral-soft);  color: #c0294a; }

/* ── ALERTS ── */
.help-alert {
  border-radius: 10px; padding: 11px 15px;
  font-size: 0.86rem; font-weight: 600; line-height: 1.6;
  display: flex; gap: 10px; align-items: flex-start;
}
.alert-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.alert-info-custom    { background: var(--sky-soft);   border-left: 4px solid var(--sky);   color: #1a508a; }
.alert-warning-custom { background: var(--lemon-soft); border-left: 4px solid var(--lemon); color: #7a5800; }

/* ── STATUS CARDS ── */
.status-card {
  padding: 16px; border-radius: 12px; text-align: center;
}
.s-icon { font-size: 1.6rem; margin-bottom: 6px; }
.status-card h6 { font-family: 'Baloo 2', cursive; font-size: 0.9rem; font-weight: 800; margin-bottom: 5px; color: var(--text); }
.status-card p  { font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; }
.s-blue  { background: var(--sky-soft);    border: 1.5px solid #A8D4F5; }
.s-lemon { background: var(--lemon-soft);  border: 1.5px solid #F5D87A; }
.s-mint  { background: var(--mint-soft);   border: 1.5px solid #88D4A8; }

/* ── FAQ ACCORDION ── */
.accordion-item {
  border: 1.5px solid #F0E8FF !important;
  border-radius: 10px !important;
  margin-bottom: 7px;
  overflow: hidden;
}
.accordion-button {
  font-family: 'Nunito', sans-serif !important;
  font-size: 0.88rem !important; font-weight: 800 !important;
  color: var(--text) !important;
  background: white !important;
  box-shadow: none !important;
  padding: 13px 18px !important;
}
.accordion-button:not(.collapsed) {
  background: var(--violet-bg) !important;
  color: var(--violet) !important;
}
.accordion-button::after {
  filter: hue-rotate(200deg);
}
.accordion-body {
  font-size: 0.86rem; color: var(--text-muted);
  font-weight: 600; line-height: 1.7;
  padding: 12px 18px 14px !important;
  background: white;
}

/* ── CONTACT GRID ── */
.contact-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
}
.contact-item {
  background: #FAFAFA; border: 1.5px dashed #F0D0DC;
  border-radius: 12px; padding: 16px; text-align: center;
}
.contact-icon  { font-size: 1.8rem; margin-bottom: 7px; }
.contact-label { font-family: 'Baloo 2', cursive; font-size: 0.88rem; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.contact-sub   { font-size: 0.77rem; color: var(--text-muted); font-weight: 600; }

@media (max-width: 600px) {
  .contact-grid { grid-template-columns: 1fr; }
}
</style>

@endsection