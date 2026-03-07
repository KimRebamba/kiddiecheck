<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow That Instruction!</title>
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

        .card {
            background: #fff;
            border-radius: 30px;
            padding: 40px 50px;
            max-width: 920px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        .progress      { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon   { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title  { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text { text-align: center; font-size: 18px; color: #555; line-height: 1.6; margin-bottom: 1.5rem; }

        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem 2rem 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .game-box.locked { background: #f8f8f8; border-color: #ccc; }

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

        .game-title    { font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        /* Mini dots */
        .mini-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.8rem; }
        .dot {
            width: 12px; height: 12px; border-radius: 50%;
            background: #e2e8f0; border: 2px solid #ccc; transition: all 0.3s;
        }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }
        .dot.correct { background: #38A169; border-color: #38A169; }
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; }

        /* ── FLIP CARD ── */
        .card-wrap {
            width: 100%;
            max-width: 560px;
            height: 320px;
            perspective: 1000px;
            margin: 0 auto 1.2rem;
            cursor: pointer;
        }

        .card-inner {
            width: 100%; height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s ease;
            border-radius: 24px;
        }
        .card-inner.flipped { transform: rotateY(180deg); }

        .card-face {
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 24px;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 4px solid rgba(0,0,0,0.1);
            box-shadow: 0 8px 28px rgba(0,0,0,0.15);
        }

        /* FRONT — instruction side */
        .card-front {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 28px 32px;
            gap: 16px;
        }

        .front-step-label {
            font-size: 0.75rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
        }

        .front-scene-emoji {
            font-size: 64px;
            line-height: 1;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }

        .front-instruction {
            font-size: 1.5rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.3;
            text-align: center;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        /* Highlighted preposition */
        .front-instruction .prep {
            background: #FFE66D;
            color: #1a1a2e;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 1.7rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .front-tap-hint {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.6);
            font-weight: 700;
        }

        /* BACK — yes/no side */
        .card-back {
            background: #fff;
            transform: rotateY(180deg);
            padding: 24px 28px;
            gap: 14px;
        }

        .back-prompt {
            font-size: 1rem;
            font-weight: 900;
            color: #1a1a2e;
        }

        .back-instruction-recap {
            font-size: 0.82rem;
            color: #888;
            font-style: italic;
            text-align: center;
            line-height: 1.5;
        }

        .back-result { font-size: 2.4rem; display: none; }

        .back-buttons { display: flex; gap: 12px; width: 100%; padding: 0 8px; }

        .btn-yes {
            flex: 1; padding: 14px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #C6F6D5; border: 2px solid #38A169; color: #276749;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-yes:hover:not(:disabled) { background: #9AE6B4; transform: translateY(-2px); }

        .btn-no {
            flex: 1; padding: 14px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #FED7D7; border: 2px solid #E53E3E; color: #9B2C2C;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-no:hover:not(:disabled) { background: #FEB2B2; transform: translateY(-2px); }

        .btn-yes:disabled, .btn-no:disabled { opacity: 0.5; cursor: default; transform: none; }

        /* Glow on answer */
        .card-inner.answered-correct .card-back { border-color: #38A169; box-shadow: 0 0 0 4px #38A169; }
        .card-inner.answered-wrong   .card-back { border-color: #E53E3E; box-shadow: 0 0 0 4px #E53E3E; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 0.2rem; }
        .answer-hint { font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        /* Nav */
        .nav-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333; transition: all 0.2s;
        }
        .btn-nav:hover  { background: #f5f5f5; transform: translateY(-2px); }
        .btn-prev       { background: #f5f5f5; border-color: #999; color: #666; }
        .btn-nav.btn-locked { background: #e9e9e9; border-color: #ccc; color: #999; cursor: not-allowed; }
        .btn-nav.btn-locked:hover { transform: none; background: #e9e9e9; }

        /* Modals */
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
        .modal-icon   { font-size: 3rem; margin-bottom: 12px; }
        .modal-title  { font-size: 1.25rem; font-weight: 900; color: #1a1a2e; margin-bottom: 8px; }
        .modal-body   { font-size: 0.95rem; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .modal-actions{ display: flex; gap: 12px; justify-content: center; }
        .btn-modal-ok     { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #7C3AED; color: #fff; border: none; cursor: pointer; }
        .btn-modal-ok:hover { background: #6d28d9; }
        .btn-modal-cancel { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #fff; color: #555; border: 2px solid #ccc; cursor: pointer; }
        .btn-modal-cancel:hover { background: #f0f0f0; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .card-wrap { height: 260px; }
            .front-instruction { font-size: 1.2rem; }
            .front-scene-emoji { font-size: 48px; }
        }
    </style>
</head>
<body>
<div class="card">

    <div class="progress">{{ $totalAnswered }} of {{ $totalQuestions }} answered</div>

    <div class="domain-icon">
        @php
            $icons = [
                'Gross Motor'         => '⚡',
                'Fine Motor'          => '✋',
                'Self-Help'           => '🎯',
                'Receptive Language'  => '👂',
                'Expressive Language' => '💬',
                'Cognitive'           => '🧠',
                'Social-Emotional'    => '❤️',
            ];
        @endphp
        {{ $icons[$currentDomain->domain_name] ?? '📋' }}
    </div>

    <div class="domain-title">{{ $currentDomain->domain_name }}</div>
    <div class="question-text">{{ $question->display_text ?? $question->text }}</div>

    <div class="game-box" id="gameBox">

        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

        <div class="game-title">👂 Follow That Instruction!</div>
        <div class="game-subtitle">Read the instruction to the child — then flip to mark Yes or No</div>

        <div class="mini-dots" id="miniDots"></div>

        <!-- FLIP CARD -->
        <div class="card-wrap" id="cardWrap">
            <div class="card-inner" id="cardInner">

                <!-- FRONT: instruction -->
                <div class="card-face card-front">
                    <div class="front-step-label" id="stepLabel">Instruction 1 of 5</div>
                    <div class="front-scene-emoji" id="frontEmoji"></div>
                    <div class="front-instruction" id="frontInstruction"></div>
                    <div class="front-tap-hint">👆 Tap card to flip after the child responds</div>
                </div>

                <!-- BACK: yes/no -->
                <div class="card-face card-back">
                    <div class="back-prompt">Did the child follow correctly?</div>
                    <div class="back-instruction-recap" id="backRecap"></div>
                    <div class="back-result" id="backResult"></div>
                    <div class="back-buttons">
                        <button class="btn-yes" id="btnYes" onclick="answer(true)">✅ Yes</button>
                        <button class="btn-no"  id="btnNo"  onclick="answer(false)">❌ No</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="tap-hint" id="tapHint">Tap the card to flip it</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Answer all 5 instructions to unlock Next →</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="button" id="btnNext" onclick="handleNext()" class="btn-nav btn-locked">Next →</button>

                @if($nextDomain && $nextIndex)
                    <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}"
                       id="btnSkip" onclick="handleSkip(event)" class="btn-nav">Skip (Answer Later)</a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">Review →</a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Confirm -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">Next means submitting your answer and not returning to it.<br><br>Are you sure?</div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSubmit()">Yes, Submit</button>
        </div>
    </div>
</div>

<!-- Locked -->
<div class="modal-overlay" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">Your previous answer has been saved and is now locked.<br><br>Click Next to continue.</div>
        <div class="modal-actions">
            <button class="btn-modal-ok" onclick="closeModal('lockedModal')">Got it!</button>
        </div>
    </div>
</div>

<!-- Skip -->
<div class="modal-overlay" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">Skipping will leave this question unanswered.<br><br>You can come back to it later.</div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
// ── 5 instructions — each has an emoji, the instruction text, and the highlighted preposition ──
const instructions = [
    {
        emoji:       '🧸',
        text:        'Put the toy <span class="prep">under</span> the table',
        plain:       'Put the toy UNDER the table',
    },
    {
        emoji:       '📦',
        text:        'Put the block <span class="prep">inside</span> the box',
        plain:       'Put the block INSIDE the box',
    },
    {
        emoji:       '🥤',
        text:        'Put the cup <span class="prep">on top of</span> the book',
        plain:       'Put the cup ON TOP OF the book',
    },
    {
        emoji:       '🪑',
        text:        'Stand <span class="prep">behind</span> the chair',
        plain:       'Stand BEHIND the chair',
    },
    {
        emoji:       '🎒',
        text:        'Put the bag <span class="prep">next to</span> the door',
        plain:       'Put the bag NEXT TO the door',
    },
];

const PASS_SCORE = 3; // 3 out of 5 = yes

const existingResponse = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existingResponse !== '';

let current   = 0;
let answers   = new Array(instructions.length).fill(null);
let roundDone = false;
let skipTarget = null;

// ── render(): show the current instruction card ──
function render() {
    const item     = instructions[current];
    const answered = answers[current] !== null;

    document.getElementById('stepLabel').textContent       = `Instruction ${current + 1} of ${instructions.length}`;
    document.getElementById('frontEmoji').textContent      = item.emoji;
    document.getElementById('frontInstruction').innerHTML  = item.text;
    document.getElementById('backRecap').textContent       = item.plain;

    // Reset flip
    const inner = document.getElementById('cardInner');
    inner.classList.remove('flipped', 'answered-correct', 'answered-wrong');

    // Reset back side
    document.getElementById('backResult').style.display = 'none';
    document.getElementById('backResult').textContent   = '';
    document.getElementById('btnYes').disabled = false;
    document.getElementById('btnNo').disabled  = false;

    roundDone = answered;

    // If already answered for this card
    if (answered) {
        inner.classList.add('flipped');
        inner.classList.add(answers[current] ? 'answered-correct' : 'answered-wrong');
        document.getElementById('backResult').textContent   = answers[current] ? '🌟' : '💪';
        document.getElementById('backResult').style.display = 'block';
        document.getElementById('btnYes').disabled = true;
        document.getElementById('btnNo').disabled  = true;
    }

    buildDots();
    updateHints();
}

// ── buildDots() ──
function buildDots() {
    const wrap = document.getElementById('miniDots');
    wrap.innerHTML = '';
    answers.forEach((ans, i) => {
        const d = document.createElement('div');
        d.className = 'dot';
        if      (i === current && ans === null) d.classList.add('current');
        else if (ans === true)                  d.classList.add('correct');
        else if (ans === false)                 d.classList.add('wrong');
        wrap.appendChild(d);
    });
}

// ── answer(): called when Yes or No tapped ──
function answer(isCorrect) {
    if (roundDone || isLocked) return;

    answers[current] = isCorrect;
    roundDone = true;

    document.getElementById('backResult').textContent   = isCorrect ? '🌟' : '💪';
    document.getElementById('backResult').style.display = 'block';
    document.getElementById('cardInner').classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');
    document.getElementById('btnYes').disabled = true;
    document.getElementById('btnNo').disabled  = true;

    buildDots();
    updateHints();

    // Auto-advance to next unanswered after 0.8s
    setTimeout(() => {
        document.getElementById('cardInner').classList.remove('flipped');
        setTimeout(() => {
            const next = answers.indexOf(null);
            if (next !== -1) { current = next; render(); }
        }, 560);
    }, 800);
}

// ── Tap card to flip ──
document.getElementById('cardWrap').addEventListener('click', function(e) {
    if (e.target.closest('.btn-yes') || e.target.closest('.btn-no')) return;
    if (!roundDone && !isLocked) {
        document.getElementById('cardInner').classList.toggle('flipped');
    }
});

function updateHints() {
    const allDone   = answers.every(a => a !== null);
    const remaining = answers.filter(a => a === null).length;

    document.getElementById('btnNext').className      = (allDone || isLocked) ? 'btn-nav' : 'btn-nav btn-locked';
    document.getElementById('tapHint').textContent    = roundDone ? 'Moving to next...' : 'Tap the card to flip it';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} instruction${remaining > 1 ? 's' : ''} left — answer all to unlock Next →`;
}

const nextUrl = "{{ $nextDomain && $nextIndex ? route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) : route('family.tests.result', $testId) }}";

function handleNext() {
    if (isLocked) {
        window.location.href = nextUrl;
        return;
    }
    if (answers.some(a => a === null)) return;
    document.getElementById('confirmModal').classList.add('show');
}

function confirmSubmit() {
    closeModal('confirmModal');
    const correct = answers.filter(a => a === true).length;
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

function handleSkip(event) {
    event.preventDefault();
    skipTarget = event.currentTarget.href;
    document.getElementById('skipModal').classList.add('show');
}
function confirmSkip() { closeModal('skipModal'); window.location.href = skipTarget; }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

// Locked state
if (isLocked) {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    answers = new Array(instructions.length).fill(true);
}

render();
</script>
</body>
</html>