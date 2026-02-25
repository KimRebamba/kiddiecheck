<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match the Colors!</title>
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
            text-align: center; font-size: 18px; color: #555;
            line-height: 1.6; margin-bottom: 1.5rem;
        }

        /* ── Game Box ── */
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

        /* ── Game Grid ── */
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

        .crayons-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* ── Crayon Card ── */
        .crayon-card {
            border-radius: 16px;
            border: 4px solid #e0e0e0;
            background: #fff;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            user-select: none;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .crayon-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        }

        .crayon-card.dragging {
            opacity: 0.5;
        }

        .crayon-card.drag-over {
            border-color: #7C3AED;
            background: #f0ebff;
            box-shadow: 0 0 0 4px #7C3AED;
        }

        .crayon-card.matched {
            border-color: #4CAF50;
            background: #f0fff4;
            box-shadow: 0 0 0 4px #4CAF50;
            cursor: default;
        }

        .crayon-card.matched::after {
            content: "✓";
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 1.5rem;
            color: #4CAF50;
            font-weight: 900;
        }

        .crayon-inner {
            width: 100%;
            height: 50px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1rem;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            box-shadow: inset 0 -3px 8px rgba(0,0,0,0.2);
        }

        /* Color classes */
        .red    { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .blue   { background: linear-gradient(135deg, #3498db, #2980b9); }
        .yellow { background: linear-gradient(135deg, #f1c40f, #f39c12); }

        /* ── Success Message ── */
        .success-message {
            display: none;
            text-align: center;
            font-size: 1.3rem;
            color: #4CAF50;
            font-weight: 900;
            margin-bottom: 1rem;
            padding: 1.2rem;
            background: #f0fff4;
            border-radius: 15px;
            border: 3px solid #4CAF50;
            animation: bounce 0.5s;
        }

        .success-message.show {
            display: block;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* ── Answer Hint ── */
        .answer-hint {
            text-align: center;
            font-size: 0.85rem;
            color: #aaa;
            margin-bottom: 0.8rem;
        }

        /* ── Nav ── */
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

        .btn-nav.hidden { visibility: hidden; }

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
        }
    </style>
</head>
<body>
<div class="card">

    <div class="progress">{{ $totalAnswered }} of {{ $totalQuestions }} answered</div>

    <div class="domain-icon">🧠</div>
    <div class="domain-title">{{ $currentDomain->domain_name }}</div>
    <div class="question-text">{{ $question->display_text ?? $question->text }}</div>

    {{-- ── Game ── --}}
    <div class="game-box">
        <div class="game-title">🎨 Match the Colors!</div>
        <div class="game-subtitle">Drag each crayon on the left to match its color on the right!</div>

        <div class="success-message" id="successMsg">
            🎉 Perfect! All colors matched! Auto-submitting YES...
        </div>

        <div class="game-grid">

            {{-- Left Column (Draggable) --}}
            <div>
                <div class="column-header">👈 Drag from here</div>
                <div class="crayons-container">
                    <div class="crayon-card" draggable="true" data-color="red">
                        <div class="crayon-inner red">🖍️ Red</div>
                    </div>

                    <div class="crayon-card" draggable="true" data-color="blue">
                        <div class="crayon-inner blue">🖍️ Blue</div>
                    </div>

                    <div class="crayon-card" draggable="true" data-color="yellow">
                        <div class="crayon-inner yellow">🖍️ Yellow</div>
                    </div>
                </div>
            </div>

            {{-- Right Column (Drop Zones - Shuffled) --}}
            <div>
                <div class="column-header">Drop on match 👉</div>
                <div class="crayons-container">
                    <div class="crayon-card" data-color="blue">
                        <div class="crayon-inner blue">🖍️ Blue</div>
                    </div>

                    <div class="crayon-card" data-color="yellow">
                        <div class="crayon-inner yellow">🖍️ Yellow</div>
                    </div>

                    <div class="crayon-card" data-color="red">
                        <div class="crayon-inner red">🖍️ Red</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ── Answer Form ── --}}
    <form method="POST" action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}" id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="">

        <div class="answer-hint">Match all colors to automatically submit YES, or click Next/Skip</div>

        <div class="nav-footer">

            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}"
                   class="btn-nav btn-prev">← Previous</a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="button" onclick="submitNo()" class="btn-nav">
                    Next → (Submit NO)
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
let matchedCount = 0;
const totalMatches = 3; // red, blue, yellow

// Get all draggable items
const leftItems = document.querySelectorAll('.game-grid > div:first-child .crayon-card[draggable="true"]');
const rightItems = document.querySelectorAll('.game-grid > div:last-child .crayon-card');

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

// Drop targets
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

        const draggedColor = draggedItem.dataset.color;
        const dropColor = this.dataset.color;

        if (draggedColor === dropColor) {
            // Correct match!
            draggedItem.classList.add('matched');
            this.classList.add('matched');
            draggedItem.draggable = false;
            matchedCount++;

            // Check if all matched
            if (matchedCount === totalMatches) {
                document.getElementById('successMsg').classList.add('show');
                
                // Auto-submit YES after 2 seconds
                setTimeout(function() {
                    document.getElementById('responseInput').value = 'yes';
                    document.getElementById('answerForm').submit();
                }, 2000);
            }
        }
    });
});

// Submit NO function
function submitNo() {
    if (matchedCount === totalMatches) {
        // Already completed, submit YES
        document.getElementById('responseInput').value = 'yes';
    } else {
        // Not completed, submit NO
        document.getElementById('responseInput').value = 'no';
    }
    document.getElementById('answerForm').submit();
}
</script>

</body>
</html>