<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match the Pictures!</title>
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
            padding: 2rem 96px 1.5rem;
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

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 2rem; }

        .dots-arena {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            min-height: 320px;
        }

        .lines-svg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            overflow: visible;
            z-index: 1;
        }

        .connection-line {
            stroke-width: 6;
            stroke-linecap: round;
            fill: none;
            opacity: 0.9;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));
        }

        .dots-col {
            display: flex;
            flex-direction: column;
            gap: 1.6rem;
            z-index: 2;
            width: 38%;
            align-items: center;
        }

        .col-header {
            font-size: 0.78rem;
            font-weight: 900;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.2rem;
            text-align: center;
        }

        .dot-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            cursor: pointer;
            transition: transform 0.15s;
            user-select: none;
        }
        .dot-item:hover { transform: scale(1.06); }

        .dots-arena.is-locked .dot-item       { cursor: default; pointer-events: none; }
        .dots-arena.is-locked .dot-item:hover { transform: none; }
        .dots-arena.is-locked .dot            { opacity: 0.75; }

        .dot {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            border: 4px solid rgba(0,0,0,0.12);
            background: #fff;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            position: relative;
        }

        .dot-emoji { font-size: 2.4rem; line-height: 1; }
        .dot-label { font-size: 0.7rem; font-weight: 800; color: #666; text-transform: uppercase; letter-spacing: 0.04em; }

        .dot.selected {
            box-shadow: 0 0 0 5px #7C3AED, 0 4px 14px rgba(0,0,0,0.18);
            transform: scale(1.12);
            border-color: #7C3AED;
        }
        .dot.connected {
            border-color: #555;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.12), 0 4px 14px rgba(0,0,0,0.12);
        }
        .dot.connected::after {
            content: '✓';
            position: absolute;
            top: -8px; right: -8px;
            background: #555; color: #fff;
            font-size: 0.65rem; font-weight: 900;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        .tap-hint    { text-align: center; font-size: 0.82rem; color: #bbb; margin-top: 1.2rem; }
        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        .nav-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center { display: flex; gap: 10px; }

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

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .game-box { padding: 1.5rem 40px 1.5rem; }
            .dot { width: 72px; height: 72px; }
            .dot-emoji { font-size: 1.9rem; }
            .dot-label { font-size: 0.6rem; }
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

        <div class="game-title">🧩 Match the Pictures!</div>
        <div class="game-subtitle">Tap a picture on the left, then tap its match on the right</div>

        <div class="dots-arena" id="arena">
            <svg class="lines-svg" id="linesSvg"></svg>

            <!-- LEFT column -->
            <div class="dots-col left">
                <div class="col-header">👈 Tap to start</div>
                <div class="dot-item" data-side="left" data-pic="apple">
                    <div class="dot" id="dot-left-apple">
                        <span class="dot-emoji">🍎</span>
                        <span class="dot-label">Apple</span>
                    </div>
                </div>
                <div class="dot-item" data-side="left" data-pic="banana">
                    <div class="dot" id="dot-left-banana">
                        <span class="dot-emoji">🍌</span>
                        <span class="dot-label">Banana</span>
                    </div>
                </div>
                <div class="dot-item" data-side="left" data-pic="orange">
                    <div class="dot" id="dot-left-orange">
                        <span class="dot-emoji">🍊</span>
                        <span class="dot-label">Orange</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT column (shuffled order) -->
            <div class="dots-col right">
                <div class="col-header">Tap to match 👉</div>
                <div class="dot-item" data-side="right" data-pic="banana">
                    <div class="dot" id="dot-right-banana">
                        <span class="dot-emoji">🍌</span>
                        <span class="dot-label">Banana</span>
                    </div>
                </div>
                <div class="dot-item" data-side="right" data-pic="orange">
                    <div class="dot" id="dot-right-orange">
                        <span class="dot-emoji">🍊</span>
                        <span class="dot-label">Orange</span>
                    </div>
                </div>
                <div class="dot-item" data-side="right" data-pic="apple">
                    <div class="dot" id="dot-right-apple">
                        <span class="dot-emoji">🍎</span>
                        <span class="dot-label">Apple</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tap-hint" id="tapHint">Tap a connected picture again to remove its line</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Connect all 3 pictures to unlock Next →</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                {{-- Starts locked — unlocks when all 3 pictures connected --}}
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
            This will skip the picture matching game and move to the next question.<br><br>
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

const pics        = ['apple', 'banana', 'orange'];
const connections = { apple: null, banana: null, orange: null };
let selectedLeft  = null;

const lineColors = { apple: '#e74c3c', banana: '#e6a800', orange: '#e67e22' };
const svg        = document.getElementById('linesSvg');
const arena      = document.getElementById('arena');

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

    if (isLocked) applyLockedUI();
});

function applyLockedUI() {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    arena.classList.add('is-locked');
    document.getElementById('tapHint').style.display    = 'none';
    document.getElementById('answerHint').style.display = 'none';

    // Next: unlock — navigates directly, no modal
    document.getElementById('btnNext').classList.remove('btn-locked');

    // Skip: lock — already answered
    if (btnSkip) btnSkip.classList.add('btn-locked');

    // Draw all correct connections
    pics.forEach(p => { connections[p] = p; });
    refreshDots();
    redrawLines();
}

function dotCenter(el) {
    const ar = arena.getBoundingClientRect();
    const dr = el.getBoundingClientRect();
    return { x: dr.left + dr.width / 2 - ar.left, y: dr.top + dr.height / 2 - ar.top };
}

function redrawLines() {
    svg.querySelectorAll('.connection-line').forEach(l => l.remove());
    pics.forEach(lp => {
        const rp = connections[lp];
        if (!rp) return;
        const ld = document.getElementById(`dot-left-${lp}`);
        const rd = document.getElementById(`dot-right-${rp}`);
        if (!ld || !rd) return;
        const lPos = dotCenter(ld), rPos = dotCenter(rd);
        const cx = (lPos.x + rPos.x) / 2;
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M ${lPos.x} ${lPos.y} C ${cx} ${lPos.y}, ${cx} ${rPos.y}, ${rPos.x} ${rPos.y}`);
        path.setAttribute('class', 'connection-line');
        path.setAttribute('stroke', lineColors[lp]);
        svg.appendChild(path);
    });
}

function refreshDots() {
    const allDone = pics.every(p => connections[p] !== null);
    const btnNext = document.getElementById('btnNext');

    if (!isLocked) {
        if (allDone) {
            btnNext.classList.remove('btn-locked');
            if (btnSkip) btnSkip.classList.remove('btn-locked');
            document.getElementById('answerHint').textContent = 'All done! Click Next → to submit.';
        } else {
            btnNext.classList.add('btn-locked');
            if (btnSkip) btnSkip.classList.remove('btn-locked');
            const remaining = pics.filter(p => connections[p] === null).length;
            document.getElementById('answerHint').textContent =
                `${remaining} picture${remaining > 1 ? 's' : ''} left — connect all to unlock Next →`;
        }
    }

    pics.forEach(p => {
        const ld = document.getElementById(`dot-left-${p}`);
        const rd = document.getElementById(`dot-right-${p}`);
        ld.classList.toggle('selected',  selectedLeft === p);
        ld.classList.toggle('connected', connections[p] !== null && selectedLeft !== p);
        rd.classList.toggle('connected', Object.values(connections).includes(p));
    });
}

document.querySelectorAll('.dot-item').forEach(item => {
    item.addEventListener('click', () => {
        if (isLocked) return;

        const side = item.dataset.side;
        const pic  = item.dataset.pic;

        if (side === 'left') {
            if (connections[pic] !== null) {
                connections[pic] = null;
                selectedLeft = pic;
            } else {
                selectedLeft = (selectedLeft === pic) ? null : pic;
            }
            refreshDots();
            redrawLines();
            return;
        }

        // right side
        const existingLeft = Object.keys(connections).find(lp => connections[lp] === pic);
        if (existingLeft) {
            connections[existingLeft] = null;
            if (selectedLeft && selectedLeft !== existingLeft) {
                connections[selectedLeft] = pic;
                selectedLeft = null;
            } else {
                selectedLeft = null;
            }
        } else {
            if (!selectedLeft) return;
            connections[selectedLeft] = pic;
            selectedLeft = null;
        }
        refreshDots();
        redrawLines();
    });
});

window.addEventListener('resize', redrawLines);

// ── Confirm modal ──
function openConfirmModal()  { document.getElementById('confirmModal').classList.add('show'); }
function closeConfirmModal() { document.getElementById('confirmModal').classList.remove('show'); }

function confirmSubmit() {
    closeConfirmModal();
    const isCorrect = pics.every(p => connections[p] === p);
    document.getElementById('responseInput').value = isCorrect ? 'yes' : 'no';
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