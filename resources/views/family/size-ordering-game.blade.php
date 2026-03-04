<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order by Size!</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #F5C518;
            background-image:
                linear-gradient(rgba(255,140,0,0.3) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255,140,0,0.3) 2px, transparent 2px);
            background-size: 50px 50px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .card {
            background: #fff;
            border-radius: 30px;
            padding: 40px 50px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        .progress   { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon  { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text { text-align: center; font-size: 18px; color: #555; line-height: 1.6; margin-bottom: 1.5rem; }

        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.4rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        /* Phase tabs */
        .phase-tabs { display: flex; justify-content: center; gap: 12px; margin-bottom: 1.5rem; }
        .phase-tab  { padding: 8px 22px; border-radius: 999px; font-size: 0.85rem; font-weight: 800; border: 2px solid #e0e0e0; background: #f5f5f5; color: #aaa; }
        .phase-tab.active { background: #7C3AED; border-color: #7C3AED; color: #fff; }
        .phase-tab.done   { background: #4CAF50; border-color: #4CAF50; color: #fff; }

        /* Note banner */
        .note {
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.2rem;
            background: #EFF6FF; border: 2px solid #93C5FD; color: #1D4ED8;
        }
        .note.child { background: #FFF7ED; border-color: #FCD34D; color: #92400E; }

        /* Tray */
        .tray-label { text-align: center; font-size: 0.85rem; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 0.05rem; margin-bottom: 0.7rem; }
        .tray {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            background: #f9f9f9;
            border: 2px dashed #ddd;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            min-height: 120px;
            margin-bottom: 1.2rem;
        }

        /* Drop slots */
        .slots { display: flex; justify-content: center; align-items: center; gap: 18px; flex-wrap: wrap; }
        .slot-wrap { display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .slot-label { font-size: 0.65rem; font-weight: 900; color: #ccc; text-transform: uppercase; letter-spacing: 0.05em; }

        .slot {
            width: 120px; height: 120px;
            border: 3px dashed #e0e0e0;
            border-radius: 14px;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s, background 0.2s;
        }
        .slot.over    { border-color: #7C3AED; background: #f0ebff; }
        .slot.filled  { border-color: #94A3B8; background: #f5f5f5; }

        /* Shape cards */
        .shape {
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; border: 3px solid #e0e0e0; background: #fff;
            cursor: grab; transition: all 0.25s; user-select: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1); flex-shrink: 0;
        }
        .shape:hover:not(.placed) { transform: scale(1.1) rotate(-3deg); box-shadow: 0 8px 20px rgba(0,0,0,0.18); border-color: #f5a623; }
        .shape.dragging { opacity: 0.4; transform: scale(0.9); }
        .shape.placed   { cursor: default; border-color: #94A3B8; background: #f5f5f5; animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
        .shape.selected { outline: 3px solid #7C3AED; outline-offset: 3px; }

        /* 4 graduated card sizes */
        .sz1 { width: 52px;  height: 52px; }
        .sz2 { width: 70px;  height: 70px; }
        .sz3 { width: 88px;  height: 88px; }
        .sz4 { width: 106px; height: 106px; }

        /* Success */
        .success { display: none; text-align: center; font-size: 1.1rem; color: #4CAF50; font-weight: 900; margin-top: 1rem; padding: 1rem; background: #f0fff4; border-radius: 14px; border: 3px solid #4CAF50; }
        .success.show { display: block; }

        /* Nav */
        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }
        .nav-footer  { display: flex; justify-content: space-between; align-items: center; margin-top: 1.2rem; }
        .nav-center  { display: flex; gap: 10px; }
        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333; transition: all 0.2s;
        }
        .btn-nav:hover { background: #f5f5f5; transform: translateY(-2px); }
        .btn-prev { background: #f5f5f5; border-color: #999; color: #666; }

        @keyframes popIn  { 0%{transform:scale(0.8)} 60%{transform:scale(1.15)} 100%{transform:scale(1)} }

        @media (max-width: 640px) {
            .card { padding: 24px 16px; }
            .slot { width: 80px; height: 80px; }
            .sz1 { width: 38px; height: 38px; } .sz2 { width: 52px; height: 52px; }
            .sz3 { width: 66px; height: 66px; } .sz4 { width: 80px; height: 80px; }
        }
    </style>
</head>
<body>
<div class="card">

    <div class="progress">{{ $totalAnswered }} of {{ $totalQuestions }} answered</div>
    <div class="domain-icon">🧠</div>
    <div class="domain-title">{{ $currentDomain->domain_name }}</div>
    <div class="question-text">{{ $question->display_text ?? $question->text }}</div>

    <div class="game-box">
        <div class="game-title">📐 Order from Smallest to Biggest!</div>
        <div class="game-subtitle">Drag each shape into the correct slot — smallest on the left, biggest on the right.</div>

        <div class="phase-tabs">
            <div class="phase-tab active" id="tab-sq">🟪 Squares</div>
            <div class="phase-tab" id="tab-ci">🔵 Circles</div>
        </div>

        {{-- PHASE 1: Squares --}}
        <div id="phase-sq">
            <div class="note">👩‍🏫 <strong>Demonstration phase</strong> — Parent shows the child how to order the squares first.</div>

            <div class="tray-label">👆 Drag the squares</div>
            <div class="tray" id="tray-sq">
                <div class="shape sz3" draggable="true" data-size="3"><svg width="68" height="68" viewBox="0 0 68 68"><rect x="4" y="4" width="60" height="60" rx="6" fill="#7C3AED"/></svg></div>
                <div class="shape sz1" draggable="true" data-size="1"><svg width="36" height="36" viewBox="0 0 36 36"><rect x="3" y="3" width="30" height="30" rx="4" fill="#7C3AED"/></svg></div>
                <div class="shape sz4" draggable="true" data-size="4"><svg width="86" height="86" viewBox="0 0 86 86"><rect x="4" y="4" width="78" height="78" rx="7" fill="#7C3AED"/></svg></div>
                <div class="shape sz2" draggable="true" data-size="2"><svg width="52" height="52" viewBox="0 0 52 52"><rect x="4" y="4" width="44" height="44" rx="5" fill="#7C3AED"/></svg></div>
            </div>

            <div class="tray-label">👇 Drop in order</div>
            <div class="slots" id="slots-sq">
                <div class="slot-wrap"><div class="slot" data-phase="sq" data-slot="1"></div><span class="slot-label">Smallest</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="sq" data-slot="2"></div><span class="slot-label">2nd</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="sq" data-slot="3"></div><span class="slot-label">3rd</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="sq" data-slot="4"></div><span class="slot-label">Biggest</span></div>
            </div>

            <div class="success" id="ok-sq">🎉 Squares done! Now try the circles on your own!</div>
        </div>

        {{-- PHASE 2: Circles --}}
        <div id="phase-ci" style="display:none;">
            <div class="note child">👧 <strong>Child's turn!</strong> — Order the circles from smallest to biggest!</div>

            <div class="tray-label">👆 Drag the circles</div>
            <div class="tray" id="tray-ci">
                <div class="shape sz2" draggable="true" data-size="2"><svg width="52" height="52" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg></div>
                <div class="shape sz4" draggable="true" data-size="4"><svg width="86" height="86" viewBox="0 0 86 86"><circle cx="43" cy="43" r="40" fill="#3B82F6"/></svg></div>
                <div class="shape sz1" draggable="true" data-size="1"><svg width="36" height="36" viewBox="0 0 36 36"><circle cx="18" cy="18" r="15" fill="#3B82F6"/></svg></div>
                <div class="shape sz3" draggable="true" data-size="3"><svg width="68" height="68" viewBox="0 0 68 68"><circle cx="34" cy="34" r="31" fill="#3B82F6"/></svg></div>
            </div>

            <div class="tray-label">👇 Drop in order</div>
            <div class="slots" id="slots-ci">
                <div class="slot-wrap"><div class="slot" data-phase="ci" data-slot="1"></div><span class="slot-label">Smallest</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="ci" data-slot="2"></div><span class="slot-label">2nd</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="ci" data-slot="3"></div><span class="slot-label">3rd</span></div>
                <div class="slot-wrap"><div class="slot" data-phase="ci" data-slot="4"></div><span class="slot-label">Biggest</span></div>
            </div>

            <div class="success" id="ok-ci">🎉 All circles placed! Click Next to submit.</div>
        </div>
    </div>

    <form method="POST" action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}" id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">
        <div class="answer-hint">Order the shapes then click Next to submit your answer</div>
        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}" class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility:hidden">← Previous</span>
            @endif
            <div class="nav-center">
                <button type="button" onclick="submitAnswer()" class="btn-nav">Next →</button>
                @if($nextDomain && $nextIndex)
                    <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}" class="btn-nav">Skip (Answer Later)</a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">Review →</a>
                @endif
            </div>
        </div>
    </form>

</div>

<script>
let dragged  = null;
let selected = null;
// Track how many shapes placed in circles phase (for showing success banner)
let ciPlaced = 0;

function setup(trayId, slotsId) {
    document.querySelectorAll('#' + trayId + ' .shape').forEach(c => {
        c.addEventListener('dragstart', () => { dragged = c; c.classList.add('dragging'); });
        c.addEventListener('dragend',   () => c.classList.remove('dragging'));
        c.addEventListener('click', () => {
            if (c.classList.contains('placed')) return;
            if (selected) selected.classList.remove('selected');
            selected = (selected === c) ? null : c;
            if (selected) selected.classList.add('selected');
        });
    });

    document.querySelectorAll('#' + slotsId + ' .slot').forEach(s => {
        s.addEventListener('dragover',  e => { e.preventDefault(); if (!s.classList.contains('filled')) s.classList.add('over'); });
        s.addEventListener('dragleave', () => s.classList.remove('over'));
        s.addEventListener('drop',      e => { e.preventDefault(); s.classList.remove('over'); placeCard(dragged, s); dragged = null; });
        s.addEventListener('click',     () => { if (selected && !s.classList.contains('filled')) { selected.classList.remove('selected'); placeCard(selected, s); selected = null; } });
    });
}

function placeCard(card, slot) {
    if (!card || card.classList.contains('placed') || slot.classList.contains('filled')) return;

    // If slot already has a child, return it to the tray first
    if (slot.firstElementChild) {
        const existing = slot.firstElementChild;
        existing.classList.remove('placed');
        existing.draggable = true;
        const trayId = slot.dataset.phase === 'ci' ? 'tray-ci' : 'tray-sq';
        document.getElementById(trayId).appendChild(existing);
        if (slot.dataset.phase === 'ci') ciPlaced--;
    }

    card.classList.add('placed');
    card.draggable = false;
    slot.appendChild(card);
    slot.classList.add('filled');

    if (slot.dataset.phase === 'ci') {
        ciPlaced++;
        if (ciPlaced === 4) {
            document.getElementById('ok-ci').classList.add('show');
        }
    }
}

function done(phase) {
    // Called when squares phase is fully placed
    document.getElementById('ok-' + phase).classList.add('show');
    document.getElementById('tab-' + phase).className = 'phase-tab done';
    setTimeout(() => {
        document.getElementById('phase-sq').style.display = 'none';
        document.getElementById('phase-ci').style.display = 'block';
        document.getElementById('tab-ci').className = 'phase-tab active';
        setup('tray-ci', 'slots-ci');
    }, 1500);
}

function isCirclesCorrect() {
    // Check each circle slot: the card inside must have data-size === slot number
    let correct = true;
    document.querySelectorAll('#slots-ci .slot').forEach(s => {
        const card = s.firstElementChild;
        if (!card || parseInt(card.dataset.size) !== parseInt(s.dataset.slot)) correct = false;
    });
    return correct;
}

function submitAnswer() {
    document.getElementById('responseInput').value = isCirclesCorrect() ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

// Squares: auto-advance after all 4 placed
let sqPlaced = 0;
function setupSquares() {
    document.querySelectorAll('#tray-sq .shape').forEach(c => {
        c.addEventListener('dragstart', () => { dragged = c; c.classList.add('dragging'); });
        c.addEventListener('dragend',   () => c.classList.remove('dragging'));
        c.addEventListener('click', () => {
            if (c.classList.contains('placed')) return;
            if (selected) selected.classList.remove('selected');
            selected = (selected === c) ? null : c;
            if (selected) selected.classList.add('selected');
        });
    });

    document.querySelectorAll('#slots-sq .slot').forEach(s => {
        s.addEventListener('dragover',  e => { e.preventDefault(); if (!s.classList.contains('filled')) s.classList.add('over'); });
        s.addEventListener('dragleave', () => s.classList.remove('over'));
        s.addEventListener('drop', e => {
            e.preventDefault(); s.classList.remove('over');
            placeSqCard(dragged, s); dragged = null;
        });
        s.addEventListener('click', () => {
            if (selected && !s.classList.contains('filled')) {
                selected.classList.remove('selected'); placeSqCard(selected, s); selected = null;
            }
        });
    });
}

function placeSqCard(card, slot) {
    if (!card || card.classList.contains('placed') || slot.classList.contains('filled')) return;

    if (slot.firstElementChild) {
        const existing = slot.firstElementChild;
        existing.classList.remove('placed');
        existing.draggable = true;
        document.getElementById('tray-sq').appendChild(existing);
        sqPlaced--;
    }

    card.classList.add('placed');
    card.draggable = false;
    slot.appendChild(card);
    slot.classList.add('filled');
    sqPlaced++;

    if (sqPlaced === 4) done('sq');
}

setupSquares();
</script>

</body>
</html>