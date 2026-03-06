<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name That Color!</title>
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
            font-family: sans-serif;
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
        .question-text { text-align: center; font-size: 18px; color: #555; margin-bottom: 1.5rem; }

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

        .mini-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 1.8rem;
        }
        .dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #e2e8f0;
            border: 2px solid #ccc;
            transition: all 0.3s;
        }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }
        .dot.correct { background: #38A169; border-color: #38A169; }
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; }

        .card-wrap {
            width: 100%;
            max-width: 500px;
            height: 340px;
            perspective: 900px;
            margin: 0 auto 1.5rem;
            cursor: pointer;
        }

        .card-inner {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.55s ease;
            border-radius: 28px;
        }

        .card-inner.flipped { transform: rotateY(180deg); }

        .front, .back {
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 28px;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: 4px solid rgba(0,0,0,0.12);
            box-shadow: 0 8px 28px rgba(0,0,0,0.18);
        }

        .front-text {
            font-size: 1.1rem;
            font-weight: 900;
            color: rgba(255,255,255,0.95);
            background: rgba(0,0,0,0.22);
            padding: 6px 16px;
            border-radius: 99px;
        }

        .back {
            background: #fff;
            transform: rotateY(180deg);
            padding: 20px 16px;
            justify-content: space-around;
        }

        .back-swatch {
            width: 70px; height: 70px;
            border-radius: 50%;
            border: 4px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .back-name   { font-size: 1.8rem; font-weight: 900; color: #1a1a2e; }
        .back-prompt { font-size: 0.82rem; color: #888; font-weight: 700; }
        .back-result { font-size: 2.2rem; display: none; }

        .back-buttons { display: flex; gap: 10px; width: 100%; padding: 0 8px; }

        .btn-yes {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #C6F6D5; border: 2px solid #38A169; color: #276749;
            cursor: pointer;
        }
        .btn-yes:hover:not(:disabled) { background: #9AE6B4; }

        .btn-no {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #FED7D7; border: 2px solid #E53E3E; color: #9B2C2C;
            cursor: pointer;
        }
        .btn-no:hover:not(:disabled) { background: #FEB2B2; }

        .btn-yes:disabled, .btn-no:disabled { opacity: 0.5; cursor: default; }

        .card-inner.answered-correct .back { border-color: #38A169; box-shadow: 0 0 0 4px #38A169; }
        .card-inner.answered-wrong   .back { border-color: #E53E3E; box-shadow: 0 0 0 4px #E53E3E; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 0.2rem; }
        .answer-hint { font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

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
            .card-wrap { height: 240px; }
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

        <div class="game-title">🎨 Name That Color!</div>
        <div class="game-subtitle">Tap the card to flip it → did the child name it correctly?</div>

        <div class="mini-dots" id="miniDots"></div>

        <div class="card-wrap" id="cardWrap">
            <div class="card-inner" id="cardInner">
                <div class="front" id="cardFront">
                    <div class="front-text">What color am I? 🤔</div>
                </div>
                <div class="back" id="cardBack">
                    <div class="back-swatch"  id="backSwatch"></div>
                    <div class="back-name"    id="backName"></div>
                    <div class="back-result"  id="backResult"></div>
                    <div class="back-prompt">Did the child say it correctly?</div>
                    <div class="back-buttons">
                        <button class="btn-yes" id="btnYes" onclick="answer(true)">✅ Yes</button>
                        <button class="btn-no"  id="btnNo"  onclick="answer(false)">❌ No</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tap-hint" id="tapHint">Tap the card to flip it and see the color name</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Answer all 6 colors to unlock Next →</div>

        <div class="nav-footer">

            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="button" id="btnNext" class="btn-nav locked" onclick="clickNext()">Next →</button>

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

<!-- Confirm submit -->
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

<!-- Already answered -->
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

<!-- Skip warning -->
<div class="modal" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">This will skip <strong>all 6 color cards</strong> and move to the next question.<br><br>You can come back to it later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
const colors = [
    { name: 'Red',    hex: '#E53E3E' },
    { name: 'Blue',   hex: '#3182CE' },
    { name: 'Green',  hex: '#38A169' },
    { name: 'Yellow', hex: '#ECC94B' },
    { name: 'Purple', hex: '#805AD5' },
    { name: 'Orange', hex: '#ED8936' },
];

const PASS_SCORE = 4;

// ── Same pattern as color-matching-game.blade.php ──
const existing = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existing !== '';

let current = 0;
let answers = colors.map(() => null);
let skipUrl = null;

function render() {
    const color    = colors[current];
    const answered = answers[current] !== null;

    document.getElementById('cardFront').style.background = color.hex;
    document.getElementById('backSwatch').style.background = color.hex;
    document.getElementById('backName').textContent        = color.name;

    const inner = document.getElementById('cardInner');
    inner.classList.remove('flipped', 'answered-correct', 'answered-wrong');

    document.getElementById('backResult').style.display = 'none';
    document.getElementById('backResult').textContent   = '';
    document.getElementById('btnYes').disabled = false;
    document.getElementById('btnNo').disabled  = false;

    if (answered) {
        inner.classList.add('flipped');
        inner.classList.add(answers[current] ? 'answered-correct' : 'answered-wrong');
        document.getElementById('backResult').textContent   = answers[current] ? '🌟' : '💪';
        document.getElementById('backResult').style.display = 'block';
        document.getElementById('btnYes').disabled = true;
        document.getElementById('btnNo').disabled  = true;
    }

    buildDots();

    const allDone   = answers.every(a => a !== null);
    const remaining = answers.filter(a => a === null).length;

    document.getElementById('btnNext').className      = (allDone || isLocked) ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('tapHint').textContent    = allDone ? 'All done! Click Next → to submit.' : 'Tap the card to flip it and see the color name';
    document.getElementById('answerHint').textContent = allDone ? 'All done! Click Next → to submit.' : `${remaining} color${remaining > 1 ? 's' : ''} left — answer all to unlock Next →`;
}

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

function answer(isCorrect) {
    if (answers[current] !== null || isLocked) return;

    answers[current] = isCorrect;

    document.getElementById('backResult').textContent   = isCorrect ? '🌟' : '💪';
    document.getElementById('backResult').style.display = 'block';
    document.getElementById('cardInner').classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');
    document.getElementById('btnYes').disabled = true;
    document.getElementById('btnNo').disabled  = true;

    buildDots();

    setTimeout(() => {
        document.getElementById('cardInner').classList.remove('flipped');
        setTimeout(() => {
            const next = answers.indexOf(null);
            if (next !== -1) current = next;
            render();
        }, 560);
    }, 800);

    const allDone = answers.every(a => a !== null);
    document.getElementById('btnNext').className      = allDone ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('answerHint').textContent = allDone ? 'All done! Click Next → to submit.' : `${answers.filter(a => a === null).length} color(s) left`;
}

function clickNext() {
    if (isLocked)                      { openModal('lockedModal'); return; }
    if (answers.some(a => a === null)) return;
    openModal('confirmModal');
}

function submitAnswer() {
    closeModal('confirmModal');
    const correct = answers.filter(a => a === true).length;
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

function clickSkip(event) {
    event.preventDefault();
    skipUrl = event.currentTarget.href;
    openModal('skipModal');
}

function doSkip() {
    closeModal('skipModal');
    window.location.href = skipUrl;
}

function openModal(id)  { document.getElementById(id).classList.add('show');    }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

document.getElementById('cardWrap').addEventListener('click', function(e) {
    if (e.target.closest('.btn-yes') || e.target.closest('.btn-no')) return;
    if (answers[current] === null && !isLocked) {
        document.getElementById('cardInner').classList.toggle('flipped');
    }
});

if (isLocked) {
    document.getElementById('lockedBanner').style.display = 'block';
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    answers = colors.map(() => true);
}

render();
</script>
</body>
</html>