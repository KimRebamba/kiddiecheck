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

        /* ── Dashboard button ── */
        .btn-dashboard {
            position: fixed;
            top: 16px;
            right: 20px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            color: #555;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.45rem 1rem;
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
            z-index: 100;
            backdrop-filter: blur(4px);
        }
        .btn-dashboard:hover {
            background: rgba(255,255,255,0.95);
            color: #222;
            text-decoration: none;
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

        .progress      { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon   { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title  { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text { text-align: center; font-size: 18px; color: #555; line-height: 1.6; margin-bottom: 1.5rem; }

        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
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

        .game-box.locked .shape       { cursor: default; pointer-events: none; opacity: 0.72; }
        .game-box.locked .shape:hover { transform: none; box-shadow: 0 3px 10px rgba(0,0,0,0.1); border-color: #e0e0e0; }
        .game-box.locked .slot        { pointer-events: none; }
        .game-box.locked .tray        { pointer-events: none; }

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.4rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        .phase-tabs { display: flex; justify-content: center; gap: 12px; margin-bottom: 1.5rem; }
        .phase-tab  { padding: 8px 22px; border-radius: 999px; font-size: 0.85rem; font-weight: 800; border: 2px solid #e0e0e0; background: #f5f5f5; color: #aaa; }
        .phase-tab.active { background: #7C3AED; border-color: #7C3AED; color: #fff; }
        .phase-tab.done   { background: #4CAF50; border-color: #4CAF50; color: #fff; }

        .note {
            border-radius: 12px; padding: 0.8rem 1.2rem; font-size: 0.85rem;
            font-weight: 600; text-align: center; margin-bottom: 1.2rem;
            background: #EFF6FF; border: 2px solid #93C5FD; color: #1D4ED8;
        }
        .note.child { background: #FFF7ED; border-color: #FCD34D; color: #92400E; }

        .tray-label { text-align: center; font-size: 0.85rem; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 0.05rem; margin-bottom: 0.7rem; }
        .tray {
            display: flex; justify-content: center; align-items: center;
            gap: 18px; flex-wrap: wrap; background: #f9f9f9;
            border: 2px dashed #ddd; border-radius: 16px;
            padding: 1rem 1.5rem; min-height: 120px; margin-bottom: 1.2rem;
            transition: border-color 0.2s, background 0.2s;
        }
        .tray.over { border-color: #f5a623; background: #fffbea; }

        .slots { display: flex; justify-content: center; align-items: center; gap: 18px; flex-wrap: wrap; }
        .slot-wrap { display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .slot-label { font-size: 0.65rem; font-weight: 900; color: #ccc; text-transform: uppercase; letter-spacing: 0.05em; }

        .slot {
            width: 120px; height: 120px;
            border: 3px dashed #e0e0e0; border-radius: 14px;
            background: #f9f9f9; display: flex; align-items: center;
            justify-content: center; transition: border-color 0.2s, background 0.2s;
        }
        .slot.over   { border-color: #7C3AED; background: #f0ebff; }
        .slot.filled { border-color: #94A3B8; background: #f5f5f5; }

        .shape {
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; border: 3px solid #e0e0e0; background: #fff;
            cursor: grab; transition: all 0.25s; user-select: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1); flex-shrink: 0;
        }
        .shape:hover    { transform: scale(1.1) rotate(-3deg); box-shadow: 0 8px 20px rgba(0,0,0,0.18); border-color: #f5a623; }
        .shape.dragging { opacity: 0.4; transform: scale(0.9); }
        .shape.placed   { border-color: #94A3B8; background: #f5f5f5; animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
        .shape.placed:hover { transform: scale(1.06); border-color: #7C3AED; cursor: grab; }
        .shape.selected { outline: 3px solid #7C3AED; outline-offset: 3px; }

        .sz1 { width: 52px;  height: 52px; }
        .sz2 { width: 70px;  height: 70px; }
        .sz3 { width: 88px;  height: 88px; }
        .sz4 { width: 106px; height: 106px; }

        .success { display: none; text-align: center; font-size: 1.1rem; color: #4CAF50; font-weight: 900; margin-top: 1rem; padding: 1rem; background: #f0fff4; border-radius: 14px; border: 3px solid #4CAF50; }
        .success.show { display: block; }

        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }
        .nav-footer  { display: flex; justify-content: space-between; align-items: center; margin-top: 1.2rem; }
        .nav-center  { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333; transition: all 0.2s;
        }
        .btn-nav:hover { background: #f5f5f5; transform: translateY(-2px); }
        .btn-prev      { background: #f5f5f5; border-color: #999; color: #666; }
        .btn-prev:hover { background: #e0e0e0; color: #333; }

        .btn-nav.btn-locked {
            background: #e9e9e9; border-color: #ccc; color: #999;
            cursor: not-allowed; pointer-events: none;
        }
        .btn-nav.btn-locked:hover { transform: none; background: #e9e9e9; }

        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center;
        }
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

        @keyframes popIn { 0%{transform:scale(0.8)} 60%{transform:scale(1.15)} 100%{transform:scale(1)} }

        @media (max-width: 640px) {
            .card { padding: 24px 16px; }
            .slot { width: 80px; height: 80px; }
            .sz1 { width: 38px; height: 38px; } .sz2 { width: 52px; height: 52px; }
            .sz3 { width: 66px; height: 66px; } .sz4 { width: 80px; height: 80px; }
            .btn-dashboard { font-size: 0.78rem; padding: 0.38rem 0.8rem; }
        }
    </style>
</head>
<body>

{{-- Dashboard button fixed top right --}}
<a href="{{ route('family.index') }}" class="btn-dashboard">
    🏠 Dashboard
</a>

<div class="card">

    <div class="progress">{{ $totalAnswered }} of {{ $totalQuestions }} answered</div>
    <div class="domain-icon">🧠</div>
    <div class="domain-title">{{ $currentDomain->domain_name }}</div>
    <div class="question-text">{{ $question->display_text ?? $question->text }}</div>

    <div class="game-box" id="gameBox">

        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

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

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Order the squares first, then the circles to unlock Next →</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility:hidden">← Previous</span>
            @endif

            <div class="nav-center">
                {{-- Starts locked — unlocks only when all circles placed --}}
                <button type="button" id="btnNext" class="btn-nav btn-locked">Next →</button>

                @if($nextDomain && $nextIndex)
                    <a href="#"
                       data-skip-url="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}"
                       class="btn-nav" id="btnSkip">Skip (Answer Later)</a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">Review →</a>
                @endif
            </div>
        </div>
    </form>

</div>

{{-- Confirm-submit modal --}}
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">
            Next means submitting the answer and not returning to it.<br><br>
            Are you sure you want to submit?
        </div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSubmit()">Yes, Submit</button>
        </div>
    </div>
</div>

{{-- Skip warning modal --}}
<div class="modal-overlay" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">
            This will skip the ordering game and move to the next question.<br><br>
            You can come back to it later.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeSkipModal()">Cancel</button>
            <button class="btn-modal-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
const existingResponse = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existingResponse !== '';

let dragged  = null;
let selected = null;

const placedCount = { sq: 0, ci: 0 };

const nextUrl = "{{ $nextDomain && $nextIndex ? route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) : route('family.tests.result', $testId) }}";

// ── Capture skip URL once from data attribute ──
const btnSkip = document.getElementById('btnSkip');
const skipUrl = btnSkip ? btnSkip.dataset.skipUrl : null;

window.addEventListener('DOMContentLoaded', () => {

    // ── Attach skip listener once, reliably ──
    if (btnSkip && skipUrl) {
        btnSkip.addEventListener('click', function(e) {
            e.preventDefault();
            if (btnSkip.classList.contains('btn-locked')) return;
            openSkipModal();
        });
    }

    // ── Attach next listener once ──
    document.getElementById('btnNext').addEventListener('click', function() {
        if (this.classList.contains('btn-locked')) return;
        if (isLocked) { window.location.href = nextUrl; return; }
        openConfirmModal();
    });

    if (isLocked) {
        applyLockedUI();
    } else {
        setupPhase('sq');
    }
});

function applyLockedUI() {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('answerHint').style.display = 'none';

    // Next: unlock — navigates directly, no modal
    document.getElementById('btnNext').classList.remove('btn-locked');

    // Skip: lock — already answered
    if (btnSkip) btnSkip.classList.add('btn-locked');

    // Place squares in correct order
    const sqSlots = document.querySelectorAll('#slots-sq .slot');
    const sqCards = [...document.querySelectorAll('#tray-sq .shape')].sort((a, b) => a.dataset.size - b.dataset.size);
    sqCards.forEach((card, i) => {
        card.classList.add('placed');
        sqSlots[i].appendChild(card);
        sqSlots[i].classList.add('filled');
    });
    document.getElementById('ok-sq').classList.add('show');
    document.getElementById('tab-sq').className = 'phase-tab done';

    // Show circles phase
    document.getElementById('phase-sq').style.display = 'none';
    document.getElementById('phase-ci').style.display = 'block';
    document.getElementById('tab-ci').className = 'phase-tab active';

    // Place circles in correct order
    const ciSlots = document.querySelectorAll('#slots-ci .slot');
    const ciCards = [...document.querySelectorAll('#tray-ci .shape')].sort((a, b) => a.dataset.size - b.dataset.size);
    ciCards.forEach((card, i) => {
        card.classList.add('placed');
        ciSlots[i].appendChild(card);
        ciSlots[i].classList.add('filled');
    });
    document.getElementById('ok-ci').classList.add('show');
}

// ── Generic phase setup ──
function setupPhase(phase) {
    const trayEl  = document.getElementById('tray-' + phase);
    const slotsEl = document.getElementById('slots-' + phase);

    trayEl.querySelectorAll('.shape').forEach(card => bindCard(card, phase));

    trayEl.addEventListener('dragover',  e => { e.preventDefault(); trayEl.classList.add('over'); });
    trayEl.addEventListener('dragleave', () => trayEl.classList.remove('over'));
    trayEl.addEventListener('drop', e => {
        e.preventDefault();
        trayEl.classList.remove('over');
        if (dragged) { returnToTray(dragged, phase); dragged = null; }
    });

    slotsEl.querySelectorAll('.slot').forEach(slot => {
        slot.addEventListener('dragover',  e => { e.preventDefault(); slot.classList.add('over'); });
        slot.addEventListener('dragleave', () => slot.classList.remove('over'));
        slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('over');
            if (dragged) { placeCard(dragged, slot, phase); dragged = null; }
        });
        slot.addEventListener('click', () => {
            if (!selected) return;
            clearSelected();
            placeCard(selected, slot, phase);
            selected = null;
        });
    });
}

function bindCard(card, phase) {
    card.addEventListener('dragstart', () => {
        if (isLocked) return;
        dragged = card;
        card.classList.add('dragging');
    });
    card.addEventListener('dragend', () => card.classList.remove('dragging'));

    card.addEventListener('click', e => {
        if (isLocked) return;
        e.stopPropagation();
        if (selected && selected !== card) {
            if (card.classList.contains('placed')) {
                const slot = card.parentElement;
                if (slot && slot.classList.contains('slot')) {
                    returnToTray(card, phase);
                    clearSelected();
                    placeCard(selected, slot, phase);
                    selected = null;
                    return;
                }
            }
            clearSelected();
        }
        if (selected === card) { clearSelected(); selected = null; return; }
        selected = card;
        card.classList.add('selected');
    });
}

function clearSelected() {
    if (selected) selected.classList.remove('selected');
}

function placeCard(card, slot, phase) {
    if (!card) return;

    if (slot.classList.contains('filled') && slot.firstElementChild) {
        returnToTray(slot.firstElementChild, phase);
    }

    if (card.classList.contains('placed')) {
        const oldSlot = card.parentElement;
        if (oldSlot && oldSlot.classList.contains('slot')) {
            oldSlot.classList.remove('filled');
            placedCount[phase]--;
        }
    }

    card.classList.add('placed');
    card.classList.remove('selected');
    slot.appendChild(card);
    slot.classList.add('filled');
    placedCount[phase]++;

    document.getElementById('ok-' + phase).classList.remove('show');

    if (placedCount[phase] === 4) {
        if (phase === 'sq') {
            advanceToCircles();
        } else {
            document.getElementById('ok-ci').classList.add('show');
            updateNextButton();
        }
    } else {
        updateNextButton();
    }
}

function returnToTray(card, phase) {
    if (!card) return;
    const trayEl = document.getElementById('tray-' + phase);

    if (card.classList.contains('placed')) {
        const oldSlot = card.parentElement;
        if (oldSlot && oldSlot.classList.contains('slot')) {
            oldSlot.classList.remove('filled');
            placedCount[phase]--;
        }
    }

    card.classList.remove('placed', 'selected');
    trayEl.appendChild(card);
    document.getElementById('ok-' + phase).classList.remove('show');
    updateNextButton();
}

function advanceToCircles() {
    document.getElementById('ok-sq').classList.add('show');
    document.getElementById('tab-sq').className = 'phase-tab done';
    setTimeout(() => {
        document.getElementById('phase-sq').style.display = 'none';
        document.getElementById('phase-ci').style.display = 'block';
        document.getElementById('tab-ci').className = 'phase-tab active';
        setupPhase('ci');
        updateNextButton();
    }, 1500);
}

// ── Update Next button lock state ──
function updateNextButton() {
    if (isLocked) return;
    const btnNext = document.getElementById('btnNext');
    if (placedCount['ci'] === 4) {
        btnNext.classList.remove('btn-locked');
        document.getElementById('answerHint').textContent = 'All done! Click Next → to submit.';
    } else {
        btnNext.classList.add('btn-locked');
        if (placedCount['sq'] < 4) {
            document.getElementById('answerHint').textContent = 'Order the squares first, then the circles to unlock Next →';
        } else {
            const rem = 4 - placedCount['ci'];
            document.getElementById('answerHint').textContent =
                `${rem} circle${rem > 1 ? 's' : ''} left to place — fill all slots to unlock Next →`;
        }
    }
}

// ── Correctness check ──
function isPhaseCorrect(phase) {
    let correct = true;
    document.querySelectorAll(`#slots-${phase} .slot`).forEach(s => {
        const card = s.firstElementChild;
        if (!card || parseInt(card.dataset.size) !== parseInt(s.dataset.slot)) correct = false;
    });
    return correct;
}

// ── Confirm modal ──
function openConfirmModal()  { document.getElementById('confirmModal').classList.add('show'); }
function closeConfirmModal() { document.getElementById('confirmModal').classList.remove('show'); }

function confirmSubmit() {
    closeConfirmModal();
    const bothCorrect = isPhaseCorrect('sq') && isPhaseCorrect('ci');
    document.getElementById('responseInput').value = bothCorrect ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});

// ── Skip modal ──
function openSkipModal()  { document.getElementById('skipModal').classList.add('show'); }
function closeSkipModal() { document.getElementById('skipModal').classList.remove('show'); }

function doSkip() {
    closeSkipModal();
    window.location.href = skipUrl;
}

document.getElementById('skipModal').addEventListener('click', function(e) {
    if (e.target === this) closeSkipModal();
});

// Dismiss selection when clicking outside
document.addEventListener('click', () => {
    if (selected) { clearSelected(); selected = null; }
});
</script>

</body>
</html>