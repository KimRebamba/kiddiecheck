<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name That Animal or Veggie!</title> <!-- CHANGE: browser tab title -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Yellow grid background — same as all other games */
        body {
            background: #F5C518;
            background-image:
                linear-gradient(rgba(255,140,0,0.3) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255,140,0,0.3) 2px, transparent 2px);
            background-size: 50px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        /* White card — same as all other games */
        .card {
            background: #fff;
            border-radius: 30px;
            padding: 40px 50px;
            max-width: 920px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        /* Top labels */
        .progress      { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon   { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title  { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text { text-align: center; font-size: 18px; color: #555; margin-bottom: 1.5rem; }

        /* Dashed game box — same as all other games */
        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem 96px 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .game-box.locked { background: #f8f8f8; border-color: #ccc; }

        .game-title    { font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        /* Yellow "already answered" warning bar */
        .locked-banner {
            display: none;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 10px 18px;
            margin-bottom: 1.2rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #856404;
        }

        /* Mini dots — one per card, tracks progress */
        .mini-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.8rem; }
        .dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #e2e8f0; /* gray = not yet answered */
            border: 2px solid #ccc;
            transition: all 0.3s;
        }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); } /* purple = active */
        .dot.correct { background: #38A169; border-color: #38A169; } /* green = got it right */
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; } /* red = got it wrong */

        /* ── The flip card — same concept as color game ── */

        /* Outer box — sets up 3D perspective */
        .card-wrap {
            width: 100%;
            max-width: 500px;
            height: 400px;
            perspective: 900px;
            margin: 0 auto 1.5rem;
            cursor: pointer;
        }

        /* Inner card — this is what actually rotates */
        .card-inner {
            width: 100%; height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.55s ease; /* CHANGE: flip speed */
            border-radius: 28px;
        }

        /* JS adds .flipped to rotate 180deg and show the back */
        .card-inner.flipped { transform: rotateY(180deg); }

        /* Both sides share these base styles */
        .front, .back {
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 28px;
            backface-visibility: hidden; /* hides each side when not facing you */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: 4px solid rgba(0,0,0,0.12);
            box-shadow: 0 8px 28px rgba(0,0,0,0.18);
        }

        /* FRONT — white card with a big emoji */
        .front {
            background: #fff;
        }

        /* The big emoji on the front */
        .front-emoji { font-size: 140px; line-height: 1; }

        /* "What am I?" text on the front — pushed lower with margin-top */
        .front-text {
            font-size: 1.1rem;
            font-weight: 900;
            color: #555;
            background: #f3f0ff;
            padding: 6px 16px;
            border-radius: 99px;
            border: 2px solid #c4b5fd;
            margin-top: 18px;
        }

        /* BACK — white with the name + Yes/No */
        .back {
            background: #fff;
            transform: rotateY(180deg); /* pre-rotated: starts hidden until flipped */
            padding: 20px 16px;
            justify-content: space-around;
        }

        /* Big emoji repeated on the back for reference */
        .back-emoji { font-size: 60px; line-height: 1; }

        /* The animal/veggie name on the back */
        .back-name { font-size: 1.8rem; font-weight: 900; color: #1a1a2e; }

        /* "Did the child say it right?" */
        .back-prompt { font-size: 0.82rem; color: #888; font-weight: 700; }

        /* Result emoji shown after answering */
        .back-result { font-size: 2.2rem; display: none; }

        /* Yes/No row */
        .back-buttons { display: flex; gap: 10px; width: 100%; padding: 0 8px; }

        /* CHANGE: Yes button (green) */
        .btn-yes {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #C6F6D5; border: 2px solid #38A169; color: #276749;
            cursor: pointer;
        }
        .btn-yes:hover:not(:disabled) { background: #9AE6B4; }

        /* CHANGE: No button (red) */
        .btn-no {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #FED7D7; border: 2px solid #E53E3E; color: #9B2C2C;
            cursor: pointer;
        }
        .btn-no:hover:not(:disabled) { background: #FEB2B2; }

        .btn-yes:disabled, .btn-no:disabled { opacity: 0.5; cursor: default; }

        /* Card border glow after answering */
        .card-inner.answered-correct .front,
        .card-inner.answered-correct .back { border-color: #38A169; box-shadow: 0 0 0 4px #38A169; }
        .card-inner.answered-wrong .front,
        .card-inner.answered-wrong .back   { border-color: #E53E3E; box-shadow: 0 0 0 4px #E53E3E; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 0.2rem; }
        .answer-hint { font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        /* Nav buttons — same as all other games */
        .nav-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333;
        }
        .btn-nav:hover  { background: #f5f5f5; }
        .btn-prev       { background: #f5f5f5; border-color: #999; color: #666; }
        .btn-nav.locked { background: #e9e9e9; border-color: #ccc; color: #999; cursor: not-allowed; }

        /* Modals — same as all other games */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
        .modal.show { display: flex; }

        .modal-box {
            background: #fff; border-radius: 24px; padding: 36px 40px;
            max-width: 420px; width: 90%;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            border: 3px solid #000; text-align: center;
        }

        .modal-icon  { font-size: 3rem; margin-bottom: 12px; }
        .modal-title { font-size: 1.25rem; font-weight: 900; margin-bottom: 8px; }
        .modal-body  { font-size: 0.95rem; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .modal-btns  { display: flex; gap: 12px; justify-content: center; }

        .btn-ok     { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #7C3AED; color: #fff; border: none; cursor: pointer; }
        .btn-cancel { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #fff; color: #555; border: 2px solid #ccc; cursor: pointer; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .game-box { padding: 1.5rem 16px; }
            .card-wrap { height: 300px; }
            .front-emoji { font-size: 100px; }
        }
    </style>
</head>
<body>
<div class="card">

    <!--
    ✏️  BLADE VARIABLES TO SWAP IN LARAVEL:
        "3 of 10 answered"       →  {{ $totalAnswered }} of {{ $totalQuestions }} answered
        "Animal & Veggie Names"  →  {{ $currentDomain->domain_name }}
        instruction text         →  {{ $question->display_text }}
    -->
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

        <!-- Shown when already submitted -->
        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

        <!-- CHANGE: edit title/subtitle here -->
        <div class="game-title">🐾 Name That Animal or Veggie!</div>
        <div class="game-subtitle">Tap the card to flip it → did the child name it correctly?</div>

        <!-- Progress dots — built by JS based on items array length -->
        <div class="mini-dots" id="miniDots"></div>

        <!-- Single flip card — JS swaps emoji + name each round -->
        <div class="card-wrap" id="cardWrap">
            <div class="card-inner" id="cardInner">

                <!-- FRONT: white card with big emoji -->
                <div class="front" id="cardFront">
                    <div class="front-emoji" id="frontEmoji"></div>
                    <!-- CHANGE: edit the prompt text on the front -->
                    <div class="front-text">What am I? 🤔</div>
                </div>

                <!-- BACK: emoji + name + Yes/No -->
                <div class="back" id="cardBack">
                    <div class="back-emoji"  id="backEmoji"></div>
                    <div class="back-name"   id="backName"></div>
                    <div class="back-result" id="backResult"></div>
                    <div class="back-prompt">Did the child say it correctly?</div>
                    <div class="back-buttons">
                        <button class="btn-yes" id="btnYes" onclick="answer(true)">✅ Yes</button>
                        <button class="btn-no"  id="btnNo"  onclick="answer(false)">❌ No</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="tap-hint" id="tapHint">Tap the card to flip it and see the name</div>
    </div>

    <!--
    ✏️  FORM — same POST pattern as all other games
        CHANGE action to your Laravel route:
        action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
        Also add @csrf inside the form in Blade.
    -->
        <form method="POST"
        action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
        id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Answer all 5 cards to unlock Next →</div>

        <div class="nav-footer">

            <!-- CHANGE: replace href with your Blade previous route -->
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <!-- Next — locked until all cards are answered -->
                <button type="button" id="btnNext" class="btn-nav locked" onclick="clickNext()">Next →</button>

                <!-- CHANGE: replace href with your Blade next/skip route -->
                @if($nextDomain && $nextIndex)
                    <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}"
                    class="btn-nav" id="btnSkip" onclick="clickSkip(event)">Skip (Answer Later)</a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">Review →</a>
                @endif
            </div>
        </div>
    </form>

</div>


<!-- Confirm submit popup — same as all other games -->
<div class="modal" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">Next means submitting your answer and not returning to it.<br><br>Are you sure?</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-ok"     onclick="submitAnswer()">Yes, Submit</button>
        </div>
    </div>
</div>


<!-- Already answered popup — same as all other games -->
<div class="modal" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">Your answer has been saved and is now locked.</div>
        <div class="modal-btns">
            <button class="btn-ok" onclick="closeModal('lockedModal')">Got it!</button>
        </div>
    </div>
</div>


<!-- Skip warning popup — same as all other games -->
<div class="modal" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <!-- CHANGE: update the number if you change item count -->
        <div class="modal-body">This will skip <strong>all 5 cards</strong> and move to the next question.<br><br>You can come back to it later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>


<script>


const items = [
    { emoji: '🐶', name: 'Dog'     },
    { emoji: '🥕', name: 'Carrot'  },
    { emoji: '🐱', name: 'Cat'     },
    { emoji: '🐸', name: 'Frog'    },
    { emoji: '🥦', name: 'Broccoli'},
];


const PASS_SCORE = 3;


const existing = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existing !== '';

// ── State ──────────────────────────────────────────────────────────────────
let current = 0;                       // which card we're on (0, 1, 2...)
let answers = items.map(() => null);   // null = not answered, true = yes, false = no
let skipUrl = null;                    // remembered skip URL before confirm


// ── render(): show the current card ───────────────────────────────────────
function render() {
    const item     = items[current];
    const answered = answers[current] !== null;

    // Update front emoji
    document.getElementById('frontEmoji').textContent = item.emoji;

    // Update back emoji and name
    document.getElementById('backEmoji').textContent = item.emoji;
    document.getElementById('backName').textContent  = item.name;

    // Reset card to front (unflipped) for each new item
    const inner = document.getElementById('cardInner');
    inner.classList.remove('flipped', 'answered-correct', 'answered-wrong');

    // Reset result and re-enable Yes/No
    document.getElementById('backResult').style.display = 'none';
    document.getElementById('backResult').textContent   = '';
    document.getElementById('btnYes').disabled = false;
    document.getElementById('btnNo').disabled  = false;

    // If already answered, show the result state
    if (answered) {
        inner.classList.add('flipped');
        inner.classList.add(answers[current] ? 'answered-correct' : 'answered-wrong');
        document.getElementById('backResult').textContent   = answers[current] ? '🌟' : '💪';
        document.getElementById('backResult').style.display = 'block';
        document.getElementById('btnYes').disabled = true;
        document.getElementById('btnNo').disabled  = true;
    }

    // Rebuild progress dots
    buildDots();

    // Unlock Next only when ALL cards are answered
    const allDone   = answers.every(a => a !== null);
    const remaining = answers.filter(a => a === null).length;

    document.getElementById('btnNext').className      = (allDone || isLocked) ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('tapHint').textContent    = allDone ? 'All done! Click Next → to submit.' : 'Tap the card to flip it and see the name';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} card${remaining > 1 ? 's' : ''} left — answer all to unlock Next →`;
}


// ── buildDots(): draw one dot per item ────────────────────────────────────
function buildDots() {
    const wrap = document.getElementById('miniDots');
    wrap.innerHTML = '';
    answers.forEach((ans, i) => {
        const d = document.createElement('div');
        d.className = 'dot';
        if      (i === current && ans === null) d.classList.add('current'); // purple
        else if (ans === true)                  d.classList.add('correct'); // green
        else if (ans === false)                 d.classList.add('wrong');   // red
        wrap.appendChild(d);
    });
}


// ── answer(): called when Yes or No is tapped ─────────────────────────────
function answer(isCorrect) {
    if (answers[current] !== null || isLocked) return; // already answered

    answers[current] = isCorrect;

    // Show result emoji on the back
    document.getElementById('backResult').textContent   = isCorrect ? '🌟' : '💪';
    document.getElementById('backResult').style.display = 'block';

    // Add green or red glow
    document.getElementById('cardInner').classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');

    // Lock the buttons
    document.getElementById('btnYes').disabled = true;
    document.getElementById('btnNo').disabled  = true;

    // Update dots right away
    buildDots();

    // After 0.8s: flip back to front, then after flip finishes, show next card
    setTimeout(() => {
        document.getElementById('cardInner').classList.remove('flipped');

        // Wait for the flip animation (0.55s) to finish, then load next card
        setTimeout(() => {
            const next = answers.indexOf(null);
            if (next !== -1) current = next;
            render();
        }, 560);

    }, 800); // CHANGE: how long result shows before flipping back

    // Update Next button and hint immediately
    const allDone = answers.every(a => a !== null);
    document.getElementById('btnNext').className      = allDone ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${answers.filter(a => a === null).length} card(s) left`;
}


// ── clickNext(): Next button handler ──────────────────────────────────────
function clickNext() {
    if (isLocked)                      { openModal('lockedModal'); return; }
    if (answers.some(a => a === null)) return; // still locked
    openModal('confirmModal');
}


// ── submitAnswer(): posts the form after confirming ────────────────────────
function submitAnswer() {
    closeModal('confirmModal');
    const correct = answers.filter(a => a === true).length;
    // Save 'yes' (passed) or 'no' — same pattern as all other games
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}


// ── clickSkip() / doSkip(): skip all cards ────────────────────────────────
function clickSkip(event) {
    event.preventDefault();
    skipUrl = event.currentTarget.href;
    openModal('skipModal');
}

function doSkip() {
    closeModal('skipModal');
    window.location.href = skipUrl;
}


// ── Modal helpers ──────────────────────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('show');    }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});


// ── Tap the card front to flip it ─────────────────────────────────────────
document.getElementById('cardWrap').addEventListener('click', function(e) {
    // Don't flip if Yes or No was tapped
    if (e.target.closest('.btn-yes') || e.target.closest('.btn-no')) return;
    // Only flip if not yet answered and not locked
    if (answers[current] === null && !isLocked) {
        document.getElementById('cardInner').classList.toggle('flipped');
    }
});


// ── Locked state: show as already submitted ────────────────────────────────
if (isLocked) {
    document.getElementById('lockedBanner').style.display = 'block';
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    answers = items.map(() => true);
}


// ── Start ──────────────────────────────────────────────────────────────────
render();

</script>
</body>
</html>