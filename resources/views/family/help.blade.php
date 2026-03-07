@extends('family.layout')

@section('title', 'Help & Resources')

@section('content')
<style>
  :root {
    --pink:       #F472A0;
    --pink-soft:  #FFD6E7;
    --pink-bg:    #FFF0F5;
    --green:      #4CAF7D;
    --green-soft: #D4F5E4;
    --yellow:     #F5C842;
    --yellow-soft:#FFF5CC;
    --blue:       #5BAAEE;
    --blue-soft:  #D6EEFF;
    --orange:     #FF8C42;
    --orange-soft:#FFE5CC;
    --text:       #3D2B2B;
    --text-muted: #8A6A6A;
    --radius:     18px;
    --shadow:     0 4px 20px rgba(200,100,130,0.12);
  }

  /* ── HERO BANNER ── */
  .help-hero {
    background: linear-gradient(135deg, #FFD6E7 0%, #FFF5CC 60%, #D4F5E4 100%);
    padding: 40px 40px 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-bottom: 3px dashed rgba(244, 114, 160, 0.25);
    margin: -2rem -20px 0;
  }
  .help-hero-emoji { font-size: 3.2rem; }
  .help-hero h1 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    font-weight: 800;
    color: var(--text);
  }
  .help-hero p {
    color: var(--text-muted);
    font-size: 1rem;
    font-weight: 600;
    margin-top: 4px;
  }

  /* ── MAIN LAYOUT ── */
  .help-container {
    max-width: 880px;
    margin: 0 auto;
    padding: 36px 0 60px;
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  /* ── CARDS ── */
  .help-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    animation: helpFadeUp 0.4s ease both;
  }
  @keyframes helpFadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .help-card:nth-child(1) { animation-delay: 0.05s; }
  .help-card:nth-child(2) { animation-delay: 0.10s; }
  .help-card:nth-child(3) { animation-delay: 0.15s; }
  .help-card:nth-child(4) { animation-delay: 0.20s; }
  .help-card:nth-child(5) { animation-delay: 0.25s; }
  .help-card:nth-child(6) { animation-delay: 0.30s; }

  .help-card-header {
    padding: 16px 22px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Baloo 2', cursive;
    font-size: 1.05rem;
    font-weight: 700;
  }
  .help-card-header .hicon {
    font-size: 1.3rem;
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
  }
  .hh-pink   { background: var(--pink-bg);    border-left: 4px solid var(--pink);   }
  .hh-green  { background: var(--green-soft); border-left: 4px solid var(--green);  }
  .hh-yellow { background: var(--yellow-soft);border-left: 4px solid var(--yellow); }
  .hh-blue   { background: var(--blue-soft);  border-left: 4px solid var(--blue);   }
  .hh-orange { background: var(--orange-soft);border-left: 4px solid var(--orange); }

  .hi-pink   { background: var(--pink-soft);  }
  .hi-green  { background: var(--green-soft); }
  .hi-yellow { background: var(--yellow-soft);}
  .hi-blue   { background: var(--blue-soft);  }
  .hi-orange { background: var(--orange-soft);}

  .help-card-body { padding: 20px 22px 24px; }
  .help-card-body p {
    font-size: 0.93rem;
    color: var(--text-muted);
    line-height: 1.7;
    margin-bottom: 12px;
  }

  /* ── NUMBERED STEPS ── */
  .help-steps { display: flex; flex-direction: column; gap: 12px; }
  .help-step  { display: flex; gap: 14px; align-items: flex-start; }
  .help-step-num {
    width: 28px; height: 28px; flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--pink), var(--orange));
    color: white; font-weight: 900; font-size: 0.8rem;
    display: flex; align-items: center; justify-content: center;
    margin-top: 1px;
  }
  .help-step-content { flex: 1; }
  .help-step-content strong {
    display: block; font-size: 0.93rem; color: var(--text);
    font-weight: 800; margin-bottom: 2px;
  }
  .help-step-content span {
    font-size: 0.875rem; color: var(--text-muted); line-height: 1.6;
  }

  /* ── DISCREPANCY ── */
  .disc-grid  { display: flex; flex-direction: column; gap: 10px; }
  .disc-item  {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 16px; border-radius: 12px;
    background: #FAFAFA; border: 1.5px solid #F0E0E8;
  }
  .disc-badge {
    padding: 3px 12px; border-radius: 20px;
    font-size: 0.75rem; font-weight: 800; flex-shrink: 0; margin-top: 2px;
  }
  .badge-none  { background: var(--green-soft);  color: var(--green); }
  .badge-minor { background: var(--yellow-soft); color: #B8900A; }
  .badge-major { background: #FFD6D6;            color: #C0392B; }
  .disc-text strong { font-size: 0.88rem; color: var(--text); font-weight: 800; }
  .disc-text span   { font-size: 0.83rem; color: var(--text-muted); }

  /* ── STATUS GRID ── */
  .status-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
  .status-card { padding: 14px 16px; border-radius: 14px; text-align: center; }
  .status-card .s-icon { font-size: 1.6rem; margin-bottom: 6px; }
  .status-card h6 { font-size: 0.88rem; font-weight: 800; margin-bottom: 4px; }
  .status-card p  { font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; }
  .s-blue   { background: var(--blue-soft);    border: 1.5px solid #A8D4F5; }
  .s-yellow { background: var(--yellow-soft);  border: 1.5px solid #F5D87A; }
  .s-green  { background: var(--green-soft);   border: 1.5px solid #88D4A8; }

  /* ── ALERT BOXES ── */
  .help-alert {
    border-radius: 12px; padding: 12px 16px;
    font-size: 0.875rem; line-height: 1.6; margin-top: 16px;
    display: flex; gap: 10px; align-items: flex-start;
  }
  .help-alert-info    { background: var(--blue-soft);   border-left: 4px solid var(--blue);   color: #1a5f8a; }
  .help-alert-warning { background: var(--yellow-soft); border-left: 4px solid var(--yellow); color: #7a5800; }
  .help-alert .aicon  { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }

  /* ── FAQ ── */
  .faq-item { border: 1.5px solid #F0E0E8; border-radius: 12px; margin-bottom: 8px; overflow: hidden; }
  .faq-question {
    width: 100%; background: white; border: none;
    padding: 14px 18px; text-align: left;
    font-size: 0.9rem; font-weight: 800; color: var(--text);
    cursor: pointer; display: flex; justify-content: space-between;
    align-items: center; transition: background 0.2s;
  }
  .faq-question:hover { background: var(--pink-bg); }
  .faq-question.open  { background: var(--pink-bg); color: var(--pink); }
  .faq-arrow { font-size: 1rem; transition: transform 0.3s; }
  .faq-question.open .faq-arrow { transform: rotate(180deg); }
  .faq-answer {
    max-height: 0; overflow: hidden;
    transition: max-height 0.35s ease, padding 0.2s;
    padding: 0 18px; font-size: 0.875rem;
    color: var(--text-muted); line-height: 1.7; background: white;
  }
  .faq-answer.open { max-height: 200px; padding: 0 18px 14px; }

  /* ── CONTACT ── */
  .contact-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; }
  .contact-item {
    background: #FAFAFA; border: 1.5px dashed #F0D0DC;
    border-radius: 14px; padding: 16px; text-align: center;
  }
  .contact-item .c-icon { font-size: 1.8rem; margin-bottom: 8px; }
  .contact-item h6 { font-size: 0.82rem; font-weight: 800; color: var(--text); margin-bottom: 4px; }
  .contact-item p  { font-size: 0.78rem; color: var(--text-muted); }

  .help-code {
    background: var(--pink-soft); color: var(--pink);
    font-size: 0.82rem; padding: 2px 8px; border-radius: 6px;
    font-family: monospace; font-weight: 700;
  }

  @media (max-width: 600px) {
    .help-hero   { padding: 24px 18px; margin: -2rem -14px 0; }
    .status-grid, .contact-grid { grid-template-columns: 1fr; }
  }
</style>

{{-- HERO --}}
<div class="help-hero">
  <div class="help-hero-emoji">🌟</div>
  <div>
    <h1>Help &amp; Resources</h1>
    <p>Everything you need to guide your child's assessment journey 🌱</p>
  </div>
</div>

<div class="help-container">

  {{-- HOW SCORING WORKS --}}
  <div class="help-card">
    <div class="help-card-header hh-pink">
      <div class="hicon hi-pink">⭐</div>
      How Scoring Works
    </div>
    <div class="help-card-body">
      <p>The KiddieCheck assessment uses a multi-step scoring process to evaluate your child's development:</p>
      <div class="help-steps">
        <div class="help-step">
          <div class="help-step-num">1</div>
          <div class="help-step-content">
            <strong>Raw Score</strong>
            <span>Each question is scored as "Yes" or "No". The raw score per domain equals the total "Yes" responses.</span>
          </div>
        </div>
        <div class="help-step">
          <div class="help-step-num">2</div>
          <div class="help-step-content">
            <strong>Scaled Score</strong>
            <span>Raw scores are converted to scaled scores based on the child's age, allowing fair comparisons across age groups.</span>
          </div>
        </div>
        <div class="help-step">
          <div class="help-step-num">3</div>
          <div class="help-step-content">
            <strong>Sum of Scaled Scores</strong>
            <span>All domain scaled scores are added together into a single sum.</span>
          </div>
        </div>
        <div class="help-step">
          <div class="help-step-num">4</div>
          <div class="help-step-content">
            <strong>Standard Score</strong>
            <span>The sum is converted to a standard score using an age-appropriate conversion table.</span>
          </div>
        </div>
        <div class="help-step">
          <div class="help-step-num">5</div>
          <div class="help-step-content">
            <strong>Interpretation</strong>
            <span>The standard score is interpreted (e.g., "Average Development", "Advanced Development") based on age group.</span>
          </div>
        </div>
        <div class="help-step">
          <div class="help-step-num">6</div>
          <div class="help-step-content">
            <strong>Weighted Final Score</strong>
            <span>When both teacher and family assessments are completed: <span class="help-code">(Teacher × 70%) + (Family × 30%)</span></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- UNDERSTANDING DISCREPANCIES --}}
  <div class="help-card">
    <div class="help-card-header hh-orange">
      <div class="hicon hi-orange">🔍</div>
      Understanding Discrepancies
    </div>
    <div class="help-card-body">
      <p>Discrepancies indicate differences in assessment scores between observers. Here's what each level means:</p>

      <p style="font-weight:800;font-size:0.9rem;color:var(--text);margin-top:8px;margin-bottom:8px;">Teacher Discrepancy</p>
      <div class="disc-grid">
        <div class="disc-item">
          <span class="disc-badge badge-none">None</span>
          <div class="disc-text"><strong>Difference ≤ 5 points</strong><span>Teachers' scores align well — great consistency!</span></div>
        </div>
        <div class="disc-item">
          <span class="disc-badge badge-minor">Minor</span>
          <div class="disc-text"><strong>6–10 points difference</strong><span>Small differences in assessment — worth noting.</span></div>
        </div>
        <div class="disc-item">
          <span class="disc-badge badge-major">Major</span>
          <div class="disc-text"><strong>11+ points difference</strong><span>Significant differences — may warrant further observation.</span></div>
        </div>
      </div>

      <p style="font-weight:800;font-size:0.9rem;color:var(--text);margin-top:16px;margin-bottom:8px;">Teacher–Family Discrepancy</p>
      <div class="disc-grid">
        <div class="disc-item">
          <span class="disc-badge badge-none">None</span>
          <div class="disc-text"><strong>Difference ≤ 5 points</strong><span>Teacher and family assessments align well.</span></div>
        </div>
        <div class="disc-item">
          <span class="disc-badge badge-minor">Minor</span>
          <div class="disc-text"><strong>6–10 points difference</strong><span>Small differences between home and school views.</span></div>
        </div>
        <div class="disc-item">
          <span class="disc-badge badge-major">Major</span>
          <div class="disc-text"><strong>11+ points difference</strong><span>Significant gap — additional investigation may be needed.</span></div>
        </div>
      </div>

      <div class="help-alert help-alert-info">
        <span class="aicon">💡</span>
        <span><strong>Tip:</strong> Significant discrepancies may mean your child behaves differently in various settings. This is common and doesn't always indicate a concern.</span>
      </div>
    </div>
  </div>

  {{-- ASSESSMENT FLOW --}}
  <div class="help-card">
    <div class="help-card-header hh-green">
      <div class="hicon hi-green">📋</div>
      Assessment Flow
    </div>
    <div class="help-card-body">
      <p>Each child has <strong>3 assessment periods</strong> — typically at enrollment, 6 months, and 12 months.</p>
      <div class="help-steps" style="margin-top:12px;">
        <div class="help-step">
          <div class="help-step-num">1</div>
          <div class="help-step-content"><strong>Preparation</strong><span>Review your child's profile and any previous assessment results.</span></div>
        </div>
        <div class="help-step">
          <div class="help-step-num">2</div>
          <div class="help-step-content"><strong>Start the Test</strong><span>Select the assessment period to begin. Status becomes "In Progress" and you can pause anytime.</span></div>
        </div>
        <div class="help-step">
          <div class="help-step-num">3</div>
          <div class="help-step-content"><strong>Answer Questions</strong><span>Go through each domain and mark each question "Yes" or "No". You can add notes and scroll back to review.</span></div>
        </div>
        <div class="help-step">
          <div class="help-step-num">4</div>
          <div class="help-step-content"><strong>Complete the Test</strong><span>After all questions are answered, scores are automatically calculated. You can still cancel at this point.</span></div>
        </div>
        <div class="help-step">
          <div class="help-step-num">5</div>
          <div class="help-step-content"><strong>Finalize</strong><span>Click "Finalize" to lock the test. Status becomes "Finalized" and no further changes can be made.</span></div>
        </div>
        <div class="help-step">
          <div class="help-step-num">6</div>
          <div class="help-step-content"><strong>Review Results</strong><span>Access your child's detailed developmental report in the Reports section.</span></div>
        </div>
      </div>
      <div class="help-alert help-alert-warning">
        <span class="aicon">⚠️</span>
        <span><strong>Important:</strong> Once a test is finalized, it cannot be changed. Please review all answers carefully before finalizing!</span>
      </div>
    </div>
  </div>

  {{-- TEST STATUS --}}
  <div class="help-card">
    <div class="help-card-header hh-blue">
      <div class="hicon hi-blue">🏷️</div>
      Test Status States
    </div>
    <div class="help-card-body">
      <div class="status-grid">
        <div class="status-card s-blue">
          <div class="s-icon">🔄</div>
          <h6>In Progress</h6>
          <p>Test is active. Can be paused or canceled anytime.</p>
        </div>
        <div class="status-card s-yellow">
          <div class="s-icon">✅</div>
          <h6>Completed</h6>
          <p>All questions answered and scores calculated. Can still be canceled.</p>
        </div>
        <div class="status-card s-green">
          <div class="s-icon">🔒</div>
          <h6>Finalized</h6>
          <p>Test is locked and counted toward the period. Cannot be changed.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- FAQ --}}
  <div class="help-card">
    <div class="help-card-header hh-yellow">
      <div class="hicon hi-yellow">❓</div>
      Frequently Asked Questions
    </div>
    <div class="help-card-body">
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">Can I go back and change my answers? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">Yes! You can scroll through and update answers while the test is "In Progress" or "Completed". Once you finalize, no changes are allowed.</div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">What should I do if I need to pause? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">You can pause anytime — your progress is automatically saved. The test stays "In Progress" and you can resume whenever you're ready.</div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">Is there a time limit for completing an assessment? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">There's no strict time limit, but assessments should be finished within the specified assessment period dates. You can pause and resume as needed.</div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">What does a "major discrepancy" mean for my child? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">A major discrepancy (11+ point difference) could mean your child behaves differently in different environments. It may warrant a conversation with the teacher or re-assessment.</div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">How often is my child assessed? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">Each child has 3 assessment periods: at enrollment, 6 months after, and 12 months after. Re-testing can occur after 6 months from the last completed assessment.</div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">Can a finalized test be undone? <span class="faq-arrow">▾</span></button>
        <div class="faq-answer">No — finalized tests are permanently locked. If you believe a test needs to be redone, please contact your child's school administrator.</div>
      </div>
    </div>
  </div>

  {{-- CONTACT --}}
  <div class="help-card">
    <div class="help-card-header" style="background:#FFF7FA; border-left:4px solid var(--pink);">
      <div class="hicon" style="background:var(--pink-soft);">💬</div>
      Need More Help?
    </div>
    <div class="help-card-body">
      <p>Can't find what you're looking for? Reach out through any of these options:</p>
      <div class="contact-grid">
        <div class="contact-item">
          <div class="c-icon">🏫</div>
          <h6>School Administrator</h6>
          <p>Contact your child's school for assessment-specific questions.</p>
        </div>
        <div class="contact-item">
          <div class="c-icon">📖</div>
          <h6>Assessment Manual</h6>
          <p>Refer to the official manual for detailed guidance on all domains.</p>
        </div>
        <div class="contact-item">
          <div class="c-icon">📧</div>
          <h6>Email Support</h6>
          <p>support@kiddiecheck.com for technical issues and platform questions.</p>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  function toggleFaq(btn) {
    const answer = btn.nextElementSibling;
    const isOpen = btn.classList.contains('open');
    document.querySelectorAll('.faq-question').forEach(b => {
      b.classList.remove('open');
      b.nextElementSibling.classList.remove('open');
    });
    if (!isOpen) {
      btn.classList.add('open');
      answer.classList.add('open');
    }
  }
</script>
@endsection