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

        .card {
            background: #fff;
            border-radius: 30px;
            padding: 40px 50px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        .progress {
            text-align: center;
            font-size: 14px;
            color: #7C3AED;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .domain-icon  { text-align: center; font-size: 48px; margin-bottom: 10px; }
        .domain-title { text-align: center; font-size: 28px; font-weight: 900; color: #1a1a2e; margin-bottom: 0.5rem; }
        .question-text {
            text-align: center;
            font-size: 18px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .game-box {
            background: #fffbea;
            border: 3px dashed #f5a623;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .game-title {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 900;
            color: #e07b00;
            margin-bottom: 0.5rem;
        }

        .game-subtitle {
            text-align: center;
            font-size: 0.85rem;
            color: #aaa;
            margin-bottom: 2rem;
        }

        .game-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .column-header {
            text-align: center;
            font-size: 0.9rem;
            font-weight: 800;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            margin-bottom: 1rem;
        }

        .pics-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .pic-card {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            border: 4px solid #e0e0e0;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: grab;
            transition: all 0.3s;
            user-select: none;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .pic-card:active { cursor: grabbing; }

        .pic-card:hover:not(.matched) {
            transform: scale(1.07) rotate(-2deg);
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            border-color: #f5a623;
        }

        .pic-card.dragging {
            opacity: 0.45;
            transform: scale(0.93);
        }

        .pic-card.drag-over {
            border-color: #7C3AED;
            background: #f0ebff;
            box-shadow: 0 0 0 4px #7C3AED44;
            transform: scale(1.08);
        }

        .pic-card.matched {
            border-color: #9E9E9E;
            background: #f5f5f5;
            cursor: default;
            transform: scale(1);
            animation: popIn 0.35s cubic-bezier(0.34,1.56,0.64,1);
        }

        .pic-card.matched::after {
            content: "✓";
            position: absolute;
            top: 6px;
            right: 10px;
            font-size: 1.3rem;
            color: #666;
            font-weight: 900;
        }

        .pic-emoji {
            font-size: 3.5rem;
            line-height: 1;
        }

        .pic-label {
            font-size: 0.85rem;
            font-weight: 800;
            color: #555;
            text-align: center;
        }

        @keyframes popIn {
            0%  { transform: scale(0.85); }
            60% { transform: scale(1.1); }
            100%{ transform: scale(1); }
        }

        .answer-hint {
            text-align: center;
            font-size: 0.85rem;
            color: #aaa;
            margin-bottom: 0.8rem;
        }

        .nav-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .nav-center { display: flex; gap: 10px; }

        .btn-nav {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid #ccc;
            cursor: pointer;
            background: #fff;
            color: #333;
            transition: all 0.2s;
        }

        .btn-nav:hover {
            background: #f5f5f5;
            transform: translateY(-2px);
        }

        .btn-prev {
            background: #f5f5f5;
            border-color: #999;
            color: #666;
        }

        .btn-prev:hover {
            background: #e0e0e0;
            color: #333;
        }

        @media (max-width: 768px) {
            .card { padding: 24px 16px; }
            .game-grid { grid-template-columns: 1fr; gap: 2rem; }
            .pic-card { width: 110px; height: 110px; }
            .pic-emoji { font-size: 2.8rem; }
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
        <div class="game-title">🧩 Match the Pictures!</div>
        <div class="game-subtitle">Drag each picture from the left to match its pair on the right!</div>

        <div class="game-grid">

            <div>
                <div class="column-header">👈 Drag from here</div>
                <div class="pics-container">

                    <div class="pic-card" draggable="true" data-pic="apple" id="left-apple">
                        <span class="pic-emoji">🍎</span>
                        <span class="pic-label">Apple</span>
                    </div>

                    <div class="pic-card" draggable="true" data-pic="banana" id="left-banana">
                        <span class="pic-emoji">🍌</span>
                        <span class="pic-label">Banana</span>
                    </div>

                    <div class="pic-card" draggable="true" data-pic="orange" id="left-orange">
                        <span class="pic-emoji">🍊</span>
                        <span class="pic-label">Orange</span>
                    </div>

                </div>
            </div>

            <div>
                <div class="column-header">Drop on match 👉</div>
                <div class="pics-container">

                    <div class="pic-card" data-pic="banana" id="right-banana">
                        <span class="pic-emoji">🍌</span>
                        <span class="pic-label">Banana</span>
                    </div>

                    <div class="pic-card" data-pic="orange" id="right-orange">
                        <span class="pic-emoji">🍊</span>
                        <span class="pic-label">Orange</span>
                    </div>

                    <div class="pic-card" data-pic="apple" id="right-apple">
                        <span class="pic-emoji">🍎</span>
                        <span class="pic-label">Apple</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <form method="POST"
          action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}"
          id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint">Match the pictures, then click Next to continue</div>

        <div class="nav-footer">

            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="button" onclick="submitAnswer()" class="btn-nav">
                    Next →
                </button>

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
let draggedItem = null;

// Store matches: { leftPic: rightPic }
const matches = {
    'apple': null,
    'banana': null,
    'orange': null
};

const leftItems = document.querySelectorAll('.game-grid > div:first-child .pic-card[draggable="true"]');
const rightItems = document.querySelectorAll('.game-grid > div:last-child .pic-card');

// Drag start
leftItems.forEach(item => {
    item.addEventListener('dragstart', function() {
        draggedItem = this;
        this.classList.add('dragging');
    });

    item.addEventListener('dragend', function() {
        this.classList.remove('dragging');
    });
});

// Drop targets - accept ANY drop
rightItems.forEach(item => {
    item.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (!this.classList.contains('matched')) {
            this.classList.add('drag-over');
        }
    });

    item.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });

    item.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');

        if (this.classList.contains('matched')) return;

        const draggedPic = draggedItem.dataset.pic;
        const dropPic = this.dataset.pic;

        // Allow ANY match (even incorrect ones)
        draggedItem.classList.add('matched');
        this.classList.add('matched');
        draggedItem.draggable = false;

        // Store the match
        matches[draggedPic] = dropPic;
    });
});

// Click / Tap for mobile
let selectedItem = null;

leftItems.forEach(item => {
    item.addEventListener('click', function() {
        if (this.classList.contains('matched')) return;
        if (selectedItem) selectedItem.style.outline = '';
        if (selectedItem === this) { selectedItem = null; return; }
        selectedItem = this;
        this.style.outline = '3px solid #7C3AED';
        this.style.outlineOffset = '3px';
    });
});

rightItems.forEach(item => {
    item.addEventListener('click', function() {
        if (!selectedItem || this.classList.contains('matched')) return;

        const draggedPic = selectedItem.dataset.pic;
        const dropPic = this.dataset.pic;

        selectedItem.classList.add('matched');
        this.classList.add('matched');
        selectedItem.draggable = false;
        selectedItem.style.outline = '';

        matches[draggedPic] = dropPic;
        selectedItem = null;
    });
});

// Check if all pictures are correctly matched
function checkMatches() {
    return matches['apple'] === 'apple' && 
           matches['banana'] === 'banana' && 
           matches['orange'] === 'orange';
}

// Submit answer based on match correctness
function submitAnswer() {
    const allMatched = Object.values(matches).every(v => v !== null);
    
    if (!allMatched) {
        alert('Please match all pictures before continuing!');
        return;
    }

    // Check if matches are correct
    const isCorrect = checkMatches();
    
    document.getElementById('responseInput').value = isCorrect ? 'yes' : 'no';
    document.getElementById('answerForm').submit();
}
</script>

</body>
</html>