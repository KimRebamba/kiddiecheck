<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How Are They Feeling?</title>
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
            padding: 2rem 60px 1.5rem;
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

        .mini-dots { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.8rem; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background: #e2e8f0; border: 2px solid #ccc; transition: all 0.3s; }
        .dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }
        .dot.correct { background: #38A169; border-color: #38A169; }
        .dot.wrong   { background: #E53E3E; border-color: #E53E3E; }

        .card-wrap {
            width: 100%;
            max-width: 500px;
            height: 380px;
            perspective: 900px;
            margin: 0 auto 1.5rem;
            cursor: pointer;
        }

        .card-inner {
            width: 100%; height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.55s ease;
            border-radius: 28px;
        }
        .card-inner.flipped { transform: rotateY(180deg); }

        .front, .back {
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 28px;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            border: 4px solid rgba(0,0,0,0.12);
            box-shadow: 0 8px 28px rgba(0,0,0,0.18);
            overflow: hidden;
        }

        .front { background: #fff; cursor: pointer; }
        .front canvas { width: 100%; height: 100%; display: block; }

        .back {
            background: #fff;
            transform: rotateY(180deg);
            padding: 24px 20px;
            justify-content: space-between;
            gap: 12px;
        }

        .back-question {
            font-size: 1rem;
            font-weight: 900;
            color: #7C3AED;
            text-align: center;
            background: #f3f0ff;
            border: 2px solid #c4b5fd;
            border-radius: 12px;
            padding: 8px 16px;
            width: 100%;
        }

        .back-emoji { font-size: 3.5rem; line-height: 1; }

        .back-answer {
            font-size: 2rem;
            font-weight: 900;
            color: #1a1a2e;
            text-align: center;
            line-height: 1.2;
        }

        .back-description {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            font-style: italic;
            line-height: 1.4;
            padding: 0 8px;
        }

        .back-prompt { font-size: 0.82rem; color: #888; font-weight: 700; }
        .back-result { font-size: 2rem; display: none; }

        .back-buttons { display: flex; gap: 10px; width: 100%; padding: 0 8px; }

        .btn-yes {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #C6F6D5; border: 2px solid #38A169; color: #276749;
            cursor: pointer;
        }
        .btn-yes:hover:not(:disabled) { background: #9AE6B4; }

        .btn-no {
            flex: 1; padding: 12px 8px; border-radius: 14px;
            font-size: 1rem; font-weight: 800;
            background: #FED7D7; border: 2px solid #E53E3E; color: #9B2C2C;
            cursor: pointer;
        }
        .btn-no:hover:not(:disabled) { background: #FEB2B2; }

        .btn-yes:disabled, .btn-no:disabled { opacity: 0.5; cursor: default; }

        .card-inner.answered-correct .back { border-color: #38A169; box-shadow: 0 0 0 4px #38A169; }
        .card-inner.answered-wrong   .back { border-color: #E53E3E; box-shadow: 0 0 0 4px #E53E3E; }

        .tap-hint    { font-size: 0.82rem; color: #bbb; margin-top: 0.2rem; }
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
            .card-wrap { height: 300px; }
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

        <div class="game-title">😊 Name That Feeling!</div>
        <div class="game-subtitle">Show each face — ask the child to name the emotion — tap to reveal!</div>

        <div class="mini-dots" id="miniDots"></div>

        <div class="card-wrap" id="cardWrap">
            <div class="card-inner" id="cardInner">

                <div class="front" id="cardFront">
                    <canvas id="faceCanvas"></canvas>
                </div>

                <div class="back">
                    <div class="back-question">❓ Anong damdamin? / What is the feeling?</div>
                    <div class="back-emoji"       id="backEmoji"></div>
                    <div class="back-answer"      id="backAnswer"></div>
                    <div class="back-description" id="backDescription"></div>
                    <div class="back-result"      id="backResult"></div>
                    <div class="back-prompt">Nasabi ba ng bata? / Did the child name it?</div>
                    <div class="back-buttons">
                        <button class="btn-yes" id="btnYes" onclick="answer(true)">✅ Oo / Yes</button>
                        <button class="btn-no"  id="btnNo"  onclick="answer(false)">❌ Hindi / No</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="tap-hint" id="tapHint">Tap the face to flip and see the emotion name</div>
    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Answer all 5 faces to unlock Next →</div>

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
        <div class="modal-body">This will skip <strong>all 5 faces</strong> and move to the next question.<br><br>You can come back later.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-ok"     onclick="doSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
const items = [
    {
        emoji:       '😄',
        label:       'HAPPY',
        tagalog:     'Masaya',
        description: 'Big smile, bright eyes — something good happened!',
        bgColor:     '#FFFBEB',
        draw(ctx, w, h) {
            ctx.fillStyle = '#FFF9E6';
            ctx.fillRect(0, 0, w, h);

            ctx.save();
            ctx.translate(w/2, h/2 - h*0.05);
            for (let i = 0; i < 12; i++) {
                ctx.save();
                ctx.rotate((i / 12) * Math.PI * 2);
                ctx.fillStyle = 'rgba(255,220,50,0.18)';
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(-14, h * 0.55);
                ctx.lineTo(14, h * 0.55);
                ctx.closePath();
                ctx.fill();
                ctx.restore();
            }
            ctx.restore();

            const cx = w / 2, cy = h / 2 - h * 0.05;
            const r  = Math.min(w, h) * 0.32;

            ctx.fillStyle = 'rgba(0,0,0,0.07)';
            ctx.beginPath(); ctx.ellipse(cx + 4, cy + r + 6, r * 0.85, 10, 0, 0, Math.PI * 2); ctx.fill();

            ctx.fillStyle = '#FFD93D';
            ctx.strokeStyle = '#E6A800';
            ctx.lineWidth = 4;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.fill(); ctx.stroke();

            ctx.fillStyle = 'rgba(255,120,100,0.25)';
            ctx.beginPath(); ctx.ellipse(cx - r*0.45, cy + r*0.18, r*0.22, r*0.14, -0.2, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.ellipse(cx + r*0.45, cy + r*0.18, r*0.22, r*0.14,  0.2, 0, Math.PI*2); ctx.fill();

            ctx.strokeStyle = '#3D2B00';
            ctx.lineWidth   = 3.5;
            ctx.lineCap     = 'round';
            ctx.beginPath(); ctx.arc(cx - r*0.33, cy - r*0.12, r*0.14, Math.PI, 0); ctx.stroke();
            ctx.beginPath(); ctx.arc(cx + r*0.33, cy - r*0.12, r*0.14, Math.PI, 0); ctx.stroke();

            ctx.strokeStyle = '#3D2B00';
            ctx.lineWidth   = 4;
            ctx.beginPath();
            ctx.arc(cx, cy + r*0.08, r*0.46, 0.25, Math.PI - 0.25);
            ctx.stroke();

            ctx.fillStyle = '#fff';
            ctx.strokeStyle = '#E6A800';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.arc(cx, cy + r*0.08, r*0.46, 0.25, Math.PI - 0.25);
            ctx.lineTo(cx - r*0.42, cy + r*0.08);
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = 'rgba(255,255,255,0.55)';
            ctx.beginPath(); ctx.ellipse(cx - r*0.25, cy - r*0.35, r*0.13, r*0.07, -0.5, 0, Math.PI*2); ctx.fill();
        }
    },
    {
        emoji:       '😢',
        label:       'SAD',
        tagalog:     'Malungkot',
        description: 'Tears falling, frown, droopy eyes — feeling upset or unhappy.',
        bgColor:     '#EFF6FF',
        draw(ctx, w, h) {
            ctx.fillStyle = '#E8F4FD';
            ctx.fillRect(0, 0, w, h);

            ctx.strokeStyle = 'rgba(100,160,230,0.3)';
            ctx.lineWidth = 1.5;
            ctx.lineCap = 'round';
            for (let i = 0; i < 18; i++) {
                const rx = (i * 67 + 20) % w;
                const ry = (i * 41 + 30) % (h - 60);
                ctx.beginPath(); ctx.moveTo(rx, ry); ctx.lineTo(rx - 3, ry + 14); ctx.stroke();
            }

            ctx.fillStyle = '#B0C8E8';
            [[w/2, h*0.1, 26], [w/2-22, h*0.12, 18], [w/2+22, h*0.12, 18]].forEach(([x,y,r]) => {
                ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI*2); ctx.fill();
            });
            ctx.fillRect(w/2-40, h*0.12, 80, 16);

            const cx = w/2, cy = h/2 + h*0.02;
            const r  = Math.min(w,h)*0.3;

            ctx.fillStyle = 'rgba(0,0,0,0.07)';
            ctx.beginPath(); ctx.ellipse(cx+4, cy+r+6, r*0.85, 10, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#AED6F1';
            ctx.strokeStyle = '#5DADE2';
            ctx.lineWidth = 4;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.fill(); ctx.stroke();

            ctx.strokeStyle = '#1A5276';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.beginPath(); ctx.moveTo(cx-r*0.48, cy-r*0.28); ctx.lineTo(cx-r*0.18, cy-r*0.18); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(cx+r*0.48, cy-r*0.28); ctx.lineTo(cx+r*0.18, cy-r*0.18); ctx.stroke();

            ctx.fillStyle = '#1A5276';
            ctx.beginPath(); ctx.ellipse(cx-r*0.3, cy-r*0.08, r*0.1, r*0.12, 0, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.ellipse(cx+r*0.3, cy-r*0.08, r*0.1, r*0.12, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#5DADE2';
            [[cx-r*0.24, cy+r*0.1], [cx+r*0.32, cy+r*0.18]].forEach(([tx,ty]) => {
                ctx.beginPath();
                ctx.moveTo(tx, ty - 10);
                ctx.bezierCurveTo(tx-5, ty, tx-5, ty+8, tx, ty+10);
                ctx.bezierCurveTo(tx+5, ty+8, tx+5, ty, tx, ty-10);
                ctx.fill();
            });

            ctx.strokeStyle = '#1A5276';
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.arc(cx, cy + r*0.55, r*0.35, Math.PI + 0.3, -0.3);
            ctx.stroke();

            ctx.fillStyle = 'rgba(255,255,255,0.45)';
            ctx.beginPath(); ctx.ellipse(cx-r*0.25, cy-r*0.32, r*0.12, r*0.07, -0.5, 0, Math.PI*2); ctx.fill();
        }
    },
    {
        emoji:       '😠',
        label:       'ANGRY',
        tagalog:     'Galit',
        description: 'Furrowed brows, tight mouth — feeling mad or frustrated!',
        bgColor:     '#FFF5F5',
        draw(ctx, w, h) {
            ctx.fillStyle = '#FEF0F0';
            ctx.fillRect(0, 0, w, h);

            ctx.strokeStyle = 'rgba(220,50,50,0.18)';
            ctx.lineWidth = 2;
            const spikes = [[w*0.1,h*0.15],[w*0.85,h*0.12],[w*0.08,h*0.7],[w*0.9,h*0.65]];
            spikes.forEach(([sx,sy]) => {
                for (let i = 0; i < 6; i++) {
                    const angle = (i/6)*Math.PI*2;
                    ctx.beginPath();
                    ctx.moveTo(sx, sy);
                    ctx.lineTo(sx + Math.cos(angle)*22, sy + Math.sin(angle)*22);
                    ctx.stroke();
                }
            });

            const cx = w/2, cy = h/2;
            const r  = Math.min(w,h)*0.3;

            [0.1, 0.06, 0.03].forEach((alpha, i) => {
                ctx.fillStyle = `rgba(220,50,50,${alpha})`;
                ctx.beginPath(); ctx.arc(cx, cy, r + 18 + i*10, 0, Math.PI*2); ctx.fill();
            });

            ctx.fillStyle = 'rgba(0,0,0,0.08)';
            ctx.beginPath(); ctx.ellipse(cx+4, cy+r+6, r*0.85, 10, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#E74C3C';
            ctx.strokeStyle = '#C0392B';
            ctx.lineWidth = 4;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.fill(); ctx.stroke();

            ctx.strokeStyle = '#7B0000';
            ctx.lineWidth = 5;
            ctx.lineCap = 'round';
            ctx.beginPath(); ctx.moveTo(cx-r*0.52, cy-r*0.32); ctx.lineTo(cx-r*0.12, cy-r*0.18); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(cx+r*0.52, cy-r*0.32); ctx.lineTo(cx+r*0.12, cy-r*0.18); ctx.stroke();

            ctx.fillStyle = '#7B0000';
            ctx.beginPath(); ctx.ellipse(cx-r*0.3, cy-r*0.04, r*0.13, r*0.08, 0.2, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.ellipse(cx+r*0.3, cy-r*0.04, r*0.13, r*0.08,-0.2, 0, Math.PI*2); ctx.fill();

            ctx.strokeStyle = '#7B0000';
            ctx.lineWidth = 4;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            ctx.moveTo(cx - r*0.38, cy + r*0.38);
            ctx.lineTo(cx - r*0.18, cy + r*0.28);
            ctx.lineTo(cx,          cy + r*0.36);
            ctx.lineTo(cx + r*0.18, cy + r*0.28);
            ctx.lineTo(cx + r*0.38, cy + r*0.38);
            ctx.stroke();

            ctx.fillStyle = 'rgba(220,50,50,0.4)';
            [[cx-r*0.2, cy-r-12, 8],[cx+r*0.2, cy-r-10, 7],[cx, cy-r-20, 6]].forEach(([sx,sy,sr]) => {
                ctx.beginPath(); ctx.arc(sx, sy, sr, 0, Math.PI*2); ctx.fill();
            });

            ctx.fillStyle = 'rgba(255,255,255,0.3)';
            ctx.beginPath(); ctx.ellipse(cx-r*0.25, cy-r*0.32, r*0.12, r*0.07, -0.5, 0, Math.PI*2); ctx.fill();
        }
    },
    {
        emoji:       '😨',
        label:       'SCARED',
        tagalog:     'Takot',
        description: 'Wide eyes, open mouth, pale — something frightening happened!',
        draw(ctx, w, h) {
            ctx.fillStyle = '#1a1a2e';
            ctx.fillRect(0, 0, w, h);

            ctx.fillStyle = 'rgba(255,255,255,0.6)';
            [[w*0.1,h*0.08],[w*0.3,h*0.05],[w*0.55,h*0.09],[w*0.75,h*0.06],
             [w*0.88,h*0.14],[w*0.18,h*0.18],[w*0.65,h*0.18],[w*0.92,h*0.3]].forEach(([sx,sy]) => {
                ctx.beginPath(); ctx.arc(sx, sy, 2, 0, Math.PI*2); ctx.fill();
            });

            ctx.fillStyle = 'rgba(255,255,255,0.08)';
            ctx.beginPath(); ctx.arc(w*0.82, h*0.22, 28, Math.PI, 0); ctx.fill();
            ctx.fillRect(w*0.82-28, h*0.22, 56, 28);

            const cx = w/2, cy = h/2 + h*0.02;
            const r  = Math.min(w,h)*0.3;

            ctx.fillStyle = 'rgba(0,0,0,0.25)';
            ctx.beginPath(); ctx.ellipse(cx+4, cy+r+6, r*0.85, 10, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#D7BDE2';
            ctx.strokeStyle = '#7D3C98';
            ctx.lineWidth = 4;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.fill(); ctx.stroke();

            ctx.strokeStyle = '#4A235A';
            ctx.lineWidth = 3.5;
            ctx.lineCap = 'round';
            ctx.beginPath(); ctx.moveTo(cx-r*0.5, cy-r*0.3); ctx.quadraticCurveTo(cx-r*0.28, cy-r*0.46, cx-r*0.1, cy-r*0.28); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(cx+r*0.5, cy-r*0.3); ctx.quadraticCurveTo(cx+r*0.28, cy-r*0.46, cx+r*0.1, cy-r*0.28); ctx.stroke();

            ctx.fillStyle = '#fff';
            ctx.beginPath(); ctx.arc(cx-r*0.3, cy-r*0.08, r*0.18, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.3, cy-r*0.08, r*0.18, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#2C0040';
            ctx.beginPath(); ctx.arc(cx-r*0.3, cy-r*0.13, r*0.08, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.3, cy-r*0.13, r*0.08, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#fff';
            ctx.beginPath(); ctx.arc(cx-r*0.26, cy-r*0.17, r*0.03, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.34, cy-r*0.17, r*0.03, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#4A235A';
            ctx.beginPath(); ctx.ellipse(cx, cy+r*0.32, r*0.16, r*0.22, 0, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#1a0028';
            ctx.beginPath(); ctx.ellipse(cx, cy+r*0.34, r*0.11, r*0.17, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#AED6F1';
            ctx.beginPath();
            ctx.moveTo(cx+r*0.52, cy-r*0.25);
            ctx.bezierCurveTo(cx+r*0.56,cy-r*0.18, cx+r*0.56,cy-r*0.08, cx+r*0.52,cy-r*0.04);
            ctx.bezierCurveTo(cx+r*0.48,cy-r*0.08, cx+r*0.48,cy-r*0.18, cx+r*0.52,cy-r*0.25);
            ctx.fill();

            ctx.fillStyle = 'rgba(255,255,255,0.35)';
            ctx.beginPath(); ctx.ellipse(cx-r*0.25, cy-r*0.3, r*0.12, r*0.07, -0.5, 0, Math.PI*2); ctx.fill();
        }
    },
    {
        emoji:       '😲',
        label:       'SURPRISED',
        tagalog:     'Nagulat',
        description: 'Eyebrows way up, mouth wide open — something unexpected!',
        draw(ctx, w, h) {
            ctx.fillStyle = '#FFFDE7';
            ctx.fillRect(0, 0, w, h);

            ctx.save();
            ctx.translate(w/2, h/2);
            for (let i = 0; i < 16; i++) {
                const angle = (i/16)*Math.PI*2;
                const len   = (i % 2 === 0) ? h*0.48 : h*0.3;
                ctx.strokeStyle = `rgba(255,193,7,${i%2===0?0.22:0.12})`;
                ctx.lineWidth = 2;
                ctx.beginPath(); ctx.moveTo(0,0); ctx.lineTo(Math.cos(angle)*len, Math.sin(angle)*len); ctx.stroke();
            }
            ctx.restore();

            ctx.font = 'bold 28px sans-serif';
            ctx.fillStyle = 'rgba(255,152,0,0.5)';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('!', w*0.12, h*0.25);
            ctx.fillText('!', w*0.88, h*0.25);
            ctx.font = 'bold 20px sans-serif';
            ctx.fillText('?', w*0.15, h*0.65);
            ctx.fillText('?', w*0.85, h*0.65);

            const cx = w/2, cy = h/2 - h*0.02;
            const r  = Math.min(w,h)*0.3;

            ctx.fillStyle = 'rgba(0,0,0,0.07)';
            ctx.beginPath(); ctx.ellipse(cx+4, cy+r+6, r*0.85, 10, 0, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#FDEBD0';
            ctx.strokeStyle = '#E59866';
            ctx.lineWidth = 4;
            ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.fill(); ctx.stroke();

            ctx.strokeStyle = '#784212';
            ctx.lineWidth = 3.5;
            ctx.lineCap = 'round';
            ctx.beginPath(); ctx.moveTo(cx-r*0.48, cy-r*0.42); ctx.quadraticCurveTo(cx-r*0.28, cy-r*0.58, cx-r*0.08, cy-r*0.42); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(cx+r*0.48, cy-r*0.42); ctx.quadraticCurveTo(cx+r*0.28, cy-r*0.58, cx+r*0.08, cy-r*0.42); ctx.stroke();

            ctx.fillStyle = '#fff';
            ctx.beginPath(); ctx.arc(cx-r*0.3, cy-r*0.15, r*0.17, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.3, cy-r*0.15, r*0.17, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#1C2833';
            ctx.beginPath(); ctx.arc(cx-r*0.3, cy-r*0.15, r*0.09, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.3, cy-r*0.15, r*0.09, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#fff';
            ctx.beginPath(); ctx.arc(cx-r*0.26, cy-r*0.2, r*0.04, 0, Math.PI*2); ctx.fill();
            ctx.beginPath(); ctx.arc(cx+r*0.34, cy-r*0.2, r*0.04, 0, Math.PI*2); ctx.fill();

            ctx.fillStyle = '#784212';
            ctx.beginPath(); ctx.ellipse(cx, cy+r*0.28, r*0.2, r*0.26, 0, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#2C0A00';
            ctx.beginPath(); ctx.ellipse(cx, cy+r*0.3, r*0.15, r*0.2, 0, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = '#fff';
            ctx.beginPath(); ctx.ellipse(cx, cy+r*0.14, r*0.18, r*0.06, 0, 0, Math.PI); ctx.fill();

            ctx.fillStyle = 'rgba(255,255,255,0.5)';
            ctx.beginPath(); ctx.ellipse(cx-r*0.25, cy-r*0.32, r*0.12, r*0.07, -0.5, 0, Math.PI*2); ctx.fill();
        }
    },
];

const PASS_SCORE = 3;
const existing   = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked   = existing !== '';

let current = 0;
let answers = items.map(() => null);
let skipUrl = null;

function render() {
    const item     = items[current];
    const answered = answers[current] !== null;

    const canvas = document.getElementById('faceCanvas');
    const wrap   = document.getElementById('cardWrap');
    canvas.width  = wrap.offsetWidth  || 500;
    canvas.height = wrap.offsetHeight || 380;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    item.draw(ctx, canvas.width, canvas.height);

    document.getElementById('backEmoji').textContent       = item.emoji;
    document.getElementById('backAnswer').textContent      = `${item.label} / ${item.tagalog}`;
    document.getElementById('backDescription').textContent = item.description;

    const inner = document.getElementById('cardInner');
    inner.classList.remove('flipped', 'answered-correct', 'answered-wrong');
    document.getElementById('backResult').style.display = 'none';
    document.getElementById('backResult').textContent   = '';
    document.getElementById('btnYes').disabled = false;
    document.getElementById('btnNo').disabled  = false;

    if (answered) {
        inner.classList.add('flipped');
        inner.classList.add(answers[current] ? 'answered-correct' : 'answered-wrong');
        document.getElementById('backResult').textContent   = answers[current] ? '🌟' : '💪';
        document.getElementById('backResult').style.display = 'block';
        document.getElementById('btnYes').disabled = true;
        document.getElementById('btnNo').disabled  = true;
    }

    buildDots();

    const allDone   = answers.every(a => a !== null);
    const remaining = answers.filter(a => a === null).length;

    document.getElementById('btnNext').className      = (allDone || isLocked) ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('tapHint').textContent    = allDone
        ? 'All done! Click Next → to submit.'
        : 'Tap the face to flip and see the emotion name';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${remaining} face${remaining > 1 ? 's' : ''} left — answer all to unlock Next →`;
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

function answer(isCorrect) {
    if (answers[current] !== null || isLocked) return;

    answers[current] = isCorrect;
    document.getElementById('backResult').textContent   = isCorrect ? '🌟' : '💪';
    document.getElementById('backResult').style.display = 'block';
    document.getElementById('cardInner').classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');
    document.getElementById('btnYes').disabled = true;
    document.getElementById('btnNo').disabled  = true;

    buildDots();

    setTimeout(() => {
        document.getElementById('cardInner').classList.remove('flipped');
        setTimeout(() => {
            const next = answers.indexOf(null);
            if (next !== -1) current = next;
            render();
        }, 560);
    }, 900);

    const allDone = answers.every(a => a !== null);
    document.getElementById('btnNext').className      = allDone ? 'btn-nav' : 'btn-nav locked';
    document.getElementById('answerHint').textContent = allDone
        ? 'All done! Click Next → to submit.'
        : `${answers.filter(a => a === null).length} face(s) left`;
}

function clickNext() {
    if (isLocked)                      { openModal('lockedModal'); return; }
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

document.getElementById('cardWrap').addEventListener('click', function(e) {
    if (e.target.closest('.btn-yes') || e.target.closest('.btn-no')) return;
    if (answers[current] === null && !isLocked) {
        document.getElementById('cardInner').classList.toggle('flipped');
    }
});

if (isLocked) {
    document.getElementById('lockedBanner').style.display = 'block';
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('btnNext').className = 'btn-nav';
    answers = items.map(() => true);
}

window.addEventListener('resize', () => render());
render();
</script>
</body>
</html>