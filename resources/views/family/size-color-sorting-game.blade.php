<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sort by Size & Color!</title>
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
            max-width: 980px;
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
        }

        .game-title    { text-align: center; font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.5rem; }
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        .legend { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 1.2rem; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; color: #666; }
        .legend-dot  { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }

        .tray-label {
            text-align: center; font-size: 0.9rem; font-weight: 800;
            color: #888; text-transform: uppercase; letter-spacing: 0.05rem; margin-bottom: 0.8rem;
        }

        .unsorted-tray {
            display: flex; flex-wrap: wrap; justify-content: center; gap: 14px;
            background: #f9f9f9; border: 2px dashed #ddd; border-radius: 16px;
            padding: 1.2rem; min-height: 120px; margin-bottom: 1.5rem;
        }

        .groups-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }

        .sort-group {
            border-radius: 16px; border: 3px dashed #e0e0e0; background: #fff;
            padding: 0.8rem 0.5rem; min-height: 180px;
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            transition: border-color 0.2s, background 0.2s; position: relative;
        }

        .sort-group.drag-over { border-color: #7C3AED; background: #f0ebff; }
        .sort-group.has-cards { border-color: #94A3B8; border-style: solid; }

        .group-label {
            font-size: 0.72rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: 0.04em; text-align: center; margin-bottom: 4px;
            padding: 3px 10px; border-radius: 999px; color: #fff;
        }

        .shape-card {
            border-radius: 12px; border: 3px solid #e0e0e0; background: #fff;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 5px; cursor: grab; transition: all 0.25s; user-select: none;
            position: relative; box-shadow: 0 3px 10px rgba(0,0,0,0.1); flex-shrink: 0; padding: 6px;
        }

        .shape-card.big   { width: 90px; height: 90px; }
        .shape-card.small { width: 60px; height: 60px; }

        .shape-card:active             { cursor: grabbing; }
        .shape-card:hover:not(.placed) { transform: scale(1.1) rotate(-3deg); box-shadow: 0 8px 20px rgba(0,0,0,0.18); border-color: #f5a623; }
        .shape-card.dragging           { opacity: 0.4; transform: scale(0.9); }
        /* Neutral gray once placed — no correct/wrong hint */
        .shape-card.placed             { cursor: grab; border-color: #94A3B8 !important; background: #E2E8F0 !important; opacity: 0.7; }
        .shape-card.placed:hover       { transform: scale(1.05); opacity: 1; border-color: #f5a623 !important; }

        .size-tag { font-size: 0.55rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.04em; color: #999; }

        @keyframes popIn { 0%{transform:scale(0.8)} 60%{transform:scale(1.15)} 100%{transform:scale(1)} }
        .shape-card.placed { animation: popIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }

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

        @media (max-width: 768px) {
            .card { padding: 24px 16px; }
            .groups-row { grid-template-columns: repeat(2, 1fr); }
            .shape-card.big   { width: 70px; height: 70px; }
            .shape-card.small { width: 50px; height: 50px; }
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
        <div class="game-title">🔵🔴 Sort by Size & Color!</div>
        <div class="game-subtitle">Put together the ones that are the same — match by BOTH color AND size!</div>

        <div class="legend">
            <span class="legend-item"><span class="legend-dot" style="background:#EF4444;"></span> Big Red</span>
            <span class="legend-item"><span class="legend-dot" style="background:#EF4444; opacity:0.45;"></span> Small Red</span>
            <span class="legend-item"><span class="legend-dot" style="background:#3B82F6;"></span> Big Blue</span>
            <span class="legend-item"><span class="legend-dot" style="background:#3B82F6; opacity:0.45;"></span> Small Blue</span>
        </div>

        <div class="tray-label">Drag from here</div>
        <div class="unsorted-tray" id="unsortedTray">

            <div class="shape-card big"   draggable="true" data-key="big-blue"   id="sh1">
                <svg width="52" height="52" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
                <span class="size-tag">Big</span>
            </div>
            <div class="shape-card small" draggable="true" data-key="small-red"  id="sh2">
                <svg width="34" height="34" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
                <span class="size-tag">Small</span>
            </div>
            <div class="shape-card big"   draggable="true" data-key="big-red"    id="sh3">
                <svg width="52" height="52" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
                <span class="size-tag">Big</span>
            </div>
            <div class="shape-card small" draggable="true" data-key="small-blue" id="sh4">
                <svg width="34" height="34" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
                <span class="size-tag">Small</span>
            </div>
            <div class="shape-card big"   draggable="true" data-key="big-red"    id="sh5">
                <svg width="52" height="52" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
                <span class="size-tag">Big</span>
            </div>
            <div class="shape-card small" draggable="true" data-key="small-blue" id="sh6">
                <svg width="34" height="34" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
                <span class="size-tag">Small</span>
            </div>
            <div class="shape-card big"   draggable="true" data-key="big-blue"   id="sh7">
                <svg width="52" height="52" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
                <span class="size-tag">Big</span>
            </div>
            <div class="shape-card small" draggable="true" data-key="small-red"  id="sh8">
                <svg width="34" height="34" viewBox="0 0 52 52"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
                <span class="size-tag">Small</span>
            </div>

        </div>

        <div class="tray-label">Drop into the matching group</div>
        <div class="groups-row">

            <div class="sort-group" data-group="big-red" id="group-big-red">
                <div class="group-label" style="background:#EF4444;">🔴 Big Red</div>
                <svg width="44" height="44" viewBox="0 0 52 52" style="opacity:0.18; margin-top:8px;"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
            </div>

            <div class="sort-group" data-group="small-red" id="group-small-red">
                <div class="group-label" style="background:#F87171;">🔴 Small Red</div>
                <svg width="28" height="28" viewBox="0 0 52 52" style="opacity:0.18; margin-top:8px;"><circle cx="26" cy="26" r="23" fill="#EF4444"/></svg>
            </div>

            <div class="sort-group" data-group="big-blue" id="group-big-blue">
                <div class="group-label" style="background:#3B82F6;">🔵 Big Blue</div>
                <svg width="44" height="44" viewBox="0 0 52 52" style="opacity:0.18; margin-top:8px;"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
            </div>

            <div class="sort-group" data-group="small-blue" id="group-small-blue">
                <div class="group-label" style="background:#60A5FA;">🔵 Small Blue</div>
                <svg width="28" height="28" viewBox="0 0 52 52" style="opacity:0.18; margin-top:8px;"><circle cx="26" cy="26" r="23" fill="#3B82F6"/></svg>
            </div>

        </div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint">Sort all shapes, then click Next</div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="button" onclick="submitAnswer()" class="btn-nav">Next →</button>

                @if($nextDomain && $nextIndex)
                    <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}"
                       class="btn-nav">Skip (Answer Later)</a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">Review →</a>
                @endif
            </div>
        </div>
    </form>

</div>

<script>
let draggedItem  = null;
let selectedItem = null;
let placedCount  = 0;
const totalCards = 8;

// Track which group each card is currently in (null = still in tray)
const cardGroup = {
    sh1: null, sh2: null, sh3: null, sh4: null,
    sh5: null, sh6: null, sh7: null, sh8: null
};

// ── Drag ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.shape-card').forEach(card => {
    card.addEventListener('dragstart', function () {
        draggedItem = this;
        this.classList.add('dragging');
    });
    card.addEventListener('dragend', function () {
        this.classList.remove('dragging');
    });
    card.addEventListener('click', function () {
        if (selectedItem) selectedItem.style.outline = '';
        if (selectedItem === this) { selectedItem = null; return; }
        selectedItem = this;
        this.style.outline = '3px solid #7C3AED';
        this.style.outlineOffset = '3px';
    });
});

// ── Drop ───────────────────────────────────────────────────────────────────
document.querySelectorAll('.sort-group').forEach(group => {
    group.addEventListener('dragover',  e => { e.preventDefault(); group.classList.add('drag-over'); });
    group.addEventListener('dragleave', () => group.classList.remove('drag-over'));
    group.addEventListener('drop', e => {
        e.preventDefault();
        group.classList.remove('drag-over');
        if (draggedItem) { placeCard(draggedItem, group); draggedItem = null; }
    });
    group.addEventListener('click', () => {
        if (!selectedItem) return;
        selectedItem.style.outline = '';
        placeCard(selectedItem, group);
        selectedItem = null;
    });
});

// ── Place card (any group, no error) ───────────────────────────────────────
function placeCard(card, group) {
    const cardId      = card.id;
    const groupId     = group.dataset.group;
    const prevGroupId = cardGroup[cardId];

    // Remove from previous location
    if (prevGroupId) {
        const prevGroup = document.getElementById('group-' + prevGroupId);
        prevGroup.removeChild(card);
        if (!prevGroup.querySelector('.shape-card')) prevGroup.classList.remove('has-cards');
        placedCount--;
    } else {
        const tray = document.getElementById('unsortedTray');
        if (tray.contains(card)) tray.removeChild(card);
    }

    // Add to new group
    card.classList.add('placed');
    card.style.outline = '';
    group.appendChild(card);
    group.classList.add('has-cards');
    cardGroup[cardId] = groupId;
    placedCount++;
}

// ── Check correctness ──────────────────────────────────────────────────────
function isSortedCorrectly() {
    for (const cardId in cardGroup) {
        const groupId = cardGroup[cardId];
        if (!groupId) return false;
        if (document.getElementById(cardId).dataset.key !== groupId) return false;
    }
    return true;
}

// ── Submit ─────────────────────────────────────────────────────────────────
function submitAnswer() {
    document.getElementById('responseInput').value = isSortedCorrectly() ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}
</script>

</body>
</html>