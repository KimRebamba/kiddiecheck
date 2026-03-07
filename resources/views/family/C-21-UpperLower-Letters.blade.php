<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter Match Game</title>
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
            padding: 2rem 40px 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .game-box.locked { background: #f8f8f8; border-color: #ccc; pointer-events: none; }

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

        .game-title    { font-size: 1.2rem; font-weight: 900; color: #e07b00; margin-bottom: 0.3rem; }
        .game-subtitle { font-size: 0.85rem; color: #aaa; margin-bottom: 1.4rem; }

        .mini-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.4rem; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background: #e2e8f0; border: 2px solid #ccc; transition: all 0.3s; }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }
        .dot.correct { background: #38A169; border-color: #38A169; }
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; }

        .game-area {
            position: relative;
            width: 100%;
            max-width: 560px;
            margin: 0 auto 0.6rem;
            user-select: none;
        }

        .round-label {
            font-size: 0.78rem;
            font-weight: 800;
            color: #7C3AED;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        #linesSvg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: visible;
        }

        .columns {
            display: flex;
            gap: 0;
            align-items: stretch;
            position: relative;
            z-index: 2;
        }

        .col {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .col-label {
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 4px;
            padding: 4px 0;
        }
        .col-upper .col-label { color: #1D4ED8; }
        .col-lower .col-label { color: #C2410C; }

        .col-upper { padding-right: 30px; align-items: flex-end; }
        .col-lower { padding-left:  30px; align-items: flex-start; }

        .tile {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            border: 3px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 900;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s, background 0.15s;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            position: relative;
            flex-shrink: 0;
        }

        .tile.uppercase {
            background: #EFF6FF;
            border-color: #93C5FD;
            color: #1D4ED8;
        }
        .tile.uppercase:hover { transform: scale(1.08); border-color: #3B82F6; box-shadow: 0 6px 18px rgba(59,130,246,0.3); }
        .tile.uppercase.selected {
            background: #DBEAFE; border-color: #2563EB; color: #1D4ED8;
            transform: scale(1.12); box-shadow: 0 8px 22px rgba(37,99,235,0.4);
        }

        .tile.lowercase {
            background: #FFF7ED;
            border-color: #FDBA74;
            color: #C2410C;
        }
        .tile.lowercase:hover { transform: scale(1.08); border-color: #F97316; box-shadow: 0 6px 18px rgba(249,115,22,0.3); }
        .tile.lowercase.selected {
            background: #FFEDD5; border-color: #EA580C; color: #C2410C;
            transform: scale(1.12); box-shadow: 0 8px 22px rgba(234,88,12,0.4);
        }

        .tile.connected {
            transform: scale(1) !important;
            cursor: pointer;
        }

        .tile.correct-match  { background: #D1FAE5 !important; border-color: #34D399 !important; color: #065F46 !important; }
        .tile.wrong-match    { background: #FEE2E2 !important; border-color: #F87171 !important; color: #991B1B !important; }
        .tile.correct-match.connected, .tile.wrong-match.connected { cursor: default; }

        @keyframes bounceIn {
            0%   { transform: scale(1); }
            40%  { transform: scale(1.18); }
            70%  { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
        .tile.bounce { animation: bounceIn 0.4s ease; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%     { transform: translateX(-6px); }
            40%     { transform: translateX(6px); }
            60%     { transform: translateX(-4px); }
            80%     { transform: translateX(4px); }
        }
        .tile.shake { animation: shake 0.35s ease; }

        .tile .dot-badge {
            position: absolute;
            top: -5px; right: -5px;
            width: 14px; height: 14px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .game-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 1.2rem;
            flex-wrap: wrap;
        }

        .btn-check {
            padding: 11px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            background: #7C3AED;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.2s, opacity 0.2s;
        }
        .btn-check:hover { background: #6D28D9; }
        .btn-check:disabled { opacity: 0.4; cursor: not-allowed; }

        .btn-reset {
            padding: 11px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            background: #fff;
            color: #666;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-reset:hover { background: #f5f5f5; }

        .pairs-status {
            font-size: 0.82rem;
            font-weight: 700;
            color: #aaa;
        }

        .round-result {
            display: none;
            margin-top: 1rem;
            padding: 10px 20px;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 900;
            text-align: center;
        }
        .round-result.show { display: block; }
        .round-result.all-correct { background: #C6F6D5; color: #276749; border: 2px solid #38A169; }
        .round-result.has-errors  { background: #FED7D7; color: #9B2C2C; border: 2px solid #E53E3E; }

        .btn-next-round {
            margin-top: 0.9rem;
            padding: 11px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            background: #7C3AED;
            color: #fff;
            border: none;
            cursor: pointer;
            display: none;
            transition: background 0.2s;
        }
        .btn-next-round:hover { background: #6D28D9; }
        .btn-next-round.visible { display: inline-block; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 0.4rem; }
        .answer-hint { font-size: 0.85rem; color: #aaa; margin-bottom: 0.8rem; }

        .nav-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .nav-center { display: flex; gap: 10px; }
        .btn-nav { padding: 12px 24px; border-radius: 10px; font-size: 15px; font-weight: 700; text-decoration: none; border: 2px solid #ccc; cursor: pointer; background: #fff; color: #333; }
        .btn-nav:hover  { background: #f5f5f5; }
        .btn-prev       { background: #f5f5f5; border-color: #999; color: #666; }
        .btn-nav.locked { background: #e9e9e9; border-color: #ccc; color: #999; cursor: not-allowed; }

        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
        .modal.show { display: flex; }
        .modal-box { background: #fff; border-radius: 24px; padding: 36px 40px; max-width: 420px; width: 90%; box-shadow: 0 12px 40px rgba(0,0,0,0.25); border: 3px solid #000; text-align: center; }
        .modal-icon  { font-size: 3rem; margin-bottom: 12px; }
        .modal-title { font-size: 1.25rem; font-weight: 900; margin-bottom: 8px; }
        .modal-body  { font-size: 0.95rem; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .modal-btns  { display: flex; gap: 12px; justify-content: center; }
        .btn-ok     { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #7C3AED; color: #fff; border: none; cursor: pointer; }
        .btn-cancel { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #fff; color: #555; border: 2px solid #ccc; cursor: pointer; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .game-box { padding: 1.5rem 16px; }
            .tile { width: 58px; height: 58px; font-size: 1.7rem; border-radius: 14px; }
            .col-upper { padding-right: 18px; }
            .col-lower { padding-left:  18px; }
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

        <div class="game-title">🔤 Mix &amp; Match Letters!</div>
        <div class="game-subtitle">Tap a letter, then tap its match. Tap again to disconnect and try again!</div>

        <div class="mini-dots" id="miniDots"></div>

        <div class="game-area" id="gameArea">
            <div class="round-label" id="roundLabel"></div>

            <svg id="linesSvg"></svg>

            <div class="columns" id="columnsWrap">
                <div class="col col-upper" id="colUpper">
                    <div class="col-label">UPPERCASE</div>
                </div>
                <div class="col col-lower" id="colLower">
                    <div class="col-label">lowercase</div>
                </div>
            </div>

            <div class="game-controls">
                <span class="pairs-status" id="pairsStatus"></span>
                <button class="btn-reset" id="btnReset" onclick="resetRound()">↺ Reset</button>
                <button class="btn-check" id="btnCheck" onclick="checkRound()" disabled>Check ✓</button>
            </div>

            <div class="round-result" id="roundResult"></div>
            <button class="btn-next-round" id="btnNextRound" onclick="nextRound()"></button>
        </div>

        <div class="tap-hint" id="tapHint">Tap any letter to select it, then tap its partner to connect</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Complete all 3 rounds to unlock Next →</div>

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

<!-- Modals -->
<div class="modal" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">Next means submitting your answer and not returning to it.<br><br>Are you sure?</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-ok" onclick="submitAnswer()">Yes, Submit</button>
        </div>
    </div>
</div>
<div class="modal" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">Your answer has been saved and is now locked.</div>
        <div class="modal-btns"><button class="btn-ok" onclick="closeModal('lockedModal')">Got it!</button></div>
    </div>
</div>
<div class="modal" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">This will skip <strong>all 3 rounds</strong> and move to the next question.<br><br>You can come back later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok" onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
const rounds = [
    { pairs: [['A','a'], ['B','b'], ['C','c'], ['D','d']] },
    { pairs: [['E','e'], ['F','f'], ['G','g'], ['H','h']] },
    { pairs: [['M','m'], ['N','n'], ['P','p'], ['R','r']] },
];

const PASS_SCORE = 2;
const SLOT_COLORS = ['#6366F1', '#F59E0B', '#10B981', '#EF4444'];

const existing = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existing !== '';

let current  = 0;
let answers  = [null, null, null];
let skipUrl  = null;
let checked  = false;

let connections = new Map();
let selected    = null;

function render() {
    selected    = null;
    connections = new Map();
    checked     = false;

    document.getElementById('roundLabel').textContent =
        `Round ${current + 1} of ${rounds.length}`;
    document.getElementById('roundResult').className   = 'round-result';
    document.getElementById('roundResult').textContent = '';
    document.getElementById('btnNextRound').classList.remove('visible');
    document.getElementById('linesSvg').innerHTML = '';
    document.getElementById('btnCheck').disabled  = true;
    document.getElementById('btnReset').style.display = '';

    buildColumns(rounds[current]);
    buildDots();
    updateStatus();
    updateHints();
}

function buildColumns(round) {
    const upper = document.getElementById('colUpper');
    const lower = document.getElementById('colLower');

    while (upper.children.length > 1) upper.removeChild(upper.lastChild);
    while (lower.children.length > 1) lower.removeChild(lower.lastChild);

    const lowers = round.pairs.map(([,lo]) => lo);
    for (let i = lowers.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [lowers[i], lowers[j]] = [lowers[j], lowers[i]];
    }

    round.pairs.forEach(([up]) => {
        upper.appendChild(makeTile(up, true));
    });
    lowers.forEach(lo => {
        lower.appendChild(makeTile(lo, false));
    });

    requestAnimationFrame(resizeSvg);
}

function makeTile(letter, isUpper) {
    const tile = document.createElement('div');
    tile.className = `tile ${isUpper ? 'uppercase' : 'lowercase'}`;
    tile.textContent = letter;
    tile.dataset.letter  = letter;
    tile.dataset.isUpper = isUpper ? '1' : '0';
    if (!isLocked) tile.addEventListener('click', () => onTileClick(tile));
    return tile;
}

function resizeSvg() {
    const area = document.getElementById('gameArea');
    const svg  = document.getElementById('linesSvg');
    svg.setAttribute('viewBox', `0 0 ${area.offsetWidth} ${area.offsetHeight}`);
}

function onTileClick(tile) {
    if (checked) return;

    const letter  = tile.dataset.letter;
    const isUpper = tile.dataset.isUpper === '1';

    if (tile.classList.contains('connected')) {
        disconnectTile(tile);
        if (selected) {
            selected.classList.remove('selected');
            selected = null;
        }
        return;
    }

    if (selected === tile) {
        tile.classList.remove('selected');
        selected = null;
        return;
    }

    if (!selected) {
        tile.classList.add('selected');
        selected = tile;
        return;
    }

    const selIsUpper = selected.dataset.isUpper === '1';

    if (isUpper === selIsUpper) {
        selected.classList.remove('selected');
        tile.classList.add('selected');
        selected = tile;
        return;
    }

    const upTile = isUpper ? tile : selected;
    const loTile = isUpper ? selected : tile;

    if (upTile.classList.contains('connected')) disconnectTile(upTile);
    if (loTile.classList.contains('connected')) disconnectTile(loTile);

    connect(upTile, loTile);

    selected.classList.remove('selected');
    selected = null;
}

function connect(upTile, loTile) {
    const upLetter = upTile.dataset.letter;
    const loLetter = loTile.dataset.letter;

    connections.set(upLetter, loLetter);

    const upTiles = Array.from(document.getElementById('colUpper').querySelectorAll('.tile'));
    const colorIdx = upTiles.indexOf(upTile) % SLOT_COLORS.length;
    const color    = SLOT_COLORS[colorIdx];

    upTile.classList.add('connected');
    loTile.classList.add('connected');
    upTile.style.borderColor = color;
    loTile.style.borderColor = color;
    upTile.style.background  = hexAlpha(color, 0.12);
    loTile.style.background  = hexAlpha(color, 0.12);
    upTile.style.color       = color;
    loTile.style.color       = color;
    upTile.dataset.colorIdx  = colorIdx;
    loTile.dataset.colorIdx  = colorIdx;

    upTile.dataset.pairedWith = loLetter;
    loTile.dataset.pairedWith = upLetter;

    drawLine(upTile, loTile, color, `line-${upLetter}`);

    updateStatus();
}

function disconnectTile(tile) {
    const isUpper  = tile.dataset.isUpper === '1';
    const upLetter = isUpper ? tile.dataset.letter : tile.dataset.pairedWith;
    const loLetter = isUpper ? tile.dataset.pairedWith : tile.dataset.letter;

    connections.delete(upLetter);

    [upLetter, loLetter].forEach(letter => {
        const el = getTileByLetter(letter);
        if (!el) return;
        el.classList.remove('connected', 'correct-match', 'wrong-match', 'bounce', 'shake');
        el.style.borderColor = '';
        el.style.background  = '';
        el.style.color       = '';
        delete el.dataset.pairedWith;
        delete el.dataset.colorIdx;
    });

    const line = document.getElementById(`line-${upLetter}`);
    if (line) line.remove();

    updateStatus();
}

function getTileByLetter(letter) {
    return document.querySelector(`.tile[data-letter="${letter}"]`);
}

function drawLine(elA, elB, color, id, instant) {
    const svg  = document.getElementById('linesSvg');
    const area = document.getElementById('gameArea');
    const base = area.getBoundingClientRect();
    const rA   = elA.getBoundingClientRect();
    const rB   = elB.getBoundingClientRect();

    const ax = rA.left + rA.width  / 2 - base.left;
    const ay = rA.top  + rA.height / 2 - base.top;
    const bx = rB.left + rB.width  / 2 - base.left;
    const by = rB.top  + rB.height / 2 - base.top;

    const old = document.getElementById(id);
    if (old) old.remove();

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    const mx   = (ax + bx) / 2;
    const my   = (ay + by) / 2;
    path.setAttribute('d', `M${ax},${ay} C${ax + 40},${my} ${bx - 40},${my} ${bx},${by}`);
    path.setAttribute('stroke', color);
    path.setAttribute('stroke-width', '4');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('fill', 'none');
    path.setAttribute('opacity', '0.85');
    path.setAttribute('id', id);
    svg.appendChild(path);

    if (!instant) {
        const len = path.getTotalLength();
        path.style.strokeDasharray  = len;
        path.style.strokeDashoffset = len;
        path.style.transition = 'stroke-dashoffset 0.35s ease';
        requestAnimationFrame(() =>
            requestAnimationFrame(() => { path.style.strokeDashoffset = '0'; })
        );
    }
}

function updateStatus() {
    const total   = rounds[current].pairs.length;
    const done    = connections.size;
    document.getElementById('pairsStatus').textContent =
        `${done} of ${total} paired`;
    document.getElementById('btnCheck').disabled = (done < total);
}

function checkRound() {
    if (checked) return;
    checked = true;

    const round = rounds[current];
    let correct = 0;

    if (selected) { selected.classList.remove('selected'); selected = null; }

    round.pairs.forEach(([up, lo]) => {
        const paired = connections.get(up);
        const upTile = getTileByLetter(up);
        const loTile = getTileByLetter(lo);
        const pairedLoTile = paired ? getTileByLetter(paired) : null;

        if (paired === lo) {
            correct++;
            [upTile, loTile].forEach(t => {
                t.classList.add('correct-match', 'bounce');
            });
            const line = document.getElementById(`line-${up}`);
            if (line) { line.setAttribute('stroke', '#34D399'); line.setAttribute('opacity', '1'); }
        } else {
            if (upTile && upTile.classList.contains('connected')) upTile.classList.add('wrong-match', 'shake');
            if (pairedLoTile) pairedLoTile.classList.add('wrong-match', 'shake');
            if (loTile && !loTile.classList.contains('connected')) loTile.classList.add('wrong-match');
            const line = document.getElementById(`line-${up}`);
            if (line) { line.setAttribute('stroke', '#F87171'); line.setAttribute('stroke-dasharray', '6 4'); }
        }
    });

    const isCorrect = correct === round.pairs.length;
    answers[current] = isCorrect;

    const res = document.getElementById('roundResult');
    res.className = 'round-result show ' + (isCorrect ? 'all-correct' : 'has-errors');
    res.textContent = isCorrect
        ? `🌟 Perfect! All ${round.pairs.length} pairs matched correctly!`
        : `💪 ${correct} of ${round.pairs.length} correct — try the next round!`;

    document.getElementById('btnCheck').disabled = true;
    document.getElementById('btnReset').style.display = 'none';

    const btn = document.getElementById('btnNextRound');
    const remaining = answers.filter(a => a === null).length;
    btn.textContent = remaining > 0 ? 'Next Round →' : 'Done! 🎉';
    btn.classList.add('visible');

    buildDots();
    updateHints();
}

function resetRound() {
    render();
}

function nextRound() {
    const next = answers.indexOf(null);
    if (next !== -1) current = next;
    render();
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

function updateHints() {
    const allDone   = answers.every(a => a !== null);
    const remaining = answers.filter(a => a === null).length;
    document.getElementById('btnNext').className =
        (allDone || isLocked) ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('tapHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : 'Tap any letter to select, tap its partner to pair — tap a connected tile to disconnect';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} round${remaining > 1 ? 's' : ''} left — complete all to unlock Next →`;
}

function hexAlpha(hex, alpha) {
    const r = parseInt(hex.slice(1,3),16);
    const g = parseInt(hex.slice(3,5),16);
    const b = parseInt(hex.slice(5,7),16);
    return `rgba(${r},${g},${b},${alpha})`;
}

const nextUrl = "{{ $nextDomain && $nextIndex ? route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) : route('family.tests.result', $testId) }}";

function clickNext() {
    if (isLocked) { window.location.href = nextUrl; return; }
    if (answers.some(a => a === null)) return;
    openModal('confirmModal');
}
function submitAnswer() {
    closeModal('confirmModal');
    const correct = answers.filter(a => a === true).length;
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}
function clickSkip(e) { e.preventDefault(); skipUrl = e.currentTarget.href; openModal('skipModal'); }
function doSkip()     { closeModal('skipModal'); window.location.href = skipUrl; }
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

if (isLocked) {
    document.getElementById('lockedBanner').style.display = 'block';
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    answers = rounds.map(() => true);
}

render();
window.addEventListener('resize', () => {
    resizeSvg();
    if (!checked) {
        document.getElementById('linesSvg').innerHTML = '';
        connections.forEach((loLetter, upLetter) => {
            const upTile = getTileByLetter(upLetter);
            const loTile = getTileByLetter(loLetter);
            if (!upTile || !loTile) return;
            const colorIdx = parseInt(upTile.dataset.colorIdx || 0);
            drawLine(upTile, loTile, SLOT_COLORS[colorIdx], `line-${upLetter}`, true);
        });
    }
});
</script>
</body>
</html>