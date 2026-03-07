<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>What's Wrong?!</title>
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
            max-width: 960px;
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
        .game-subtitle { text-align: center; font-size: 0.85rem; color: #aaa; margin-bottom: 1.2rem; }

        .mini-progress-wrap { display: flex; justify-content: center; gap: 8px; margin-bottom: 1.2rem; }
        .mini-dot {
            width: 12px; height: 12px; border-radius: 50%;
            background: #e2e8f0; border: 2px solid #ccc; transition: all 0.2s;
        }
        .mini-dot.done    { background: #38A169; border-color: #38A169; }
        .mini-dot.current { background: #7C3AED; border-color: #7C3AED; transform: scale(1.3); }

        .scene-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        .scene-label { font-size: 0.95rem; font-weight: 900; color: #7C3AED; }
        .chances-wrap { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 700; color: #666; }
        .chance-heart { font-size: 1.1rem; transition: all 0.3s; }
        .chance-heart.lost { filter: grayscale(1); opacity: 0.3; }

        /* ── THE SCENE ── */
        .scene-wrap {
            position: relative;
            width: 100%;
            height: 300px;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 3px solid rgba(0,0,0,0.1);
        }

        .scene-wrap.sky-bg    { background: linear-gradient(180deg, #87CEEB 0%, #b8e4f9 55%, #8BC34A 55%, #558B2F 100%); }
        .scene-wrap.ocean-bg  { background: linear-gradient(180deg, #87CEEB 0%, #cceeff 35%, #1565C0 35%, #0D47A1 100%); }
        .scene-wrap.garden-bg { background: linear-gradient(180deg, #fffde7 0%, #fff9c4 30%, #8BC34A 30%, #558B2F 100%); }
        .scene-wrap.room-bg   { background: linear-gradient(180deg, #ffe0cc 0%, #ffccbc 65%, #8D6E63 65%, #6D4C41 100%); }
        .scene-wrap.night-bg  { background: linear-gradient(180deg, #1a1a4e 0%, #2d2d7a 55%, #2d4a2d 55%, #1a3a1a 100%); }

        /* Non-interactive decorations */
        .deco {
            position: absolute;
            font-size: 2rem;
            user-select: none;
            pointer-events: none;
            opacity: 0.9;
            transform: translate(-50%, -50%);
        }

        /* Tappable objects */
        .obj-btn {
        position: absolute;
        transform: translate(-50%, -50%);
        background: transparent;
        border: none;
        border-radius: 50%;
        width: 90px;
        height: 90px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        box-shadow: none;
    }
        .obj-btn:hover:not(:disabled) {
            transform: translate(-50%, -50%) scale(1.2);
            background: transparent;
            box-shadow: none;
        }
        .obj-btn:disabled { cursor: default; }

        .obj-emoji { font-size: 52px; line-height: 1; }
        .obj-label { font-size: 0.58rem; font-weight: 900; color: #333; white-space: nowrap; }

        .obj-btn.correct {
            background: rgba(198,246,213,0.95);
            border-color: #38A169;
            box-shadow: 0 0 0 5px #38A169, 0 4px 12px rgba(0,0,0,0.2);
            animation: popIn 0.35s ease;
        }
        .obj-btn.wrong {
            background: rgba(254,215,215,0.95);
            border-color: #E53E3E;
            animation: shake 0.4s ease;
        }
        .obj-btn.reveal {
            background: rgba(198,246,213,0.95);
            border-color: #38A169;
            box-shadow: 0 0 0 5px #38A169;
        }

        @keyframes shake {
            0%,100% { transform: translate(-50%,-50%) translateX(0); }
            25%      { transform: translate(-50%,-50%) translateX(-8px); }
            75%      { transform: translate(-50%,-50%) translateX(8px); }
        }
        @keyframes popIn {
            0%   { transform: translate(-50%,-50%) scale(0.8); }
            60%  { transform: translate(-50%,-50%) scale(1.3); }
            100% { transform: translate(-50%,-50%) scale(1); }
        }

        .silly-badge {
            position: absolute;
            top: -8px; right: -8px;
            background: #E53E3E;
            color: #fff;
            font-size: 0.55rem;
            font-weight: 900;
            padding: 2px 5px;
            border-radius: 99px;
            white-space: nowrap;
        }

        .feedback { text-align: center; font-size: 1rem; font-weight: 900; min-height: 28px; margin-bottom: 0.4rem; }
        .feedback.correct { color: #38A169; }
        .feedback.wrong   { color: #E53E3E; }

        .tap-hint    { text-align: center; font-size: 0.82rem; color: #bbb; }
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
        .btn-nav.btn-locked { background: #e9e9e9; border-color: #ccc; color: #999; cursor: not-allowed; }
        .btn-nav.btn-locked:hover { transform: none; background: #e9e9e9; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; }
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
        .modal-icon   { font-size: 3rem; margin-bottom: 12px; }
        .modal-title  { font-size: 1.25rem; font-weight: 900; color: #1a1a2e; margin-bottom: 8px; }
        .modal-body   { font-size: 0.95rem; color: #666; line-height: 1.6; margin-bottom: 24px; }
        .modal-actions{ display: flex; gap: 12px; justify-content: center; }
        .btn-modal-ok     { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #7C3AED; color: #fff; border: none; cursor: pointer; }
        .btn-modal-ok:hover { background: #6d28d9; }
        .btn-modal-cancel { padding: 12px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; background: #fff; color: #555; border: 2px solid #ccc; cursor: pointer; }
        .btn-modal-cancel:hover { background: #f0f0f0; }

        @media (max-width: 600px) {
            .card { padding: 24px 16px; }
            .scene-wrap { height: 220px; }
            .obj-btn { width: 58px; height: 58px; }
            .obj-emoji { font-size: 26px; }
        }
    </style>
</head>
<body>
<div class="card">

    <div class="progress"><?php echo e($totalAnswered); ?> of <?php echo e($totalQuestions); ?> answered</div>

    <div class="domain-icon">
        <?php
            $icons = [
                'Gross Motor'         => '⚡',
                'Fine Motor'          => '✋',
                'Self-Help'           => '🎯',
                'Receptive Language'  => '👂',
                'Expressive Language' => '💬',
                'Cognitive'           => '🧠',
                'Social-Emotional'    => '❤️',
            ];
        ?>
        <?php echo e($icons[$currentDomain->domain_name] ?? '📋'); ?>

    </div>

    <div class="domain-title"><?php echo e($currentDomain->domain_name); ?></div>
    <div class="question-text"><?php echo e($question->display_text ?? $question->text); ?></div>

    <div class="game-box" id="gameBox">

        <div class="locked-banner" id="lockedBanner">
            🔒 This question has already been answered and cannot be changed.
        </div>

        <div class="game-title">🤔 What's Wrong?!</div>
        <div class="game-subtitle">Tap the silly or wrong thing in the picture! You have 2 chances.</div>

        <div class="mini-progress-wrap" id="miniProgress"></div>

        <div class="scene-header">
            <div class="scene-label" id="sceneLabel"></div>
            <div class="chances-wrap" id="chancesWrap">
                Chances:
                <span class="chance-heart" id="heart1">❤️</span>
                <span class="chance-heart" id="heart2">❤️</span>
            </div>
        </div>

        <div class="scene-wrap" id="sceneWrap"></div>

        <div class="feedback" id="feedback"></div>
        <div class="tap-hint" id="tapHint">Tap what doesn't belong!</div>
    </div>

    <form method="POST"
          action="<?php echo e(route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex])); ?>"
          id="answerForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint" id="answerHint">Find what's wrong in all 5 scenes to unlock Next →</div>

        <div class="nav-footer">
            <?php if($prevDomain && $prevIndex): ?>
                <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex])); ?>"
                   class="btn-nav btn-prev">← Previous</a>
            <?php else: ?>
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            <?php endif; ?>

            <div class="nav-center">
                <button type="button" id="btnNext" onclick="handleNext()" class="btn-nav btn-locked">Next →</button>

                <?php if($nextDomain && $nextIndex): ?>
                    <a href="<?php echo e(route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex])); ?>"
                       id="btnSkip" onclick="handleSkip(event)" class="btn-nav">Skip (Answer Later)</a>
                <?php else: ?>
                    <a href="<?php echo e(route('family.tests.result', $testId)); ?>" class="btn-nav">Review →</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Confirm -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <div class="modal-title">Submit Answer?</div>
        <div class="modal-body">Next means submitting your answer and not returning to it.<br><br>Are you sure?</div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSubmit()">Yes, Submit</button>
        </div>
    </div>
</div>

<!-- Locked -->
<div class="modal-overlay" id="lockedModal">
    <div class="modal-box">
        <div class="modal-icon">🔒</div>
        <div class="modal-title">Answer Already Submitted</div>
        <div class="modal-body">Your previous answer has been saved and is now locked.<br><br>Click Next to continue.</div>
        <div class="modal-actions">
            <button class="btn-modal-ok" onclick="closeModal('lockedModal')">Got it!</button>
        </div>
    </div>
</div>

<!-- Skip -->
<div class="modal-overlay" id="skipModal">
    <div class="modal-box">
        <div class="modal-icon">⏭️</div>
        <div class="modal-title">Skip This Question?</div>
        <div class="modal-body">Skipping will leave this question unanswered.<br><br>You can come back to it later.</div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" onclick="closeModal('skipModal')">Cancel</button>
            <button class="btn-modal-ok"     onclick="confirmSkip()">Yes, Skip</button>
        </div>
    </div>
</div>

<script>
// ── top/left are percentages inside the scene box ──
// ── deco = non-tappable decorations, objects = tappable ──
const scenes = [
    {
        label:   '🌊 Ocean Scene',
        bgClass: 'ocean-bg',
        deco: [
            { emoji: '☁️', top: 12, left: 15 },
            { emoji: '☁️', top:  8, left: 55 },
            { emoji: '🌞', top: 10, left: 85 },
            { emoji: '🌊', top: 45, left: 5  },
            { emoji: '🌊', top: 50, left: 45 },
            { emoji: '🌊', top: 42, left: 80 },
        ],
        objects: [
            { emoji: '🐠',  label: 'Fish',    top: 65, left: 12 },
            { emoji: '🐙',  label: 'Octopus', top: 75, left: 32 },
            { emoji: '🔥',  label: 'Fire',    top: 58, left: 55 }, // ← WRONG: fire underwater!
            { emoji: '🐚',  label: 'Shell',   top: 78, left: 72 },
            { emoji: '🐋',  label: 'Whale',   top: 60, left: 85 },
        ],
        wrongIndex: 2,
    },
    {
        label:   '🌳 Forest Scene',
        bgClass: 'sky-bg',
        deco: [
            { emoji: '🌳', top: 35, left:  8 },
            { emoji: '🌲', top: 30, left: 70 },
            { emoji: '🌿', top: 72, left: 25 },
            { emoji: '🌿', top: 70, left: 58 },
            { emoji: '☁️', top:  8, left: 38 },
            { emoji: '🌞', top:  8, left: 80 },
        ],
        objects: [
            { emoji: '🐦',  label: 'Bird',     top: 18, left: 25 },
            { emoji: '🍄',  label: 'Mushroom', top: 68, left: 12 },
            { emoji: '🚀',  label: 'Rocket',   top: 12, left: 52 }, // ← WRONG: rocket in a forest!
            { emoji: '🐿️', label: 'Squirrel', top: 62, left: 45 },
            { emoji: '🍃',  label: 'Leaf',     top: 68, left: 78 },
        ],
        wrongIndex: 2,
    },
    {
        label:   '☁️ Sky Scene',
        bgClass: 'sky-bg',
        deco: [
            { emoji: '☁️', top: 10, left: 10 },
            { emoji: '☁️', top: 22, left: 52 },
            { emoji: '☁️', top:  8, left: 78 },
            { emoji: '🌈', top: 35, left:  5 },
            { emoji: '🌞', top:  8, left: 45 },
        ],
        objects: [
            { emoji: '✈️', label: 'Airplane', top: 18, left: 18 },
            { emoji: '🦅', label: 'Eagle',    top: 28, left: 42 },
            { emoji: '🎈', label: 'Balloon',  top: 20, left: 72 },
            { emoji: '🦜', label: 'Parrot',   top: 42, left: 85 },
            { emoji: '🐟', label: 'Fish',     top: 55, left: 35 }, // ← WRONG: fish in the sky!
        ],
        wrongIndex: 4,
    },
    {
        label:   '🏡 Living Room',
        bgClass: 'room-bg',
        deco: [
            { emoji: '🖼️', top: 12, left: 12 },
            { emoji: '🪟',  top: 12, left: 58 },
            { emoji: '🪴',  top: 55, left: 85 },
            { emoji: '💡',  top: 10, left: 35 },
        ],
        objects: [
            { emoji: '🛋️', label: 'Sofa',    top: 62, left: 12 },
            { emoji: '📺',  label: 'TV',      top: 25, left: 38 },
            { emoji: '🌋',  label: 'Volcano', top: 35, left: 65 }, // ← WRONG: volcano in a living room!
            { emoji: '🚪',  label: 'Door',    top: 38, left: 85 },
            { emoji: '🧸',  label: 'Teddy',   top: 65, left: 52 },
        ],
        wrongIndex: 2,
    },
    {
        label:   '🌻 Garden Scene',
        bgClass: 'garden-bg',
        deco: [
            { emoji: '🌻', top: 48, left:  8 },
            { emoji: '🌸', top: 55, left: 28 },
            { emoji: '🌻', top: 45, left: 78 },
            { emoji: '🌞', top:  8, left: 85 },
            { emoji: '☁️', top:  8, left: 22 },
            { emoji: '🌱', top: 70, left: 60 },
        ],
        objects: [
            { emoji: '🦋', label: 'Butterfly', top: 22, left: 15 },
            { emoji: '🐝', label: 'Bee',       top: 32, left: 42 },
            { emoji: '🌷', label: 'Tulip',     top: 52, left: 55 },
            { emoji: '🧊', label: 'Ice Block', top: 62, left: 18 }, // ← WRONG: ice in a sunny garden!
            { emoji: '🐛', label: 'Worm',      top: 70, left: 78 },
        ],
        wrongIndex: 3,
    },
];

const PASS_SCORE  = 3;
const MAX_CHANCES = 2;

const existingResponse = '<?php echo addslashes($existingResponse ?? ''); ?>';
const isLocked = existingResponse !== '';

let current   = 0;
let scores    = new Array(scenes.length).fill(null);
let chances   = MAX_CHANCES;
let roundDone = false;
let skipTarget = null;

function render() {
    const scene = scenes[current];
    document.getElementById('sceneLabel').textContent = `${scene.label} — Scene ${current + 1} of ${scenes.length}`;

    // Mini dots
    const dotsWrap = document.getElementById('miniProgress');
    dotsWrap.innerHTML = '';
    scenes.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = 'mini-dot';
        if (scores[i] !== null) dot.classList.add('done');
        if (i === current)      dot.classList.add('current');
        dotsWrap.appendChild(dot);
    });

    chances   = MAX_CHANCES;
    roundDone = scores[current] !== null;

    document.getElementById('feedback').textContent = '';
    document.getElementById('feedback').className   = 'feedback';

    updateHearts();
    buildScene();
    updateHints();
}

function buildScene() {
    const scene = scenes[current];
    const wrap  = document.getElementById('sceneWrap');
    wrap.innerHTML = '';
    wrap.className = 'scene-wrap ' + scene.bgClass;

    // Decorative non-tappable elements
    scene.deco.forEach(d => {
        const el = document.createElement('div');
        el.className   = 'deco';
        el.textContent = d.emoji;
        el.style.top   = d.top  + '%';
        el.style.left  = d.left + '%';
        wrap.appendChild(el);
    });

    // Tappable objects
    scene.objects.forEach((obj, i) => {
        const btn = document.createElement('button');
        btn.className = 'obj-btn';
        btn.style.top  = obj.top  + '%';
        btn.style.left = obj.left + '%';
        btn.disabled   = roundDone || isLocked;
        btn.innerHTML  = `<span class="obj-emoji">${obj.emoji}</span><span class="obj-label">${obj.label}</span>`;
        btn.onclick    = () => tapObject(i);

        if ((roundDone || isLocked) && i === scene.wrongIndex) {
            btn.classList.add('reveal');
            const badge = document.createElement('span');
            badge.className   = 'silly-badge';
            badge.textContent = 'SILLY!';
            btn.appendChild(badge);
        }

        wrap.appendChild(btn);
    });
}

function tapObject(index) {
    if (roundDone || isLocked) return;

    const scene   = scenes[current];
    const buttons = document.getElementById('sceneWrap').querySelectorAll('.obj-btn');
    const fb      = document.getElementById('feedback');

    if (index === scene.wrongIndex) {
        buttons[index].classList.add('correct');
        scores[current] = true;
        fb.textContent  = '🎉 That\'s right! Great job!';
        fb.className    = 'feedback correct';
        roundDone       = true;
        disableAll();
        updateHints();
        setTimeout(nextScene, 1300);
    } else {
        buttons[index].classList.add('wrong');
        chances--;
        updateHearts();
        setTimeout(() => buttons[index].classList.remove('wrong'), 500);

        if (chances <= 0) {
            scores[current] = false;
            fb.textContent  = `😅 The silly one was the ${scene.objects[scene.wrongIndex].label}!`;
            fb.className    = 'feedback wrong';
            buttons[scene.wrongIndex].classList.add('reveal');
            const badge = document.createElement('span');
            badge.className   = 'silly-badge';
            badge.textContent = 'SILLY!';
            buttons[scene.wrongIndex].appendChild(badge);
            roundDone = true;
            disableAll();
            updateHints();
            setTimeout(nextScene, 2000);
        } else {
            fb.textContent = `❌ Try again! ${chances} chance${chances > 1 ? 's' : ''} left.`;
            fb.className   = 'feedback wrong';
        }
    }
}

function nextScene() {
    document.getElementById('feedback').textContent = '';
    document.getElementById('feedback').className   = 'feedback';
    const next = scores.indexOf(null);
    if (next !== -1) { current = next; render(); }
    else updateHints();
}

function disableAll() {
    document.getElementById('sceneWrap').querySelectorAll('.obj-btn').forEach(b => b.disabled = true);
}

function updateHearts() {
    document.getElementById('heart1').className = 'chance-heart' + (chances < 2 ? ' lost' : '');
    document.getElementById('heart2').className = 'chance-heart' + (chances < 1 ? ' lost' : '');
}

function updateHints() {
    const allDone   = scores.every(s => s !== null);
    const remaining = scores.filter(s => s === null).length;
    document.getElementById('btnNext').className      = (allDone || isLocked) ? 'btn-nav' : 'btn-nav btn-locked';
    document.getElementById('answerHint').textContent = allDone ? 'All done! Click Next → to submit.' : `${remaining} scene${remaining > 1 ? 's' : ''} left to go`;
    document.getElementById('tapHint').textContent    = allDone ? 'All scenes done! Click Next →' : 'Tap what doesn\'t belong!';
}

const nextUrl = "<?php echo e($nextDomain && $nextIndex ? route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) : route('family.tests.result', $testId)); ?>";

function handleNext() {
    if (isLocked) {
        window.location.href = nextUrl;
        return;
    }
    if (scores.some(s => s === null)) return;
    document.getElementById('confirmModal').classList.add('show');
}

function confirmSubmit() {
    closeModal('confirmModal');
    const correct = scores.filter(s => s === true).length;
    document.getElementById('responseInput').value = correct >= PASS_SCORE ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}

function handleSkip(event) {
    event.preventDefault();
    skipTarget = event.currentTarget.href;
    document.getElementById('skipModal').classList.add('show');
}
function confirmSkip() { closeModal('skipModal'); window.location.href = skipTarget; }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

if (isLocked) {
    document.getElementById('lockedBanner').classList.add('visible');
    document.getElementById('gameBox').classList.add('locked');
    document.getElementById('chancesWrap').style.display = 'none';
    document.getElementById('btnNext').className = 'btn-nav';
    scores = new Array(scenes.length).fill(true);
}

render();
</script>
</body>
</html><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\family\C-20-Whats-wrong-pic.blade.php ENDPATH**/ ?>