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
            max-width: 820px;
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
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
        }

        .game-title    { text-align: center; font-size: 1.1rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { text-align: center; font-size: 0.82rem; color: #aaa; margin-bottom: 1.5rem; }

        .game-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .col-header {
            text-align: center;
            font-size: 0.85rem;
            font-weight: 800;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            margin-bottom: 1rem;
        }

        .items-column { display: flex; flex-direction: column; gap: 1rem; }

        /* ── Object Card ── */
        .obj-card {
            border-radius: 20px;
            border: 4px solid #e0e0e0;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.2rem 0.8rem;
            cursor: move;
            transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
            user-select: none;
            min-height: 110px;
            position: relative;
        }

        .obj-card:hover:not(.locked)  { transform: scale(1.05); border-color: #f5a623; box-shadow: 0 6px 18px rgba(245,166,35,0.3); }
        .obj-card.dragging            { opacity: 0.5; transform: rotate(5deg); }
        .obj-card.drag-over           { border-color: #7C3AED; background: #f0ebff; box-shadow: 0 0 0 4px #7C3AED44; }
        /* Left card turns fully gray once paired — overrides all color classes */
        .obj-card.paired              { border-color: #CBD5E1 !important; background: #E2E8F0 !important; cursor: default; opacity: 0.6; }
        /* Right slot gets neutral gray border when something is dropped on it */
        .right-slot.paired            { border-color: #94A3B8; background: #f8f9fa; cursor: default; }
        .obj-card.locked              { cursor: default; }

        .obj-emoji { font-size: 3rem; line-height: 1; pointer-events: none; }
        .obj-name  { font-size: 0.85rem; font-weight: 800; color: #888; margin-top: 0.4rem; pointer-events: none; }

        /* Left column colors */
        .left-1 { background: #fff0f9; border-color: #ffb3d9; }
        .left-2 { background: #fff8ee; border-color: #ffd194; }
        .left-3 { background: #f0f8ff; border-color: #99d6ff; }
        .left-4 { background: #f5fff0; border-color: #a8e6a3; }

        /* Right column colors */
        .right-1 { background: #f5fff0; border-color: #a8e6a3; }
        .right-2 { background: #f0f8ff; border-color: #99d6ff; }
        .right-3 { background: #fff0f9; border-color: #ffb3d9; }
        .right-4 { background: #fff8ee; border-color: #ffd194; }

        /* Badge showing which left card is paired to a right slot */
        .pair-badge {
            position: absolute;
            top: 6px; right: 8px;
            font-size: 1.2rem;
            line-height: 1;
            pointer-events: none;
        }

        /* Success message */
        .success-message {
            display: none;
            text-align: center;
            font-size: 1.2rem;
            color: #4CAF50;
            font-weight: 900;
            margin-bottom: 1rem;
            padding: 1.2rem;
            background: #f0fff4;
            border-radius: 15px;
            border: 3px solid #4CAF50;
            animation: bounce 0.5s;
        }
        .success-message.show { display: block; }

        @keyframes bounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }

        .answer-hint { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        .nav-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700;
            text-decoration: none; border: 2px solid #ccc; cursor: pointer;
            background: #fff; color: #333; transition: all 0.2s;
        }
        .btn-nav:hover  { background: #f5f5f5; transform: translateY(-2px); }
        .btn-nav.hidden { visibility: hidden; }
        .btn-prev       { background: transparent; border: none; color: #999; }
        .btn-prev:hover { background: transparent; color: #666; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .game-grid { gap: 1rem; }
            .obj-emoji { font-size: 2.2rem; }
            .obj-card  { min-height: 90px; padding: 1rem 0.5rem; }
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
        <div class="game-title">🎮 Match the Objects!</div>
        <div class="game-subtitle">Drag an item from the left and drop it on the matching item on the right.</div>

        <div class="game-grid">
            {{-- Left Column --}}
            <div>
                <div class="col-header">👈 Drag from here</div>
                <div class="items-column">
                    <div class="obj-card left-1" draggable="true" data-item="spoon" id="left-spoon">
                        <span class="obj-emoji">🥄</span>
                        <span class="obj-name">Spoon</span>
                    </div>
                    <div class="obj-card left-2" draggable="true" data-item="block" id="left-block">
                        <span class="obj-emoji">🧱</span>
                        <span class="obj-name">Block</span>
                    </div>
                    <div class="obj-card left-3" draggable="true" data-item="ball" id="left-ball">
                        <span class="obj-emoji">🔵</span>
                        <span class="obj-name">Ball</span>
                    </div>
                    <div class="obj-card left-4" draggable="true" data-item="star" id="left-star">
                        <span class="obj-emoji">⭐</span>
                        <span class="obj-name">Star</span>
                    </div>
                </div>
            </div>

            {{-- Right Column (shuffled) --}}
            <div>
                <div class="col-header">Drop on match 👉</div>
                <div class="items-column">
                    <div class="obj-card right-1" data-item="ball" id="right-ball">
                        <span class="obj-emoji">🔵</span>
                        <span class="obj-name">Ball</span>
                    </div>
                    <div class="obj-card right-2" data-item="spoon" id="right-spoon">
                        <span class="obj-emoji">🥄</span>
                        <span class="obj-name">Spoon</span>
                    </div>
                    <div class="obj-card right-3" data-item="star" id="right-star">
                        <span class="obj-emoji">⭐</span>
                        <span class="obj-name">Star</span>
                    </div>
                    <div class="obj-card right-4" data-item="block" id="right-block">
                        <span class="obj-emoji">🧱</span>
                        <span class="obj-name">Block</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}" id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint">Drag all items to a match on the right, then click Next</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}" class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev hidden">← Previous</span>
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
// pairs[rightItemId] = leftItemId currently dropped on it (or null)
const pairs = { 'right-ball': null, 'right-spoon': null, 'right-star': null, 'right-block': null };

let dragged = null;
let selected = null; // for tap/click support

// ── Drag from left ──────────────────────────────────────────────────────────
document.querySelectorAll('.game-grid > div:first-child .obj-card').forEach(card => {
    card.addEventListener('dragstart', function () {
        dragged = this;
        this.classList.add('dragging');
    });
    card.addEventListener('dragend', function () {
        this.classList.remove('dragging');
    });
    // Tap support: click left card to select it
    card.addEventListener('click', function () {
        if (selected) selected.classList.remove('selected');
        selected = (selected === this) ? null : this;
        if (selected) selected.style.outline = '3px solid #7C3AED';
        else this.style.outline = '';
    });
});

// ── Drop on right ───────────────────────────────────────────────────────────
document.querySelectorAll('.game-grid > div:last-child .obj-card').forEach(slot => {
    slot.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    slot.addEventListener('dragleave', function () {
        this.classList.remove('drag-over');
    });
    slot.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        if (dragged) { pair(dragged, this); dragged = null; }
    });
    // Tap support: click right card after selecting a left card
    slot.addEventListener('click', function () {
        if (selected) {
            selected.style.outline = '';
            pair(selected, this);
            selected = null;
        }
    });
});

// ── Core pair logic ─────────────────────────────────────────────────────────
function pair(leftCard, rightSlot) {
    const leftId  = leftCard.id;
    const rightId = rightSlot.id;

    // If this left card was previously paired to another right slot, clear that slot
    for (const rid in pairs) {
        if (pairs[rid] === leftId) {
            document.getElementById(rid).classList.remove('paired');
            removeBadge(rid);
            pairs[rid] = null;
        }
    }

    // If this right slot had a different left card, un-gray that left card
    if (pairs[rightId] && pairs[rightId] !== leftId) {
        const prevLeft = document.getElementById(pairs[rightId]);
        if (prevLeft) prevLeft.classList.remove('paired');
    }

    // Record the new pairing
    pairs[rightId] = leftId;

    // Gray out the left card to show it's been used
    leftCard.classList.add('paired');

    // Neutral border on right slot only
    rightSlot.classList.add('paired');

    // Show left card's emoji as a small badge on the right slot
    removeBadge(rightId);
    const badge = document.createElement('span');
    badge.className = 'pair-badge';
    badge.textContent = leftCard.querySelector('.obj-emoji').textContent;
    rightSlot.appendChild(badge);
}

function removeBadge(rightId) {
    const existing = document.getElementById(rightId).querySelector('.pair-badge');
    if (existing) existing.remove();
}

// ── Submit ───────────────────────────────────────────────────────────────────
function submitAnswer() {
    // Check every right slot: paired left card's data-item must equal right slot's data-item
    let allCorrect = true;
    for (const rightId in pairs) {
        const leftId = pairs[rightId];
        if (!leftId) { allCorrect = false; break; }
        const leftItem  = document.getElementById(leftId).dataset.item;
        const rightItem = document.getElementById(rightId).dataset.item;
        if (leftItem !== rightItem) { allCorrect = false; break; }
    }
    document.getElementById('responseInput').value = allCorrect ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}
</script>

</body>
</html>