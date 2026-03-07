<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CHANGE: Browser tab title -->
    <title>Name That Thing!</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #F5C518;
            background-image:
                linear-gradient(rgba(255,140,0,0.3) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255,140,0,0.3) 2px, transparent 2px);
            background-size: 50px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        /* Main white card — same as sample */
        .card {
            background: #fff;
            border-radius: 30px;
            padding: 40px 50px;
            max-width: 920px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        /* "X of Y answered" — same as sample */
        .progress       { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon    { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title   { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text  { text-align: center; font-size: 18px; color: #555; line-height: 1.6; margin-bottom: 1.5rem; }

        /* Dashed game box — same as sample */
        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        /* Gray out game box when locked (already answered) */
        .game-box.locked { background: #f8f8f8; border-color: #ccc; }

        /* "Already answered" yellow warning bar — same as sample */
        .locked-banner {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 10px 18px;
            margin-bottom: 1.2rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #856404;
        }
        .locked-banner.visible { display: flex; }

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        /* ── Mini progress bar: shows how many of the 5 pictures done ── */
        .mini-progress-wrap {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 1.5rem;
        }
        .mini-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            border: 2px solid #ccc;
            transition: all 0.2s;
        }
        .mini-dot.done    { background: #38A169; border-color: #38A169; }
        .mini-dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }

        /* ── Game area: big picture + 4 choices ── */
        .game-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        /* The big emoji/image box */
        .picture-box {
            font-size: 90px;
            background: #fff;
            border: 4px solid rgba(0,0,0,0.1);
            border-radius: 24px;
            width: 140px; height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        /* 2x2 grid of answer buttons */
        .choices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            width: 100%;
            max-width: 400px;
        }

        .choice-btn {
            padding: 15px 10px;
            background: #fff;
            border: 3px solid #e2e8f0;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }
        .choice-btn:hover:not(:disabled) { background: #f3f0ff; border-color: #7C3AED; transform: translateY(-2px); }
        .choice-btn:disabled             { cursor: default; }

        /* CHANGE: correct answer highlight color */
        .choice-btn.correct { background: #C6F6D5; border-color: #38A169; color: #276749; }

        /* CHANGE: wrong answer highlight color */
        .choice-btn.wrong   { background: #FED7D7; border-color: #E53E3E; color: #9B2C2C; }

        /* Hint text below the choices */
        .tap-hint    { text-align: center; font-size: 0.82rem; color: #bbb; margin-top: 1rem; }
        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        /* ── Nav footer — identical layout to sample ── */
        .nav-footer  { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center  { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333; transition: all 0.2s;
        }
        .btn-nav:hover  { background: #f5f5f5; transform: translateY(-2px); }
        .btn-prev       { background: #f5f5f5; border-color: #999; color: #666; }
        .btn-prev:hover { background: #e0e0e0; color: #333; }

        /* Grayed-out Next — before all 5 are answered */
        .btn-nav.btn-locked { background: #e9e9e9; border-color: #ccc; color: #999; cursor: not-allowed; }
        .btn-nav.btn-locked:hover { transform: none; background: #e9e9e9; }

        /* ── Modals — identical to sample ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }

        .modal-box {
            background: #fff; border-radius: 24px; padding: 36px 40px;
            max-width: 420px; width: 90%;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            border: 3px solid #000; text-align: center;
            animation: modalPop 0.25s ease;
        }
        @keyframes modalPop {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }

        .modal-icon  { font-size: 3rem; margin-bottom: 12px; }
        .modal-title { font-size: 1.25rem; font-weight: 900; color: #1a1a2e; margin-bottom: 8px; }
        .modal-body  { font-size: 0.95rem; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 12px; justify-content: center; }

        .btn-modal-ok {
            padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700;
            background: #7C3AED; color: #fff; border: none; cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-modal-ok:hover { background: #6d28d9; transform: translateY(-2px); }

        .btn-modal-cancel {
            padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700;
            background: #fff; color: #555; border: 2px solid #ccc; cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-modal-cancel:hover { background: #f0f0f0; transform: translateY(-2px); }

        /* Score summary inside result modal */
        .score-row   { font-size: 2rem; font-weight: 900; color: #7C3AED; margin-bottom: 6px; }
        .badge       { display: inline-block; padding: 6px 20px; border-radius: 99px; font-weight: 900; font-size: 1rem; margin-top: 8px; }
        .badge.pass  { background: #C6F6D5; color: #276749; border: 2px solid #38A169; }
        .badge.fail  { background: #FED7D7; color: #9B2C2C; border: 2px solid #E53E3E; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .picture-box { width: 110px; height: 110px; font-size: 70px; }
        }
    </style>
</head>
<body>

<div class="card">

    

    <!-- CHANGE: "$totalAnswered of $totalQuestions answered" — replace with Blade: <?php echo e($totalAnswered); ?> of <?php echo e($totalQuestions); ?> answered -->
    <div class="progress" id="progress"><?php echo e($totalAnswered); ?> of <?php echo e($totalQuestions); ?> answered</div>

    <div class="domain-icon">
    <?php
        $icons = [
            'Gross Motor' => '⚡',
            'Fine Motor' => '✋',
            'Self-Help' => '🎯',
            'Receptive Language' => '👂',
            'Expressive Language' => '💬',
            'Cognitive' => '🧠',
            'Social-Emotional' => '❤️',
        ];
    ?>
    <?php echo e($icons[$currentDomain->domain_name] ?? '📋'); ?>

</div>

    <!-- CHANGE: domain name — replace with Blade: <?php echo e($currentDomain->domain_name); ?> -->
    <div class="domain-title"><?php echo e($currentDomain->domain_name); ?></div>

    <!-- CHANGE: question instruction text — replace with Blade: <?php echo e($question->display_text); ?> -->
    <div class="question-text"><?php echo e($question->display_text ?? $question->text); ?></div>


    <!-- ── GAME BOX ── -->
    <div class="game-box" id="gameBox">

        <!-- Shown when this question was already answered before -->
        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

        <!-- CHANGE: Edit the mini title and subtitle of the game here -->
        <div class="game-title">🖼️ Name That Thing!</div>
        <div class="game-subtitle">Answer all 5 pictures, then click Next →</div>

        <!-- 5 dots showing progress through the mini-game (filled in by JS) -->
        <div class="mini-progress-wrap" id="miniProgress"></div>

        <!-- Big picture + 4 choices (filled in by JS) -->
        <div class="game-area">
            <div class="picture-box" id="pictureBox"></div>
            <div class="choices"     id="choices"></div>
        </div>

        <div class="tap-hint" id="tapHint">Answer all 5 to unlock Next →</div>
    </div>


    <!--
        ── LARAVEL FORM ──
        CHANGE: Update the action URL to match your route, e.g.:
        action="<?php echo e(route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex])); ?>"
    -->
    <form method="POST" action="<?php echo e(route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex])); ?>" id="answerForm">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Answer all 5 pictures, then click Next →</div>

        <div class="nav-footer">

            <!--
                CHANGE: Previous button — replace href with Blade route:
                href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex])); ?>"
                Hide it if there's no previous question (same as sample).
            -->
            <?php if($prevDomain && $prevIndex): ?>
            <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex])); ?>"
            class="btn-nav btn-prev">← Previous</a>
        <?php else: ?>
            <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
        <?php endif; ?>

            <div class="nav-center">

                <!--
                    Next button — locked (grayed out) until all 5 are answered.
                    When clicked it opens the confirm modal, then submits the form.
                -->
                <button type="button" id="btnNext" onclick="handleNext()" class="btn-nav btn-locked">
                    Next →
                </button>

                <!--
                    Skip button — skips the ENTIRE game (all 5 questions) with no answer saved.
                    CHANGE: Replace href with Blade route to the next domain/question:
                    href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex])); ?>"
                    Change the last href to Review if this is the last question (same as sample).
                -->
                <?php if($nextDomain && $nextIndex): ?>
                    <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex])); ?>"
                    id="btnSkip" onclick="handleSkip(event)" class="btn-nav">Skip (Answer Later)</a>
                <?php else: ?>
                    <a href="<?php echo e(route('family.tests.result', $testId)); ?>" class="btn-nav">Review →</a>
                <?php endif; ?>

            </div>
        </div>
    </form>

</div>


<!-- ── CONFIRM SUBMIT MODAL: opens when Next → is clicked after all 5 answered ── -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>

        <!-- CHANGE: Edit confirm popup title and message here -->
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">
            Next means submitting your answer and not returning to it.<br><br>
            Are you sure you want to submit?
        </div>

        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSubmit()">Yes, Submit</button>
        </div>
    </div>
</div>


<!-- ── ALREADY ANSWERED MODAL: opens when Next → is clicked on a locked question ── -->
<div class="modal-overlay" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">
            Your previous answer has been saved and is now locked.<br><br>
            Click Next to continue to the next question.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-ok" onclick="closeModal('lockedModal')">Got it!</button>
        </div>
    </div>
</div>


<!-- ── SKIP CONFIRM MODAL: warns user before skipping the whole game ── -->
<div class="modal-overlay" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">
            Skipping will leave this question unanswered.<br><br>
            You can come back to it later.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSkip()">Yes, Skip</button>
        </div>
    </div>
</div>


<script>

const questions = [
    { emoji: '🍎', answer: 'Apple',  choices: ['Apple',  'Orange', 'Mango',  'Grape']  },
    { emoji: '🐶', answer: 'Dog',    choices: ['Cat',    'Dog',    'Rabbit', 'Fox']    },
    { emoji: '🚗', answer: 'Car',    choices: ['Truck',  'Bus',    'Car',    'Bike']   },
    { emoji: '🌻', answer: 'Flower', choices: ['Tree',   'Leaf',   'Flower', 'Grass']  },
    { emoji: '🐱', answer: 'Cat',    choices: ['Dog',    'Lion',   'Cat',    'Tiger']  },
];

// ============================================================
// ✏️  2. PASSING SCORE — how many correct out of 5 to pass
//        Currently: 3 out of 5 = PASSED, below 3 = NOT YET
// ============================================================
const PASS_SCORE = 3;

// ============================================================
// ✏️  3. ALREADY ANSWERED? — if this question was already
//        submitted before, set this to 'yes' or 'no'.
//        In Laravel, replace with:
//        const existingResponse = '<?php echo e($existingResponse ?? ""); ?>';
// ============================================================
const existingResponse = '<?php echo addslashes($existingResponse ?? ''); ?>';

// ── Internal game state (no need to edit) ────────────────────────────────
const isLocked = existingResponse !== ''; // true = already answered, lock the game
let current    = 0;                       // which of the 5 pictures we're on
let answers    = new Array(questions.length).fill(null); // null = not answered yet
let skipTarget = null;                    // stores the skip URL before confirming


// ── render(): redraws the current picture and choices ────────────────────
function render() {
    const q        = questions[current];
    const answered = answers[current] !== null;

    // Show the emoji picture
    document.getElementById('pictureBox').textContent = q.emoji;

    // Build the mini progress dots (one dot per question)
    const dotsWrap = document.getElementById('miniProgress');
    dotsWrap.innerHTML = '';
    questions.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = 'mini-dot';
        if (answers[i] !== null) dot.classList.add('done');    // answered = green
        if (i === current)       dot.classList.add('current'); // current = purple
        dotsWrap.appendChild(dot);
    });

    // Build the 4 choice buttons
    const grid = document.getElementById('choices');
    grid.innerHTML = '';
    q.choices.forEach(choice => {
        const btn       = document.createElement('button');
        btn.className   = 'choice-btn';
        btn.textContent = choice;
        btn.disabled    = answered || isLocked;
        btn.onclick     = () => pickAnswer(choice);

        // Color buttons if already answered
        if (answered || isLocked) {
            if (choice === q.answer)                                 btn.classList.add('correct');
            if (choice === answers[current] && choice !== q.answer)  btn.classList.add('wrong');
        }

        grid.appendChild(btn);
    });

    // Check if ALL 5 are answered — unlock Next button only then
    const allDone = answers.every(a => a !== null);
    document.getElementById('btnNext').className = (allDone || isLocked)
        ? 'btn-nav'         // active
        : 'btn-nav btn-locked'; // grayed out

    // Update hint text
    const remaining = answers.filter(a => a === null).length;
    document.getElementById('tapHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} picture${remaining > 1 ? 's' : ''} left to answer`;
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : 'Answer all 5 pictures, then click Next →';
}


// ── pickAnswer(): called when a child taps a choice ──────────────────────
function pickAnswer(choice) {
    if (answers[current] !== null || isLocked) return; // ignore if locked or done
    answers[current] = choice; // save answer

    // Auto-advance to the next unanswered picture after a short delay
    setTimeout(() => {
        const nextUnanswered = answers.indexOf(null);
        if (nextUnanswered !== -1) {
            current = nextUnanswered; // jump to next unanswered
        }
        render();
    }, 600); // 600ms pause so child can see the correct/wrong color

    render(); // immediately show the color feedback
}


// ── handleNext(): called when Next → is clicked ──────────────────────────
const nextUrl = "<?php echo e($nextDomain && $nextIndex ? route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) : route('family.tests.result', $testId)); ?>";

function handleNext() {
    if (isLocked) { window.location.href = nextUrl; return; }
    if (answers.some(a => a === null)) return;
    document.getElementById('confirmModal').classList.add('show');
}

// ── confirmSubmit(): runs after user clicks "Yes, Submit" ────────────────
function confirmSubmit() {
    closeModal('confirmModal');

    // Count correct answers
    const correct = answers.filter((a, i) => a === questions[i].answer).length;

    // Save "yes" or "no" into the hidden form field (same as the match game)
    // "yes" = passed (3 or more correct), "no" = failed (less than 3)
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';

    // Submit the Laravel form
    document.getElementById('answerForm').submit();
}


// ── handleSkip(): opens skip confirm modal before skipping ───────────────
function handleSkip(event) {
    event.preventDefault(); // stop the link from navigating immediately
    skipTarget = event.currentTarget.href; // remember where to go
    document.getElementById('skipModal').classList.add('show');
}


// ── confirmSkip(): skips the whole game, goes to next domain/question ────
function confirmSkip() {
    closeModal('skipModal');
    // Navigate to the skip URL (next domain/question route from Blade)
    window.location.href = skipTarget;
}


// ── closeModal(): hides a modal by its ID ────────────────────────────────
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// Also close modal when clicking the dark background
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('show'); });
});


// ── applyLockedState(): if already answered, show all answers locked ─────
function applyLockedState() {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('tapHint').textContent    = 'This question is locked.';
    document.getElementById('answerHint').textContent = 'This question has already been submitted.';
    document.getElementById('btnSkip').style.display  = 'none';

    // Show all 5 as "correct" since we don't store individual answers after lock
    answers = questions.map(q => q.answer);
}


// ── Start ─────────────────────────────────────────────────────────────────
if (isLocked) applyLockedState();
render();

</script>
</body>
</html><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\family\EL-8-Name-Objects.blade.php ENDPATH**/ ?>