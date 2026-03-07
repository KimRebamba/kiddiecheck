<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match the Objects!</title>
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
            padding: 2rem 96px 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        /* Locked overlay */
        .game-box.locked {
            background: #f8f8f8;
            border-color: #ccc;
        }

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

        .game-title    { text-align: center; font-size: 1.1rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { text-align: center; font-size: 0.82rem; color: #aaa; margin-bottom: 2rem; }

        /* ── Connect arena ── */
        .dots-arena {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            min-height: 420px;
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

        /* ── Dot columns ── */
        .dots-col {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
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

        /* ── Each dot item ── */
        .dot-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: transform 0.15s;
            user-select: none;
        }
        .dot-item:hover { transform: scale(1.06); }

        /* Locked: disable interaction */
        .dots-arena.is-locked .dot-item {
            cursor: default;
            pointer-events: none;
        }
        .dots-arena.is-locked .dot-item:hover { transform: none; }

        /* ── Object card (the "dot") ── */
        .dot {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            border: 4px solid rgba(0,0,0,0.1);
            background: #fff;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            position: relative;
        }

        .dot-emoji { font-size: 2.4rem; line-height: 1; pointer-events: none; }
        .dot-label { font-size: 0.68rem; font-weight: 800; color: #777; text-transform: uppercase; letter-spacing: 0.04em; pointer-events: none; }

        /* Left card tints */
        .dot.tint-1 { background: #fff0f9; border-color: #ffb3d9; }
        .dot.tint-2 { background: #fff8ee; border-color: #ffd194; }
        .dot.tint-3 { background: #f0f8ff; border-color: #99d6ff; }
        .dot.tint-4 { background: #f5fff0; border-color: #a8e6a3; }

        /* Locked tint override */
        .dots-arena.is-locked .dot.tint-1,
        .dots-arena.is-locked .dot.tint-2,
        .dots-arena.is-locked .dot.tint-3,
        .dots-arena.is-locked .dot.tint-4,
        .dots-arena.is-locked .dot { opacity: 0.75; }

        /* States */
        .dot.selected {
            box-shadow: 0 0 0 5px #7C3AED, 0 4px 14px rgba(0,0,0,0.18);
            transform: scale(1.12);
            border-color: #7C3AED !important;
        }
        .dot.connected {
            border-color: #555 !important;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.12), 0 4px 14px rgba(0,0,0,0.1);
        }
        .dot.connected::after {
            content: '✓';
            position: absolute;
            top: -8px; right: -8px;
            background: #555;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 900;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tap hint */
        .tap-hint { text-align: center; font-size: 0.82rem; color: #bbb; margin-top: 1.2rem; }

        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

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
        .btn-prev:hover { background: #e0e0e0; color: #333; }

        /* Locked Next button */
        .btn-nav.btn-locked {
            background: #e9e9e9;
            border-color: #ccc;
            color: #999;
            cursor: not-allowed;
        }
        .btn-nav.btn-locked:hover { transform: none; background: #e9e9e9; }

        /* ── Modal overlay ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show { display: flex; }

        .modal-box {
            background: #fff;
            border-radius: 24px;
            padding: 36px 40px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            border: 3px solid #000;
            text-align: center;
            animation: popIn 0.25s ease;
        }

        @keyframes popIn {
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
        }
    </style>
</head>
<body>
<div class="card">

    <div class="progress"><?php echo e($totalAnswered); ?> of <?php echo e($totalQuestions); ?> answered</div>
    <div class="domain-icon">🧠</div>
    <div class="domain-title"><?php echo e($currentDomain->domain_name); ?></div>
    <div class="question-text"><?php echo e($question->display_text ?? $question->text); ?></div>

    <div class="game-box" id="gameBox">

        
        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

        <div class="game-title">🎮 Match the Objects!</div>
        <div class="game-subtitle">Tap an item on the left, then tap its match on the right</div>

        <div class="dots-arena" id="arena">

            <!-- SVG lines layer -->
            <svg class="lines-svg" id="linesSvg"></svg>

            <!-- LEFT column -->
            <div class="dots-col">
                <div class="col-header">👈 Tap to start</div>

                <div class="dot-item" data-side="left" data-item="spoon">
                    <div class="dot tint-1" id="dot-left-spoon">
                        <span class="dot-emoji">🥄</span>
                        <span class="dot-label">Spoon</span>
                    </div>
                </div>

                <div class="dot-item" data-side="left" data-item="block">
                    <div class="dot tint-2" id="dot-left-block">
                        <span class="dot-emoji">🧱</span>
                        <span class="dot-label">Block</span>
                    </div>
                </div>

                <div class="dot-item" data-side="left" data-item="ball">
                    <div class="dot tint-3" id="dot-left-ball">
                        <span class="dot-emoji">🔵</span>
                        <span class="dot-label">Ball</span>
                    </div>
                </div>

                <div class="dot-item" data-side="left" data-item="star">
                    <div class="dot tint-4" id="dot-left-star">
                        <span class="dot-emoji">⭐</span>
                        <span class="dot-label">Star</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT column (shuffled) -->
            <div class="dots-col">
                <div class="col-header">Tap to match 👉</div>

                <div class="dot-item" data-side="right" data-item="ball">
                    <div class="dot" id="dot-right-ball">
                        <span class="dot-emoji">🔵</span>
                        <span class="dot-label">Ball</span>
                    </div>
                </div>

                <div class="dot-item" data-side="right" data-item="spoon">
                    <div class="dot" id="dot-right-spoon">
                        <span class="dot-emoji">🥄</span>
                        <span class="dot-label">Spoon</span>
                    </div>
                </div>

                <div class="dot-item" data-side="right" data-item="star">
                    <div class="dot" id="dot-right-star">
                        <span class="dot-emoji">⭐</span>
                        <span class="dot-label">Star</span>
                    </div>
                </div>

                <div class="dot-item" data-side="right" data-item="block">
                    <div class="dot" id="dot-right-block">
                        <span class="dot-emoji">🧱</span>
                        <span class="dot-label">Block</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="tap-hint" id="tapHint">Tap a connected item again to remove its line</div>
    </div>

    <form method="POST"
          action="<?php echo e(route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex])); ?>"
          id="answerForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Connect all 4 items, then click Next →</div>

        <div class="nav-footer">
            <?php if($prevDomain && $prevIndex): ?>
                <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex])); ?>"
                   class="btn-nav btn-prev">← Previous</a>
            <?php else: ?>
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            <?php endif; ?>

            <div class="nav-center">
                
                <button type="button" id="btnNext" onclick="handleNext()" class="btn-nav">Next →</button>

                <?php if($nextDomain && $nextIndex): ?>
                    <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex])); ?>"
                       class="btn-nav">Skip (Answer Later)</a>
                <?php else: ?>
                    <a href="<?php echo e(route('family.tests.result', $testId)); ?>" class="btn-nav">Review →</a>
                <?php endif; ?>
            </div>
        </div>
    </form>

</div>


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
            <button class="btn-modal-ok" onclick="confirmSubmit()">Yes, Submit</button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">
            Clicking <strong>Next</strong> doesn't allow you to go back and answer it again.<br><br>
            Your previous answer has been saved and is now locked.
        </div>
        <div class="modal-actions">
            <button class="btn-modal-ok" onclick="closeLockedModal()">Got it!</button>
        </div>
    </div>
</div>

<script>
// ── Pass existing response from Blade ──
const existingResponse = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existingResponse !== '';

const items       = ['spoon', 'block', 'ball', 'star'];
// Pre-fill connections: if already answered, draw all correct lines (identity match)
const connections = { spoon: null, block: null, ball: null, star: null };
let selectedLeft  = null;

const lineColors = { spoon: '#e91e8c', block: '#f5a623', ball: '#3498db', star: '#8bc34a' };

const svg   = document.getElementById('linesSvg');
const arena = document.getElementById('arena');

// ── On page load: apply locked state if already answered ──
window.addEventListener('DOMContentLoaded', () => {
    if (isLocked) {
        applyLockedUI();
    }
});

function applyLockedUI() {
    // Show banner inside game box
    document.getElementById('lockedBanner').classList.add('visible');

    // Style the game box
    document.getElementById('gameBox').classList.add('locked');

    // Disable all dot interactions
    arena.classList.add('is-locked');

    // Hide tap hint; update answer hint
    document.getElementById('tapHint').style.display   = 'none';
    document.getElementById('answerHint').style.display = 'none';

    // Mark Next button as visually locked (still clickable to show modal)
    document.getElementById('btnNext').classList.add('btn-locked');

    // Draw all connections as if the child matched everything correctly
    // (we only store yes/no, so we always show the correct full matching)
    items.forEach(i => { connections[i] = i; });
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
    items.forEach(li => {
        const ri = connections[li];
        if (!ri) return;
        const ld = document.getElementById(`dot-left-${li}`);
        const rd = document.getElementById(`dot-right-${ri}`);
        if (!ld || !rd) return;
        const lp = dotCenter(ld), rp = dotCenter(rd);
        const cx = (lp.x + rp.x) / 2;
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M ${lp.x} ${lp.y} C ${cx} ${lp.y}, ${cx} ${rp.y}, ${rp.x} ${rp.y}`);
        path.setAttribute('class', 'connection-line');
        path.setAttribute('stroke', lineColors[li]);
        svg.appendChild(path);
    });
}

function refreshDots() {
    items.forEach(i => {
        const ld = document.getElementById(`dot-left-${i}`);
        const rd = document.getElementById(`dot-right-${i}`);
        ld.classList.toggle('selected',  selectedLeft === i);
        ld.classList.toggle('connected', connections[i] !== null && selectedLeft !== i);
        rd.classList.toggle('connected', Object.values(connections).includes(i));
    });
}

// ── Interaction (disabled when locked via pointer-events: none on arena) ──
document.querySelectorAll('.dot-item').forEach(item => {
    item.addEventListener('click', () => {
        if (isLocked) return;

        const side = item.dataset.side;
        const key  = item.dataset.item;

        if (side === 'left') {
            if (connections[key] !== null) {
                connections[key] = null;
                selectedLeft = key;
            } else {
                selectedLeft = (selectedLeft === key) ? null : key;
            }
            refreshDots();
            redrawLines();
            return;
        }

        // right side
        const existingLeft = Object.keys(connections).find(lk => connections[lk] === key);
        if (existingLeft) {
            connections[existingLeft] = null;
            if (selectedLeft && selectedLeft !== existingLeft) {
                connections[selectedLeft] = key;
                selectedLeft = null;
            } else {
                selectedLeft = null;
            }
        } else {
            if (!selectedLeft) return;
            connections[selectedLeft] = key;
            selectedLeft = null;
        }
        refreshDots();
        redrawLines();
    });
});

window.addEventListener('resize', redrawLines);

// ── Next button handler ──
function handleNext() {
    if (isLocked) {
        document.getElementById('lockedModal').classList.add('show');
        return;
    }
    // Validate all connected first
    if (!items.every(i => connections[i] !== null)) {
        alert('Please connect all 4 items before continuing!');
        return;
    }
    // Show confirmation modal
    document.getElementById('confirmModal').classList.add('show');
}

function confirmSubmit() {
    closeConfirmModal();
    submitAnswer();
}

function submitAnswer() {
    const isCorrect = items.every(i => connections[i] === i);
    document.getElementById('responseInput').value = isCorrect ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

// ── Modals ──
function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
}
function closeLockedModal() {
    document.getElementById('lockedModal').classList.remove('show');
}

// Close on backdrop click
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});
document.getElementById('lockedModal').addEventListener('click', function(e) {
    if (e.target === this) closeLockedModal();
});
</script>

</body>
</html><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\family\matching-game.blade.php ENDPATH**/ ?>