<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sort the Shapes!</title>
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
            max-width: 960px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        .progress     { text-align: center; font-size: 14px; color: #7C3AED; font-weight: 700; margin-bottom: 1.5rem; }
        .domain-icon  { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
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

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.5rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        .tray-label {
            text-align: center; font-size: 0.9rem; font-weight: 800;
            color: #888; text-transform: uppercase; letter-spacing: 0.05rem; margin-bottom: 0.8rem;
        }

        .unsorted-tray {
            display: flex; flex-wrap: wrap; justify-content: center; gap: 14px;
            background: #f9f9f9; border: 2px dashed #ddd; border-radius: 16px;
            padding: 1.2rem; min-height: 110px; margin-bottom: 1.5rem;
        }

        .game-box.locked .unsorted-tray,
        .game-box.locked .sort-group { pointer-events: none; }

        .groups-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }

        .sort-group {
            border-radius: 16px; border: 3px dashed #e0e0e0; background: #fff;
            padding: 0.8rem 0.6rem; min-height: 160px;
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            transition: border-color 0.2s, background 0.2s; position: relative;
        }
        .sort-group.drag-over { border-color: #7C3AED; background: #f0ebff; }
        .sort-group.has-cards { border-color: #94A3B8; border-style: solid; }

        .group-label { font-size: 0.75rem; font-weight: 800; color: #bbb; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }

        .shape-card {
            width: 80px; height: 80px; border-radius: 14px; border: 3px solid #e0e0e0;
            background: #fff; display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 4px; cursor: grab; transition: all 0.25s;
            user-select: none; position: relative; box-shadow: 0 3px 10px rgba(0,0,0,0.08); flex-shrink: 0;
        }
        .shape-card:active                { cursor: grabbing; }
        .shape-card:hover:not(.placed)    { transform: scale(1.1) rotate(-3deg); box-shadow: 0 8px 20px rgba(0,0,0,0.16); border-color: #f5a623; }
        .shape-card.dragging              { opacity: 0.4; transform: scale(0.9); }
        .shape-card.placed                { cursor: grab; border-color: #94A3B8; background: #E2E8F0; opacity: 0.7; }
        .shape-card.placed:hover          { transform: scale(1.05); opacity: 1; border-color: #f5a623; }

        .game-box.locked .shape-card      { cursor: default; pointer-events: none; opacity: 0.72; }
        .game-box.locked .shape-card:hover { transform: none; }

        .shape-svg  { width: 44px; height: 44px; }
        .shape-name { font-size: 0.6rem; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 0.03em; }

        @keyframes popIn { 0%{transform:scale(0.8)} 60%{transform:scale(1.12)} 100%{transform:scale(1)} }

        .why-section {
            display: none; background: #EFF6FF; border: 2px solid #93C5FD;
            border-radius: 14px; padding: 1.2rem 1.5rem; margin-top: 1rem; text-align: center;
        }
        .why-section.show { display: block; }
        .why-question { font-size: 1rem; font-weight: 800; color: #1D4ED8; margin-bottom: 1rem; }
        .why-options  { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }

        .why-btn {
            padding: 10px 18px; border-radius: 25px; border: 2.5px solid #93C5FD;
            background: #fff; color: #1D4ED8; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
        }
        .why-btn:hover    { background: #DBEAFE; border-color: #3B82F6; transform: translateY(-2px); }
        .why-btn.selected { background: #2563EB; border-color: #2563EB; color: #fff; }

        .game-box.locked .why-btn { pointer-events: none; cursor: default; }
        .game-box.locked .why-btn:hover { transform: none; background: #fff; }
        .game-box.locked .why-btn.selected:hover { background: #2563EB; }

        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }
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

        @media (max-width: 768px) {
            .card { padding: 24px 16px; }
            .groups-row { grid-template-columns: repeat(2, 1fr); }
            .shape-card { width: 68px; height: 68px; }
            .shape-svg  { width: 36px; height: 36px; }
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

        <div class="game-title">🔷 Sort the Shapes!</div>
        <div class="game-subtitle">Drag each shape into a group box below!</div>

        <div class="tray-label">👆 Drag from here</div>
        <div class="unsorted-tray" id="unsortedTray">
            <div class="shape-card" draggable="true" data-shape="circle" id="c1">
                <svg class="shape-svg" viewBox="0 0 44 44"><circle cx="22" cy="22" r="19" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Circle</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="triangle" id="t1">
                <svg class="shape-svg" viewBox="0 0 44 44"><polygon points="22,4 42,40 2,40" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Triangle</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="square" id="s1">
                <svg class="shape-svg" viewBox="0 0 44 44"><rect x="3" y="3" width="38" height="38" rx="4" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Square</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="star" id="st1">
                <svg class="shape-svg" viewBox="0 0 44 44"><polygon points="22,2 27,16 42,16 30,25 35,40 22,31 9,40 14,25 2,16 17,16" fill="#60A5FA" stroke="#2563EB" stroke-width="1.5"/></svg>
                <span class="shape-name">Star</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="circle" id="c2">
                <svg class="shape-svg" viewBox="0 0 44 44"><circle cx="22" cy="22" r="19" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Circle</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="star" id="st2">
                <svg class="shape-svg" viewBox="0 0 44 44"><polygon points="22,2 27,16 42,16 30,25 35,40 22,31 9,40 14,25 2,16 17,16" fill="#60A5FA" stroke="#2563EB" stroke-width="1.5"/></svg>
                <span class="shape-name">Star</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="square" id="s2">
                <svg class="shape-svg" viewBox="0 0 44 44"><rect x="3" y="3" width="38" height="38" rx="4" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Square</span>
            </div>
            <div class="shape-card" draggable="true" data-shape="triangle" id="t2">
                <svg class="shape-svg" viewBox="0 0 44 44"><polygon points="22,4 42,40 2,40" fill="#60A5FA" stroke="#2563EB" stroke-width="2"/></svg>
                <span class="shape-name">Triangle</span>
            </div>
        </div>

        <div class="tray-label">👇 Drop into groups</div>
        <div class="groups-row">
            <div class="sort-group" data-group="circle" id="group-circle">
                <div class="group-label">Circles</div>
                <svg width="36" height="36" viewBox="0 0 44 44" style="opacity:0.15"><circle cx="22" cy="22" r="19" fill="#60A5FA"/></svg>
            </div>
            <div class="sort-group" data-group="triangle" id="group-triangle">
                <div class="group-label">Triangles</div>
                <svg width="36" height="36" viewBox="0 0 44 44" style="opacity:0.15"><polygon points="22,4 42,40 2,40" fill="#60A5FA"/></svg>
            </div>
            <div class="sort-group" data-group="square" id="group-square">
                <div class="group-label">Squares</div>
                <svg width="36" height="36" viewBox="0 0 44 44" style="opacity:0.15"><rect x="3" y="3" width="38" height="38" rx="4" fill="#60A5FA"/></svg>
            </div>
            <div class="sort-group" data-group="star" id="group-star">
                <div class="group-label">Stars</div>
                <svg width="36" height="36" viewBox="0 0 44 44" style="opacity:0.15"><polygon points="22,2 27,16 42,16 30,25 35,40 22,31 9,40 14,25 2,16 17,16" fill="#60A5FA"/></svg>
            </div>
        </div>

        <div class="why-section" id="whySection">
            <div class="why-question">Great job! 🎉 Why did you put these shapes together?</div>
            <div class="why-options">
                <button class="why-btn" onclick="selectWhy(this, 'same_shape')">They are the same shape</button>
                <button class="why-btn" onclick="selectWhy(this, 'same_color')">They look the same</button>
                <button class="why-btn" onclick="selectWhy(this, 'they_match')">They match each other</button>
                <button class="why-btn" onclick="selectWhy(this, 'idk')">I don't know</button>
            </div>
        </div>

    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Sort all shapes and answer why, then click Next</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                {{-- Starts locked — unlocks when all shapes placed + why answered --}}
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
            This will skip the sorting game and move to the next question.<br><br>
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

let draggedItem  = null;
let selectedItem = null;
let placedCount  = 0;
let whyValue     = null;
const totalCards = 8;

const cardGroup = { c1: null, c2: null, t1: null, t2: null, s1: null, s2: null, st1: null, st2: null };

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
    }
});

function applyLockedUI() {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('answerHint').style.display = 'none';

    // Next: unlock and navigate directly (no modal)
    const btnNext = document.getElementById('btnNext');
    btnNext.classList.remove('btn-locked');

    // Skip: lock — already answered
    if (btnSkip) btnSkip.classList.add('btn-locked');

    // Place all cards into correct groups to show completed state
    const correctMapping = {
        c1: 'circle', c2: 'circle',
        t1: 'triangle', t2: 'triangle',
        s1: 'square',   s2: 'square',
        st1: 'star',    st2: 'star'
    };
    for (const cardId in correctMapping) {
        const card  = document.getElementById(cardId);
        const group = document.getElementById('group-' + correctMapping[cardId]);
        if (!card || !group) continue;
        const tray = document.getElementById('unsortedTray');
        if (tray.contains(card)) tray.removeChild(card);
        card.classList.add('placed');
        group.appendChild(card);
        group.classList.add('has-cards');
        cardGroup[cardId] = correctMapping[cardId];
        placedCount++;
    }

    // Show why section with pre-selected answer
    document.getElementById('whySection').classList.add('show');
    const firstBtn = document.querySelector('.why-btn');
    if (firstBtn) firstBtn.classList.add('selected');
    whyValue = 'same_shape';
}

// ── Drag ──
document.querySelectorAll('.shape-card').forEach(card => {
    card.addEventListener('dragstart', function () {
        if (isLocked) return;
        draggedItem = this;
        this.classList.add('dragging');
    });
    card.addEventListener('dragend', function () {
        this.classList.remove('dragging');
    });
    card.addEventListener('click', function () {
        if (isLocked) return;
        if (selectedItem) selectedItem.style.outline = '';
        if (selectedItem === this) { selectedItem = null; return; }
        selectedItem = this;
        this.style.outline = '3px solid #7C3AED';
        this.style.outlineOffset = '3px';
    });
});

// ── Drop ──
document.querySelectorAll('.sort-group').forEach(group => {
    group.addEventListener('dragover',  e => { if (isLocked) return; e.preventDefault(); group.classList.add('drag-over'); });
    group.addEventListener('dragleave', () => group.classList.remove('drag-over'));
    group.addEventListener('drop', e => {
        e.preventDefault();
        group.classList.remove('drag-over');
        if (!isLocked && draggedItem) { placeCard(draggedItem, group); draggedItem = null; }
    });
    group.addEventListener('click', () => {
        if (isLocked || !selectedItem) return;
        selectedItem.style.outline = '';
        placeCard(selectedItem, group);
        selectedItem = null;
    });
});

// ── Place card ──
function placeCard(card, group) {
    const cardId      = card.id;
    const groupId     = group.dataset.group;
    const prevGroupId = cardGroup[cardId];

    if (prevGroupId) {
        const prevGroup = document.getElementById('group-' + prevGroupId);
        prevGroup.removeChild(card);
        if (!prevGroup.querySelector('.shape-card')) prevGroup.classList.remove('has-cards');
        placedCount--;
    } else {
        const tray = document.getElementById('unsortedTray');
        if (tray.contains(card)) tray.removeChild(card);
    }

    card.classList.add('placed');
    card.style.outline = '';
    group.appendChild(card);
    group.classList.add('has-cards');
    cardGroup[cardId] = groupId;
    placedCount++;

    if (placedCount === totalCards) {
        document.getElementById('whySection').classList.add('show');
    }

    updateNextButton();
}

// ── Why answer ──
function selectWhy(btn, value) {
    if (isLocked) return;
    document.querySelectorAll('.why-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    whyValue = value;
    updateNextButton();
}

// ── Update Next button lock state ──
function updateNextButton() {
    if (isLocked) return;
    const btnNext = document.getElementById('btnNext');
    const ready = placedCount === totalCards && whyValue !== null;
    if (ready) {
        btnNext.classList.remove('btn-locked');
        document.getElementById('answerHint').textContent = 'All done! Click Next → to submit.';
    } else {
        btnNext.classList.add('btn-locked');
        if (placedCount < totalCards) {
            const rem = totalCards - placedCount;
            document.getElementById('answerHint').textContent =
                `${rem} shape${rem > 1 ? 's' : ''} left to sort — then answer why!`;
        } else {
            document.getElementById('answerHint').textContent = 'Now answer the "Why?" question to unlock Next →';
        }
    }
}

// ── Correctness check ──
function isSortedCorrectly() {
    for (const cardId in cardGroup) {
        const groupId = cardGroup[cardId];
        if (!groupId) return false;
        if (document.getElementById(cardId).dataset.shape !== groupId) return false;
    }
    return true;
}

// ── Confirm modal ──
function openConfirmModal()  { document.getElementById('confirmModal').classList.add('show'); }
function closeConfirmModal() { document.getElementById('confirmModal').classList.remove('show'); }

function confirmSubmit() {
    closeConfirmModal();
    const ok = isSortedCorrectly();
    document.getElementById('responseInput').value = ok ? 'yes' : 'no';
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
</script>

</body>
</html>