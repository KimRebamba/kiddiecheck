<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point to the Picture!</title>
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

        /* Mini dots — identical to letter match */
        .mini-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.4rem; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background: #e2e8f0; border: 2px solid #ccc; transition: all 0.3s; }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }
        .dot.correct { background: #38A169; border-color: #38A169; }
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; }

        /* Game area */
        .game-area {
            width: 100%;
            max-width: 560px;
            margin: 0 auto 0.6rem;
        }

        .round-label {
            font-size: 0.78rem;
            font-weight: 800;
            color: #7C3AED;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        /* Called word banner */
        .called-word-wrap { margin-bottom: 1.4rem; }
        .called-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 6px;
        }
        .called-word {
            display: inline-block;
            font-size: 1.9rem;
            font-weight: 900;
            color: #1a1a2e;
            background: #fff;
            border: 3px solid #7C3AED;
            border-radius: 16px;
            padding: 8px 32px;
            letter-spacing: 0.04em;
            box-shadow: 4px 4px 0 #7C3AED;
        }
        .btn-say {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            background: #f3f0ff;
            border: 2px solid #c4b5fd;
            color: #5b21b6;
            cursor: pointer;
        }
        .btn-say:hover { background: #e9d5ff; }

        /* Picture grid */
        .picture-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin: 0 auto;
        }
        @media (max-width: 560px) {
            .picture-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .pic-card {
            aspect-ratio: 1;
            border-radius: 16px;
            border: 3px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: transform 0.15s, border-color 0.15s, background 0.15s, box-shadow 0.15s;
            position: relative;
            overflow: hidden;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        .pic-card:hover {
            transform: scale(1.06);
            border-color: #7C3AED;
            box-shadow: 0 6px 20px rgba(124,58,237,0.2);
        }
        /* once round is checked, no hover */
        .pic-card.done:hover {
            transform: none;
            border-color: inherit;
            box-shadow: none;
            cursor: default;
        }
        .pic-card.correct {
            border-color: #38A169 !important;
            background: #f0fff4 !important;
            box-shadow: 0 0 0 3px rgba(56,161,105,0.3) !important;
        }
        .pic-card.wrong {
            border-color: #E53E3E !important;
            background: #fff5f5 !important;
            box-shadow: 0 0 0 3px rgba(229,62,62,0.2) !important;
        }
        .pic-card.selected {
            border-color: #7C3AED !important;
            background: #f3f0ff !important;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.3) !important;
            transform: scale(1.08);
        }
        .pic-card canvas {
            width: 68%;
            height: 68%;
            display: block;
            border-radius: 6px;
        }
        .pic-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #555;
            text-transform: capitalize;
            letter-spacing: 0.02em;
        }
        .pic-card.correct .pic-label { color: #276749; }
        .pic-card.wrong   .pic-label { color: #C53030; }

        .feedback-badge {
            position: absolute;
            top: 5px; right: 5px;
            font-size: 0.9rem;
            display: none;
        }
        .pic-card.correct .feedback-badge,
        .pic-card.wrong   .feedback-badge { display: block; }

        /* Controls row — same as letter match */
        .game-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 1.2rem;
            flex-wrap: wrap;
        }
        .pairs-status {
            font-size: 0.82rem;
            font-weight: 700;
            color: #aaa;
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
        .btn-check:hover:not(:disabled) { background: #6D28D9; }
        .btn-check:disabled { opacity: 0.4; cursor: not-allowed; }

        /* Round result — identical to letter match */
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
            .called-word { font-size: 1.5rem; padding: 7px 22px; }
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

        <div class="game-title">👆 Point to the Picture!</div>
        <div class="game-subtitle">Tap the picture that matches the word — 5 rounds!</div>

        <div class="mini-dots" id="miniDots"></div>

        <div class="game-area" id="gameArea">
            <div class="round-label" id="roundLabel"></div>

            <div class="called-word-wrap">
                <div class="called-label">👂 Find this one</div>
                <div class="called-word" id="calledWord"></div>
                <br>
                <button class="btn-say" onclick="speakWord()">🔊 Say it again</button>
            </div>

            <div class="picture-grid" id="pictureGrid"></div>

            <div class="game-controls">
                <span class="pairs-status" id="pairsStatus"></span>
                <button class="btn-check" id="btnCheck" onclick="checkRound()" disabled>Check ✓</button>
            </div>

            <div class="round-result" id="roundResult"></div>
            <button class="btn-next-round" id="btnNextRound" onclick="nextRound()"></button>
        </div>

        <div class="tap-hint" id="tapHint">Tap the picture that matches the word above</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Complete all 5 rounds to unlock Next →</div>

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
            <button class="btn-ok"     onclick="submitAnswer()">Yes, Submit</button>
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
        <div class="modal-body">This will skip <strong>all 5 rounds</strong> and move to the next question.<br><br>You can come back later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
// ─── 5 Rounds ────────────────────────────────────────────────────────────────
const ROUNDS = [
    { target: 'dog',   pictures: ['dog',   'cup',   'bird',  'shoe',  'car']   },
    { target: 'ball',  pictures: ['ball',  'tree',  'fish',  'hat',   'book']  },
    { target: 'apple', pictures: ['apple', 'chair', 'spoon', 'bus',   'duck']  },
    { target: 'house', pictures: ['house', 'star',  'frog',  'sock',  'cake']  },
    { target: 'sun',   pictures: ['sun',   'boat',  'milk',  'bear',  'hand']  },
];

const PASS_SCORE = 3;

const existing = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existing !== '';

let current   = 0;
let answers   = [null, null, null, null, null];
let skipUrl   = null;
let checked   = false;
let selected  = null; // currently selected card element

// ─── Draw functions ───────────────────────────────────────────────────────────
function drawDog(ctx,w,h){
    ctx.fillStyle='#C8A26B';
    ctx.beginPath();ctx.ellipse(w*.5,h*.6,w*.28,h*.2,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.arc(w*.72,h*.38,w*.18,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#A0784A';
    ctx.beginPath();ctx.ellipse(w*.6,h*.26,w*.07,h*.12,-0.4,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.ellipse(w*.84,h*.26,w*.07,h*.12,0.4,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#E8C898';ctx.beginPath();ctx.ellipse(w*.78,h*.44,w*.09,h*.07,0,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#222';
    ctx.beginPath();ctx.ellipse(w*.78,h*.40,w*.04,h*.03,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.arc(w*.66,h*.34,w*.025,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.arc(w*.79,h*.34,w*.025,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#C8A26B';
    [[.32,.72],[.44,.72],[.56,.72],[.68,.72]].forEach(([x,y])=>{ctx.beginPath();ctx.roundRect(w*x-w*.045,h*y,w*.09,h*.2,4);ctx.fill();});
    ctx.strokeStyle='#A0784A';ctx.lineWidth=w*.05;ctx.lineCap='round';
    ctx.beginPath();ctx.moveTo(w*.22,h*.6);ctx.quadraticCurveTo(w*.08,h*.4,w*.16,h*.28);ctx.stroke();
}
function drawCup(ctx,w,h){
    const g=ctx.createLinearGradient(w*.25,0,w*.75,0);g.addColorStop(0,'#e74c3c');g.addColorStop(1,'#c0392b');
    ctx.fillStyle=g;ctx.beginPath();ctx.moveTo(w*.28,h*.28);ctx.lineTo(w*.72,h*.28);ctx.lineTo(w*.64,h*.82);ctx.lineTo(w*.36,h*.82);ctx.closePath();ctx.fill();
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.ellipse(w*.5,h*.28,w*.22,h*.055,0,0,Math.PI*2);ctx.fill();
    ctx.strokeStyle='#a93226';ctx.lineWidth=w*.055;ctx.lineCap='round';
    ctx.beginPath();ctx.moveTo(w*.72,h*.4);ctx.quadraticCurveTo(w*.92,h*.4,w*.92,h*.58);ctx.quadraticCurveTo(w*.92,h*.72,w*.72,h*.72);ctx.stroke();
}
function drawBird(ctx,w,h){
    ctx.fillStyle='#3498db';
    ctx.beginPath();ctx.ellipse(w*.5,h*.6,w*.22,h*.18,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.arc(w*.65,h*.36,w*.16,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#2980b9';ctx.beginPath();ctx.ellipse(w*.42,h*.58,w*.18,h*.1,-0.4,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.moveTo(w*.28,h*.65);ctx.lineTo(w*.12,h*.55);ctx.lineTo(w*.14,h*.68);ctx.lineTo(w*.1,h*.78);ctx.lineTo(w*.28,h*.7);ctx.fill();
    ctx.fillStyle='#f39c12';ctx.beginPath();ctx.moveTo(w*.78,h*.34);ctx.lineTo(w*.92,h*.38);ctx.lineTo(w*.78,h*.42);ctx.closePath();ctx.fill();
    ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(w*.68,h*.33,w*.04,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#111';ctx.beginPath();ctx.arc(w*.69,h*.33,w*.022,0,Math.PI*2);ctx.fill();
}
function drawShoe(ctx,w,h){
    ctx.fillStyle='#2c3e50';ctx.beginPath();ctx.ellipse(w*.5,h*.8,w*.4,h*.08,0,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#e74c3c';
    ctx.beginPath();ctx.moveTo(w*.12,h*.72);ctx.lineTo(w*.12,h*.5);ctx.quadraticCurveTo(w*.12,h*.28,w*.42,h*.28);ctx.lineTo(w*.82,h*.28);ctx.quadraticCurveTo(w*.9,h*.28,w*.9,h*.38);ctx.lineTo(w*.9,h*.62);ctx.quadraticCurveTo(w*.88,h*.72,w*.82,h*.72);ctx.closePath();ctx.fill();
    ctx.fillStyle='#ecf0f1';ctx.beginPath();ctx.roundRect(w*.38,h*.28,w*.24,h*.22,4);ctx.fill();
    ctx.strokeStyle='#fff';ctx.lineWidth=w*.018;ctx.lineCap='round';
    [.34,.42,.5].forEach(y=>{ctx.beginPath();ctx.moveTo(w*.38,h*y);ctx.lineTo(w*.62,h*y);ctx.stroke();});
}
function drawCar(ctx,w,h){
    ctx.fillStyle='#2980b9';ctx.beginPath();ctx.roundRect(w*.08,h*.52,w*.84,h*.3,8);ctx.fill();
    ctx.beginPath();ctx.moveTo(w*.24,h*.52);ctx.lineTo(w*.3,h*.3);ctx.lineTo(w*.7,h*.3);ctx.lineTo(w*.78,h*.52);ctx.closePath();ctx.fill();
    ctx.fillStyle='#a8d8ea';ctx.beginPath();ctx.roundRect(w*.32,h*.33,w*.15,h*.15,4);ctx.fill();ctx.beginPath();ctx.roundRect(w*.52,h*.33,w*.15,h*.15,4);ctx.fill();
    [[.24,.82],[.74,.82]].forEach(([x,y])=>{ctx.fillStyle='#1a1a2e';ctx.beginPath();ctx.arc(w*x,h*y,w*.13,0,Math.PI*2);ctx.fill();ctx.fillStyle='#aaa';ctx.beginPath();ctx.arc(w*x,h*y,w*.07,0,Math.PI*2);ctx.fill();});
}
function drawBall(ctx,w,h){
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.arc(w*.5,h*.5,w*.36,0,Math.PI*2);ctx.fill();
    ctx.strokeStyle='#fff';ctx.lineWidth=w*.03;ctx.lineCap='round';
    ctx.beginPath();ctx.moveTo(w*.5,h*.14);ctx.quadraticCurveTo(w*.3,h*.5,w*.5,h*.86);ctx.stroke();
    ctx.beginPath();ctx.moveTo(w*.5,h*.14);ctx.quadraticCurveTo(w*.7,h*.5,w*.5,h*.86);ctx.stroke();
    ctx.beginPath();ctx.moveTo(w*.14,h*.5);ctx.lineTo(w*.86,h*.5);ctx.stroke();
    ctx.fillStyle='rgba(255,255,255,0.3)';ctx.beginPath();ctx.ellipse(w*.38,h*.34,w*.1,w*.06,-0.5,0,Math.PI*2);ctx.fill();
}
function drawTree(ctx,w,h){
    ctx.fillStyle='#8B5E3C';ctx.beginPath();ctx.roundRect(w*.44,h*.62,w*.12,h*.3,3);ctx.fill();
    ctx.fillStyle='#27ae60';ctx.beginPath();ctx.moveTo(w*.5,h*.1);ctx.lineTo(w*.82,h*.52);ctx.lineTo(w*.18,h*.52);ctx.closePath();ctx.fill();
    ctx.fillStyle='#2ecc71';ctx.beginPath();ctx.moveTo(w*.5,h*.06);ctx.lineTo(w*.78,h*.44);ctx.lineTo(w*.22,h*.44);ctx.closePath();ctx.fill();
    ctx.fillStyle='#1a8a42';ctx.beginPath();ctx.moveTo(w*.5,h*.02);ctx.lineTo(w*.7,h*.34);ctx.lineTo(w*.3,h*.34);ctx.closePath();ctx.fill();
}
function drawFish(ctx,w,h){
    ctx.fillStyle='#f39c12';ctx.beginPath();ctx.ellipse(w*.46,h*.5,w*.3,h*.18,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.moveTo(w*.76,h*.5);ctx.lineTo(w*.94,h*.3);ctx.lineTo(w*.94,h*.7);ctx.closePath();ctx.fill();
    ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(w*.28,h*.44,w*.04,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#111';ctx.beginPath();ctx.arc(w*.285,h*.44,w*.02,0,Math.PI*2);ctx.fill();
}
function drawHat(ctx,w,h){
    ctx.fillStyle='#2c3e50';ctx.beginPath();ctx.ellipse(w*.5,h*.72,w*.42,h*.1,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.roundRect(w*.28,h*.24,w*.44,h*.5,[6,6,0,0]);ctx.fill();
    ctx.fillStyle='#e74c3c';ctx.fillRect(w*.28,h*.56,w*.44,h*.06);
    ctx.fillStyle='#f1c40f';ctx.beginPath();ctx.arc(w*.5,h*.59,w*.04,0,Math.PI*2);ctx.fill();
}
function drawBook(ctx,w,h){
    ctx.fillStyle='#9b59b6';ctx.beginPath();ctx.roundRect(w*.2,h*.15,w*.6,h*.7,6);ctx.fill();
    ctx.fillStyle='#8e44ad';ctx.beginPath();ctx.roundRect(w*.2,h*.15,w*.08,h*.7,[6,0,0,6]);ctx.fill();
    ctx.fillStyle='rgba(255,255,255,0.6)';[.3,.4,.5,.6].forEach(y=>{ctx.fillRect(w*.34,h*y,w*.36,h*.03);});
}
function drawApple(ctx,w,h){
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.arc(w*.46,h*.54,w*.32,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.62,h*.48,w*.26,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#8B5E3C';ctx.beginPath();ctx.roundRect(w*.48,h*.18,w*.06,h*.18,3);ctx.fill();
    ctx.fillStyle='#27ae60';ctx.beginPath();ctx.moveTo(w*.54,h*.24);ctx.quadraticCurveTo(w*.72,h*.16,w*.7,h*.34);ctx.quadraticCurveTo(w*.58,h*.3,w*.54,h*.24);ctx.fill();
    ctx.fillStyle='rgba(255,255,255,0.35)';ctx.beginPath();ctx.ellipse(w*.38,h*.42,w*.1,w*.06,-0.5,0,Math.PI*2);ctx.fill();
}
function drawChair(ctx,w,h){
    ctx.fillStyle='#8B5E3C';
    ctx.beginPath();ctx.roundRect(w*.2,h*.28,w*.6,h*.08,4);ctx.fill();
    ctx.beginPath();ctx.roundRect(w*.2,h*.12,w*.08,h*.2,4);ctx.fill();
    [[.2,.36],[.72,.36]].forEach(([x,y])=>{ctx.beginPath();ctx.roundRect(w*x,h*y,w*.08,h*.5,4);ctx.fill();});
    ctx.beginPath();ctx.roundRect(w*.35,h*.36,w*.3,h*.06,3);ctx.fill();
}
function drawSpoon(ctx,w,h){
    ctx.fillStyle='#bdc3c7';
    ctx.beginPath();ctx.ellipse(w*.5,h*.25,w*.2,h*.18,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.roundRect(w*.46,h*.38,w*.08,h*.5,4);ctx.fill();
    ctx.fillStyle='#ecf0f1';ctx.beginPath();ctx.ellipse(w*.44,h*.22,w*.08,w*.06,-0.3,0,Math.PI*2);ctx.fill();
}
function drawBus(ctx,w,h){
    ctx.fillStyle='#f1c40f';ctx.beginPath();ctx.roundRect(w*.08,h*.34,w*.84,h*.38,[10,10,6,6]);ctx.fill();
    ctx.fillStyle='#a8d8ea';[.16,.32,.5,.66].forEach(x=>{ctx.beginPath();ctx.roundRect(w*x,h*.38,w*.14,h*.16,4);ctx.fill();});
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.roundRect(w*.78,h*.38,w*.1,h*.16,4);ctx.fill();
    [[.22,.72],[.72,.72]].forEach(([x,y])=>{ctx.fillStyle='#1a1a2e';ctx.beginPath();ctx.arc(w*x,h*y,w*.1,0,Math.PI*2);ctx.fill();ctx.fillStyle='#aaa';ctx.beginPath();ctx.arc(w*x,h*y,w*.055,0,Math.PI*2);ctx.fill();});
}
function drawDuck(ctx,w,h){
    ctx.fillStyle='#f1c40f';
    ctx.beginPath();ctx.ellipse(w*.5,h*.62,w*.28,h*.2,0,0,Math.PI*2);ctx.fill();
    ctx.beginPath();ctx.arc(w*.68,h*.36,w*.18,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#e67e22';ctx.beginPath();ctx.moveTo(w*.84,h*.36);ctx.lineTo(w*.96,h*.34);ctx.lineTo(w*.96,h*.4);ctx.lineTo(w*.84,h*.4);ctx.closePath();ctx.fill();
    ctx.fillStyle='#111';ctx.beginPath();ctx.arc(w*.74,h*.32,w*.025,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#f39c12';ctx.beginPath();ctx.ellipse(w*.36,h*.72,w*.1,h*.06,-0.3,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.ellipse(w*.58,h*.76,w*.1,h*.06,0.3,0,Math.PI*2);ctx.fill();
}
function drawHouse(ctx,w,h){
    ctx.fillStyle='#e8c07a';ctx.fillRect(w*.18,h*.46,w*.64,h*.42);
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.moveTo(w*.1,h*.46);ctx.lineTo(w*.5,h*.16);ctx.lineTo(w*.9,h*.46);ctx.closePath();ctx.fill();
    ctx.fillStyle='#5c3d2e';ctx.beginPath();ctx.roundRect(w*.42,h*.66,w*.16,h*.22,3);ctx.fill();
    ctx.fillStyle='#a8d8ea';ctx.beginPath();ctx.roundRect(w*.22,h*.54,w*.16,h*.14,3);ctx.fill();ctx.beginPath();ctx.roundRect(w*.62,h*.54,w*.16,h*.14,3);ctx.fill();
}
function drawStar(ctx,w,h){
    ctx.fillStyle='#f1c40f';ctx.beginPath();
    for(let i=0;i<5;i++){const o=(i*4*Math.PI/5)-Math.PI/2,n=o+(2*Math.PI/10);if(i===0)ctx.moveTo(w*.5+Math.cos(o)*w*.38,h*.5+Math.sin(o)*w*.38);else ctx.lineTo(w*.5+Math.cos(o)*w*.38,h*.5+Math.sin(o)*w*.38);ctx.lineTo(w*.5+Math.cos(n)*w*.16,h*.5+Math.sin(n)*w*.16);}
    ctx.closePath();ctx.fill();ctx.strokeStyle='#e67e22';ctx.lineWidth=2;ctx.stroke();
}
function drawFrog(ctx,w,h){
    ctx.fillStyle='#27ae60';ctx.beginPath();ctx.ellipse(w*.5,h*.58,w*.3,h*.24,0,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.5,h*.4,w*.22,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#2ecc71';ctx.beginPath();ctx.arc(w*.34,h*.28,w*.12,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.66,h*.28,w*.12,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(w*.34,h*.28,w*.07,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.66,h*.28,w*.07,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#111';ctx.beginPath();ctx.arc(w*.34,h*.28,w*.04,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.66,h*.28,w*.04,0,Math.PI*2);ctx.fill();
    ctx.strokeStyle='#1a8a42';ctx.lineWidth=w*.04;ctx.lineCap='round';ctx.beginPath();ctx.moveTo(w*.38,h*.46);ctx.quadraticCurveTo(w*.5,h*.52,w*.62,h*.46);ctx.stroke();
}
function drawSock(ctx,w,h){
    ctx.fillStyle='#9b59b6';
    ctx.beginPath();ctx.moveTo(w*.36,h*.12);ctx.lineTo(w*.64,h*.12);ctx.lineTo(w*.64,h*.68);ctx.quadraticCurveTo(w*.64,h*.88,w*.8,h*.88);ctx.quadraticCurveTo(w*.88,h*.88,w*.88,h*.78);ctx.quadraticCurveTo(w*.88,h*.68,w*.72,h*.68);ctx.lineTo(w*.72,h*.12);ctx.closePath();ctx.fill();
    ctx.fillStyle='#8e44ad';ctx.fillRect(w*.36,h*.12,w*.28,h*.1);
}
function drawCake(ctx,w,h){
    ctx.fillStyle='#f39c12';ctx.beginPath();ctx.roundRect(w*.16,h*.5,w*.68,h*.38,[0,0,10,10]);ctx.fill();
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.ellipse(w*.5,h*.5,w*.34,h*.09,0,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#fff';[.26,.38,.5,.62,.74].forEach(x=>{ctx.beginPath();ctx.arc(w*x,h*.5,w*.03,0,Math.PI*2);ctx.fill();});
    ctx.fillStyle='#f1c40f';ctx.beginPath();ctx.roundRect(w*.46,h*.28,w*.08,h*.24,3);ctx.fill();
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.moveTo(w*.5,h*.22);ctx.quadraticCurveTo(w*.56,h*.28,w*.5,h*.3);ctx.quadraticCurveTo(w*.44,h*.28,w*.5,h*.22);ctx.fill();
}
function drawSun(ctx,w,h){
    ctx.strokeStyle='#f39c12';ctx.lineWidth=w*.04;ctx.lineCap='round';
    for(let i=0;i<8;i++){const a=(i/8)*Math.PI*2;ctx.beginPath();ctx.moveTo(w*.5+Math.cos(a)*w*.28,h*.5+Math.sin(a)*w*.28);ctx.lineTo(w*.5+Math.cos(a)*w*.42,h*.5+Math.sin(a)*w*.42);ctx.stroke();}
    ctx.fillStyle='#f1c40f';ctx.beginPath();ctx.arc(w*.5,h*.5,w*.24,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='rgba(255,255,255,0.45)';ctx.beginPath();ctx.ellipse(w*.4,h*.4,w*.08,w*.05,-0.5,0,Math.PI*2);ctx.fill();
}
function drawBoat(ctx,w,h){
    ctx.fillStyle='#2980b9';ctx.beginPath();ctx.moveTo(w*.12,h*.54);ctx.lineTo(w*.88,h*.54);ctx.lineTo(w*.76,h*.78);ctx.lineTo(w*.24,h*.78);ctx.closePath();ctx.fill();
    ctx.fillStyle='#e74c3c';ctx.beginPath();ctx.roundRect(w*.44,h*.22,w*.06,h*.34,3);ctx.fill();
    ctx.fillStyle='#ecf0f1';ctx.beginPath();ctx.moveTo(w*.5,h*.22);ctx.lineTo(w*.82,h*.5);ctx.lineTo(w*.5,h*.56);ctx.closePath();ctx.fill();
}
function drawMilk(ctx,w,h){
    ctx.fillStyle='#ecf0f1';ctx.beginPath();ctx.moveTo(w*.3,h*.28);ctx.lineTo(w*.7,h*.28);ctx.lineTo(w*.64,h*.84);ctx.lineTo(w*.36,h*.84);ctx.closePath();ctx.fill();
    ctx.strokeStyle='#bdc3c7';ctx.lineWidth=2;ctx.stroke();
    ctx.fillStyle='#3498db';ctx.beginPath();ctx.roundRect(w*.3,h*.28,w*.4,h*.1,0);ctx.fill();
    ctx.fillStyle='#2980b9';ctx.beginPath();ctx.roundRect(w*.36,h*.16,w*.28,h*.14,4);ctx.fill();
    ctx.fillStyle='#1a5276';ctx.font=`bold ${w*.1}px sans-serif`;ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText('MILK',w*.5,h*.6);
}
function drawBear(ctx,w,h){
    ctx.fillStyle='#8B5E3C';ctx.beginPath();ctx.ellipse(w*.5,h*.6,w*.26,h*.22,0,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.5,h*.36,w*.2,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.32,h*.24,w*.1,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.68,h*.24,w*.1,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#A0784A';ctx.beginPath();ctx.arc(w*.32,h*.24,w*.06,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.68,h*.24,w*.06,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.ellipse(w*.5,h*.42,w*.1,h*.08,0,0,Math.PI*2);ctx.fill();
    ctx.fillStyle='#222';ctx.beginPath();ctx.arc(w*.42,h*.32,w*.025,0,Math.PI*2);ctx.fill();ctx.beginPath();ctx.arc(w*.58,h*.32,w*.025,0,Math.PI*2);ctx.fill();
    ctx.strokeStyle='#5c3d2e';ctx.lineWidth=w*.02;ctx.lineCap='round';ctx.beginPath();ctx.moveTo(w*.44,h*.46);ctx.quadraticCurveTo(w*.5,h*.5,w*.56,h*.46);ctx.stroke();
}
function drawHand(ctx,w,h){
    ctx.fillStyle='#FFDAB9';
    ctx.beginPath();ctx.roundRect(w*.26,h*.44,w*.48,h*.42,10);ctx.fill();
    [[.28,.22,w*.09,h*.24],[.39,.18,w*.09,h*.28],[.51,.18,w*.09,h*.28],[.62,.22,w*.09,h*.24]].forEach(([x,y,fw,fh])=>{ctx.beginPath();ctx.roundRect(w*x,h*y,fw,fh,8);ctx.fill();});
    ctx.beginPath();ctx.roundRect(w*.14,h*.48,w*.14,h*.22,8);ctx.fill();
    ctx.strokeStyle='#E8B490';ctx.lineWidth=1.5;ctx.beginPath();ctx.roundRect(w*.26,h*.44,w*.48,h*.42,10);ctx.stroke();
}

const DRAW_FNS = {
    dog:drawDog,cup:drawCup,bird:drawBird,shoe:drawShoe,car:drawCar,
    ball:drawBall,tree:drawTree,fish:drawFish,hat:drawHat,book:drawBook,
    apple:drawApple,chair:drawChair,spoon:drawSpoon,bus:drawBus,duck:drawDuck,
    house:drawHouse,star:drawStar,frog:drawFrog,sock:drawSock,cake:drawCake,
    sun:drawSun,boat:drawBoat,milk:drawMilk,bear:drawBear,hand:drawHand,
};

function shuffle(arr){
    const a=[...arr];
    for(let i=a.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[a[i],a[j]]=[a[j],a[i]];}
    return a;
}

// ─── Render ───────────────────────────────────────────────────────────────────
function render() {
    checked  = false;
    selected = null;

    const round = ROUNDS[current];
    document.getElementById('roundLabel').textContent  = `Round ${current + 1} of ${ROUNDS.length}`;
    document.getElementById('calledWord').textContent  = round.target.toUpperCase();
    document.getElementById('roundResult').className   = 'round-result';
    document.getElementById('roundResult').textContent = '';
    document.getElementById('btnNextRound').classList.remove('visible');
    document.getElementById('btnCheck').disabled       = true;
    document.getElementById('pairsStatus').textContent = 'Tap a picture to select it';

    buildGrid(round);
    buildDots();
    updateHints();

    if (!isLocked) speakWord();
}

function buildGrid(round) {
    const grid     = document.getElementById('pictureGrid');
    grid.innerHTML = '';
    const shuffled = shuffle(round.pictures);

    shuffled.forEach(label => {
        const isTarget = label === round.target;

        const card = document.createElement('div');
        card.className       = 'pic-card';
        card.dataset.label   = label;
        card.dataset.correct = isTarget ? '1' : '0';

        const canvas  = document.createElement('canvas');
        canvas.width  = 120;
        canvas.height = 120;
        if (DRAW_FNS[label]) DRAW_FNS[label](canvas.getContext('2d'), 120, 120);
        card.appendChild(canvas);

        const badge = document.createElement('div');
        badge.className   = 'feedback-badge';
        badge.textContent = isTarget ? '✅' : '❌';
        card.appendChild(badge);

        const lbl = document.createElement('div');
        lbl.className   = 'pic-label';
        lbl.textContent = '?';
        card.appendChild(lbl);

        card.addEventListener('click', () => onCardClick(card));

        grid.appendChild(card);
    });
}

function onCardClick(card) {
    if (checked || isLocked) return;

    // Deselect if same card tapped again
    if (selected === card) {
        card.classList.remove('selected');
        selected = null;
        document.getElementById('btnCheck').disabled       = true;
        document.getElementById('pairsStatus').textContent = 'Tap a picture to select it';
        return;
    }

    // Deselect previous
    if (selected) selected.classList.remove('selected');

    card.classList.add('selected');
    selected = card;

    document.getElementById('pairsStatus').textContent = `"${card.dataset.label.toUpperCase()}" selected — tap Check ✓ to confirm`;
    document.getElementById('btnCheck').disabled = false;
}

function checkRound() {
    if (!selected || checked) return;
    checked = true;

    const isCorrect = selected.dataset.correct === '1';
    answers[current] = isCorrect;

    // Style all cards
    document.querySelectorAll('.pic-card').forEach(card => {
        card.classList.remove('selected');
        card.classList.add('done');
        card.querySelector('.pic-label').textContent = card.dataset.label;
        if (card.dataset.correct === '1') card.classList.add('correct');
        else if (card === selected)       card.classList.add('wrong');
    });

    selected = null;

    const res = document.getElementById('roundResult');
    res.className = 'round-result show ' + (isCorrect ? 'all-correct' : 'has-errors');
    res.textContent = isCorrect
        ? `🌟 Correct! That's a ${ROUNDS[current].target}!`
        : `💪 Not quite — the ${ROUNDS[current].target} is highlighted below.`;

    document.getElementById('btnCheck').disabled       = true;
    document.getElementById('pairsStatus').textContent = '';

    const remaining = answers.filter(a => a === null).length;
    const btn = document.getElementById('btnNextRound');
    btn.textContent = remaining > 0 ? 'Next Round →' : 'Done! 🎉';
    btn.classList.add('visible');

    buildDots();
    updateHints();
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
    document.getElementById('btnNext').className = (allDone || isLocked) ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('tapHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : 'Tap the picture that matches the word above';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} round${remaining > 1 ? 's' : ''} left — complete all to unlock Next →`;
}

function speakWord() {
    const word = document.getElementById('calledWord').textContent;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utt = new SpeechSynthesisUtterance(word);
        utt.rate = 0.85;
        window.speechSynthesis.speak(utt);
    }
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
    answers = ROUNDS.map(() => true);
}

render();
</script>
</body>
</html>