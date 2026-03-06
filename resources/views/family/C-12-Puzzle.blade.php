<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Puzzle!</title>
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
            max-width: 1100px;
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
        .game-box.locked { background: #f8f8f8; border-color: #ccc; }

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
        .game-subtitle { font-size: 0.85rem; color: #aaa; margin-bottom: 1.5rem; }

        .puzzle-layout {
            display: flex;
            gap: 24px;
            align-items: stretch;
            justify-content: center;
        }

        .col-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            text-align: center;
            margin-bottom: 8px;
        }

        .col-divider {
            width: 2px;
            background: #e2e8f0;
            border-radius: 2px;
            align-self: stretch;
            flex-shrink: 0;
        }

        .puzzle-board {
            display: grid;
            grid-template-columns: repeat(3, 90px);
            grid-template-rows: repeat(3, 90px);
            gap: 6px;
            background: #f0f4f8;
            border-radius: 16px;
            padding: 8px;
            border: 3px solid #cbd5e0;
            flex-shrink: 0;
        }

        .board-slot {
            width: 90px;
            height: 90px;
            background: #fff;
            border-radius: 10px;
            border: 2px dashed #c0cfe0;
            position: relative;
            transition: background 0.2s, border-color 0.2s;
            overflow: hidden;
        }

        .board-slot .slot-hint {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: rgba(0,0,0,0.07);
            font-weight: 900;
            pointer-events: none;
        }

        .board-slot.drag-over {
            background: #ede9fe;
            border-color: #7C3AED;
            border-style: solid;
        }

        .board-slot.correct { border-color: #38A169; border-style: solid; background: #f0fff4; }
        .board-slot.wrong   { border-color: #E53E3E; border-style: solid; background: #fff5f5; }

        .piece-tray {
            display: grid;
            grid-template-columns: repeat(3, 90px);
            grid-template-rows: repeat(3, 90px);
            gap: 6px;
            background: #f0f4f8;
            border-radius: 16px;
            padding: 8px;
            border: 3px solid #cbd5e0;
            align-content: start;
            flex-shrink: 0;
        }

        .puzzle-piece {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            border: 2px solid #7C3AED;
            cursor: grab;
            position: relative;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
            transition: transform 0.15s, box-shadow 0.15s;
            background: #fff;
            touch-action: none;
        }
        .puzzle-piece:hover { transform: scale(1.07); box-shadow: 0 6px 18px rgba(124,58,237,0.25); }
        .puzzle-piece.dragging { opacity: 0.35; cursor: grabbing; }
        .puzzle-piece canvas { width: 100%; height: 100%; display: block; pointer-events: none; }

        .placed-piece {
            width: 100%; height: 100%;
            border-radius: 8px;
            overflow: hidden;
            cursor: grab;
            position: absolute;
            inset: 0;
        }
        .placed-piece canvas { width: 100%; height: 100%; display: block; pointer-events: none; }
        .placed-piece:hover { opacity: 0.8; }

        .preview-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            justify-content: center;
        }
        .preview-canvas {
            width: 160px;
            height: 160px;
            border-radius: 14px;
            border: 3px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .preview-canvas canvas { width: 100%; height: 100%; display: block; }
        .btn-shuffle {
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
            background: #f3f0ff;
            border: 2px solid #c4b5fd;
            color: #5b21b6;
            cursor: pointer;
            width: 100%;
            letter-spacing: 0.02em;
        }
        .btn-shuffle:hover { background: #e9d5ff; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 1rem; }
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
            .card { padding: 20px 14px; }
            .game-box { padding: 1.5rem 10px; }
            .puzzle-layout { gap: 16px; }
            .preview-canvas { width: 80px; height: 80px; }
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

        <div class="game-title">🧩 Assemble the Puzzle!</div>
        <div class="game-subtitle">Drag pieces from the tray below into the grid — use the picture as a guide</div>

        <div class="puzzle-layout">

            <div>
                <div class="col-label">🎯 Drop here</div>
                <div class="puzzle-board" id="puzzleBoard"></div>
            </div>

            <div class="col-divider"></div>

            <div>
                <div class="col-label">🧩 Pieces</div>
                <div class="piece-tray" id="pieceTray"></div>
            </div>

            <div class="col-divider"></div>

            <div class="preview-box">
                <div class="col-label">🖼 Full picture</div>
                <div class="preview-canvas">
                    <canvas id="previewCanvas"></canvas>
                </div>
                <button class="btn-shuffle" onclick="shufflePieces()">🔀 Shuffle pieces</button>
            </div>

        </div>

        <div class="tap-hint" id="tapHint">Drag all 9 pieces into the grid to unlock Next →</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Place all 9 pieces to unlock Next →</div>

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


<!-- Confirm submit popup -->
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

<!-- Already answered popup -->
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

<!-- Skip warning popup -->
<div class="modal" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">This will skip the puzzle and move to the next question.<br><br>You can come back to it later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>


<script>

const PASS_SCORE = 6;

const existing = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existing !== '';

const GRID      = 3;
const PIECE_PX  = 90;
const FULL_SIZE = GRID * PIECE_PX;

let boardState = new Array(GRID * GRID).fill(null);
let trayState  = new Array(GRID * GRID).fill(true);
let dragPiece  = null;
let skipUrl    = null;


function drawScene(ctx, w, h) {
    const sky = ctx.createLinearGradient(0, 0, 0, h * 0.65);
    sky.addColorStop(0, '#87CEEB');
    sky.addColorStop(1, '#c9e9fb');
    ctx.fillStyle = sky;
    ctx.fillRect(0, 0, w, h);

    ctx.fillStyle = '#FFD700';
    ctx.beginPath();
    ctx.arc(w * 0.82, h * 0.14, w * 0.09, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = '#FFD700';
    ctx.lineWidth = 3;
    for (let i = 0; i < 8; i++) {
        const angle = (i / 8) * Math.PI * 2;
        const rx = w * 0.82 + Math.cos(angle) * w * 0.13;
        const ry = h * 0.14 + Math.sin(angle) * w * 0.13;
        const rx2 = w * 0.82 + Math.cos(angle) * w * 0.17;
        const ry2 = h * 0.14 + Math.sin(angle) * w * 0.17;
        ctx.beginPath(); ctx.moveTo(rx, ry); ctx.lineTo(rx2, ry2); ctx.stroke();
    }

    function cloud(cx, cy, r) {
        ctx.fillStyle = '#fff';
        ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(cx + r * 0.9, cy + r * 0.1, r * 0.75, 0, Math.PI * 2); ctx.fill();
        ctx.beginPath(); ctx.arc(cx - r * 0.9, cy + r * 0.1, r * 0.7, 0, Math.PI * 2); ctx.fill();
    }
    cloud(w * 0.2, h * 0.12, w * 0.07);
    cloud(w * 0.55, h * 0.08, w * 0.055);

    const ground = ctx.createLinearGradient(0, h * 0.6, 0, h);
    ground.addColorStop(0, '#5cbf5c');
    ground.addColorStop(1, '#3d9e3d');
    ctx.fillStyle = ground;
    ctx.fillRect(0, h * 0.62, w, h * 0.38);

    ctx.fillStyle = '#5cbf5c';
    ctx.beginPath();
    ctx.ellipse(w / 2, h * 0.62, w * 0.7, h * 0.07, 0, 0, Math.PI);
    ctx.fill();

    ctx.fillStyle = '#c8a96e';
    ctx.beginPath();
    ctx.moveTo(w * 0.42, h);
    ctx.lineTo(w * 0.58, h);
    ctx.lineTo(w * 0.65, h * 0.64);
    ctx.lineTo(w * 0.35, h * 0.64);
    ctx.fill();

    ctx.fillStyle = '#F4A261';
    ctx.fillRect(w * 0.18, h * 0.42, w * 0.3, h * 0.25);

    ctx.fillStyle = '#E63946';
    ctx.beginPath();
    ctx.moveTo(w * 0.13, h * 0.42);
    ctx.lineTo(w * 0.33, h * 0.24);
    ctx.lineTo(w * 0.53, h * 0.42);
    ctx.fill();

    ctx.fillStyle = '#5c3d2e';
    ctx.beginPath();
    ctx.roundRect(w * 0.28, h * 0.53, w * 0.08, h * 0.14, 4);
    ctx.fill();

    ctx.fillStyle = '#a8d8ea';
    ctx.strokeStyle = '#5c3d2e'; ctx.lineWidth = 2;
    ctx.beginPath(); ctx.roundRect(w * 0.2, h * 0.48, w * 0.07, h * 0.07, 3); ctx.fill(); ctx.stroke();
    ctx.beginPath(); ctx.roundRect(w * 0.39, h * 0.48, w * 0.07, h * 0.07, 3); ctx.fill(); ctx.stroke();

    ctx.fillStyle = '#c1440e';
    ctx.fillRect(w * 0.38, h * 0.26, w * 0.055, h * 0.1);

    ctx.fillStyle = '#8B5E3C';
    ctx.fillRect(w * 0.7, h * 0.55, w * 0.045, h * 0.1);

    ctx.fillStyle = '#2d8a2d';
    ctx.beginPath(); ctx.arc(w * 0.722, h * 0.48, w * 0.08, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#38A169';
    ctx.beginPath(); ctx.arc(w * 0.71, h * 0.43, w * 0.065, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = '#48BB78';
    ctx.beginPath(); ctx.arc(w * 0.726, h * 0.385, w * 0.05, 0, Math.PI * 2); ctx.fill();

    const flowers = [
        { x: 0.15, y: 0.68, c: '#FF6B9D' },
        { x: 0.25, y: 0.72, c: '#FFD700' },
        { x: 0.62, y: 0.70, c: '#FF6B9D' },
        { x: 0.75, y: 0.75, c: '#a855f7' },
        { x: 0.85, y: 0.68, c: '#FFD700' },
    ];
    flowers.forEach(f => {
        ctx.fillStyle = f.c;
        for (let p = 0; p < 5; p++) {
            const a = (p / 5) * Math.PI * 2;
            ctx.beginPath();
            ctx.arc(w * f.x + Math.cos(a) * 5, h * f.y + Math.sin(a) * 5, 5, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.fillStyle = '#FFD700';
        ctx.beginPath(); ctx.arc(w * f.x, h * f.y, 4, 0, Math.PI * 2); ctx.fill();
    });

    ctx.strokeStyle = '#333'; ctx.lineWidth = 2;
    [[0.44, 0.18], [0.52, 0.22]].forEach(([bx, by]) => {
        ctx.beginPath();
        ctx.moveTo(w * bx, h * by);
        ctx.quadraticCurveTo(w * (bx + 0.02), h * (by - 0.025), w * (bx + 0.04), h * by);
        ctx.stroke();
    });
}


let offscreen;
function buildFullCanvas() {
    offscreen        = document.createElement('canvas');
    offscreen.width  = FULL_SIZE;
    offscreen.height = FULL_SIZE;
    drawScene(offscreen.getContext('2d'), FULL_SIZE, FULL_SIZE);
}

function drawPreview() {
    const canvas = document.getElementById('previewCanvas');
    canvas.width  = 160;
    canvas.height = 160;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(offscreen, 0, 0, 160, 160);
}

function drawPieceCanvas(canvas, pieceIndex) {
    const col  = pieceIndex % GRID;
    const row  = Math.floor(pieceIndex / GRID);
    canvas.width  = PIECE_PX;
    canvas.height = PIECE_PX;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(offscreen, col * PIECE_PX, row * PIECE_PX, PIECE_PX, PIECE_PX, 0, 0, PIECE_PX, PIECE_PX);
}

function buildBoard() {
    const board = document.getElementById('puzzleBoard');
    board.innerHTML = '';
    for (let i = 0; i < GRID * GRID; i++) {
        const slot = document.createElement('div');
        slot.className    = 'board-slot';
        slot.dataset.slot = i;

        const hint = document.createElement('div');
        hint.className   = 'slot-hint';
        hint.textContent = i + 1;
        slot.appendChild(hint);

        slot.addEventListener('dragover',  onDragOver);
        slot.addEventListener('drop',      onDrop);
        slot.addEventListener('dragleave', onDragLeave);

        board.appendChild(slot);
    }
}

function buildTray(order) {
    const tray = document.getElementById('pieceTray');
    tray.innerHTML = '';

    order.forEach(pieceIndex => {
        if (!trayState[pieceIndex]) return;

        const piece        = document.createElement('div');
        piece.className    = 'puzzle-piece';
        piece.draggable    = true;
        piece.dataset.piece = pieceIndex;

        const c = document.createElement('canvas');
        drawPieceCanvas(c, pieceIndex);
        piece.appendChild(c);

        piece.addEventListener('dragstart', onDragStart);
        piece.addEventListener('dragend',   onDragEnd);
        piece.addEventListener('touchstart', onTouchStart, { passive: false });
        piece.addEventListener('touchmove',  onTouchMove,  { passive: false });
        piece.addEventListener('touchend',   onTouchEnd);

        tray.appendChild(piece);
    });
}

let trayOrder = [];
function shufflePieces() {
    trayOrder = [...Array(GRID * GRID).keys()].sort(() => Math.random() - 0.5);
    rebuildTray();
}

function rebuildTray() {
    buildTray(trayOrder);
}

function onDragStart(e) {
    dragPiece = {
        pieceIndex: parseInt(e.currentTarget.dataset.piece),
        fromSlot:   null,
    };
    e.currentTarget.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function onDragEnd(e) {
    e.currentTarget.classList.remove('dragging');
}

function onDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function onDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function onDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    if (dragPiece === null) return;

    const slotIndex = parseInt(e.currentTarget.dataset.slot);
    placePiece(dragPiece.pieceIndex, slotIndex);
    dragPiece = null;
}

function makePlacedPieceDraggable(el, pieceIndex, slotIndex) {
    el.draggable = true;
    el.addEventListener('dragstart', (e) => {
        dragPiece = { pieceIndex, fromSlot: slotIndex };
        e.dataTransfer.effectAllowed = 'move';
    });
    el.addEventListener('touchstart', (e) => {
        dragPiece = { pieceIndex, fromSlot: slotIndex };
        onTouchStart(e);
    }, { passive: false });
    el.addEventListener('touchmove',  onTouchMove,  { passive: false });
    el.addEventListener('touchend',   (e) => {
        onTouchEnd(e, slotIndex);
    });
}

function placePiece(pieceIndex, slotIndex) {
    if (boardState[slotIndex] !== null) {
        const oldPiece = boardState[slotIndex];
        trayState[oldPiece] = true;
        boardState[slotIndex] = null;
    }

    if (dragPiece && dragPiece.fromSlot !== null) {
        boardState[dragPiece.fromSlot] = null;
        const oldSlotEl = document.querySelector(`.board-slot[data-slot="${dragPiece.fromSlot}"]`);
        if (oldSlotEl) {
            oldSlotEl.innerHTML = '';
            oldSlotEl.className = 'board-slot';
            const hint = document.createElement('div');
            hint.className   = 'slot-hint';
            hint.textContent = dragPiece.fromSlot + 1;
            oldSlotEl.appendChild(hint);
        }
    }

    boardState[slotIndex] = pieceIndex;
    trayState[pieceIndex] = false;

    const slotEl = document.querySelector(`.board-slot[data-slot="${slotIndex}"]`);
    slotEl.innerHTML = '';

    const placed = document.createElement('div');
    placed.className = 'placed-piece';

    const c = document.createElement('canvas');
    drawPieceCanvas(c, pieceIndex);
    placed.appendChild(c);

    slotEl.appendChild(placed);
    const isCorrect = pieceIndex === slotIndex;
    slotEl.classList.add(isCorrect ? 'correct' : 'wrong');

    makePlacedPieceDraggable(placed, pieceIndex, slotIndex);

    rebuildTray();
    checkComplete();
}

let touchClone   = null;
let touchOffsetX = 0;
let touchOffsetY = 0;

function onTouchStart(e) {
    e.preventDefault();
    const touch  = e.touches[0];
    const target = e.currentTarget;
    const rect   = target.getBoundingClientRect();

    touchOffsetX = touch.clientX - rect.left;
    touchOffsetY = touch.clientY - rect.top;

    touchClone               = target.cloneNode(true);
    touchClone.style.cssText = `
        position: fixed;
        z-index: 9999;
        width: ${rect.width}px;
        height: ${rect.height}px;
        opacity: 0.85;
        pointer-events: none;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        left: ${touch.clientX - touchOffsetX}px;
        top:  ${touch.clientY - touchOffsetY}px;
    `;
    document.body.appendChild(touchClone);

    if (!dragPiece) {
        dragPiece = {
            pieceIndex: parseInt(target.dataset.piece || target.closest('[data-piece]')?.dataset.piece),
            fromSlot:   null,
        };
    }
}

function onTouchMove(e) {
    e.preventDefault();
    if (!touchClone) return;
    const touch = e.touches[0];
    touchClone.style.left = `${touch.clientX - touchOffsetX}px`;
    touchClone.style.top  = `${touch.clientY - touchOffsetY}px`;
}

function onTouchEnd(e, fromSlotOverride) {
    if (touchClone) { touchClone.remove(); touchClone = null; }
    if (!dragPiece) return;

    const touch = e.changedTouches[0];

    const els = document.elementsFromPoint(touch.clientX, touch.clientY);
    const slotEl = els.find(el => el.classList.contains('board-slot') || el.closest('.board-slot'));
    const targetSlot = slotEl?.closest?.('.board-slot') || (slotEl?.classList.contains('board-slot') ? slotEl : null);

    if (targetSlot) {
        const slotIndex = parseInt(targetSlot.dataset.slot);
        if (fromSlotOverride !== undefined) dragPiece.fromSlot = fromSlotOverride;
        placePiece(dragPiece.pieceIndex, slotIndex);
    }

    dragPiece = null;
}

function checkComplete() {
    const allPlaced = boardState.every(p => p !== null);
    const correct   = boardState.filter((p, i) => p === i).length;

    document.getElementById('btnNext').className = allPlaced ? 'btn-nav' : 'btn-nav locked';

    if (allPlaced) {
        document.getElementById('tapHint').textContent    = `Done! ${correct}/9 in the right spot. Click Next → to submit.`;
        document.getElementById('answerHint').textContent = `${correct} out of 9 correct — click Next → to submit!`;
    } else {
        const remaining = boardState.filter(p => p === null).length;
        document.getElementById('tapHint').textContent    = `${remaining} piece${remaining > 1 ? 's' : ''} left to place`;
        document.getElementById('answerHint').textContent = `Place all 9 pieces to unlock Next →`;
    }
}

function clickNext() {
    if (isLocked)                              { openModal('lockedModal'); return; }
    if (boardState.some(p => p === null))      return;
    openModal('confirmModal');
}

function submitAnswer() {
    closeModal('confirmModal');
    const correct = boardState.filter((p, i) => p === i).length;
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

function clickSkip(event) { event.preventDefault(); skipUrl = event.currentTarget.href; openModal('skipModal'); }
function doSkip()          { closeModal('skipModal'); window.location.href = skipUrl; }

function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

if (isLocked) {
    document.getElementById('lockedBanner').style.display = 'block';
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    boardState = [...Array(GRID * GRID).keys()];
    trayState  = new Array(GRID * GRID).fill(false);
}

buildFullCanvas();
drawPreview();
buildBoard();
shufflePieces();

if (isLocked) {
    boardState.forEach((pieceIndex, slotIndex) => {
        const slotEl = document.querySelector(`.board-slot[data-slot="${slotIndex}"]`);
        slotEl.innerHTML = '';
        const placed = document.createElement('div');
        placed.className = 'placed-piece';
        const c = document.createElement('canvas');
        drawPieceCanvas(c, pieceIndex);
        placed.appendChild(c);
        slotEl.appendChild(placed);
        slotEl.classList.add('correct');
    });
    document.getElementById('pieceTray').innerHTML = '';
    checkComplete();
}

</script>
</body>
</html>