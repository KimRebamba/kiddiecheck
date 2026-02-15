<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECCD Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, sans-serif;
            background: #FFE66D;
            background-image: 
                repeating-linear-gradient(0deg, transparent, transparent 49px, #F4B740 49px, #F4B740 50px),
                repeating-linear-gradient(90deg, transparent, transparent 49px, #F4B740 49px, #F4B740 50px);
            background-size: 50px 50px;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 3rem;
            border: 3px solid #2D3142;
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: #ddd;
            z-index: 1000;
        }

        .progress-fill {
            height: 100%;
            background: #A770EF;
            width: <?php echo e($progressPercentage); ?>%;
            transition: width 0.3s ease;
        }

        .counter {
            text-align: center;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .answered-info {
            text-align: center;
            color: #A770EF;
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .domain-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .icon { font-size: 3rem; }
        .title { font-size: 2rem; font-weight: bold; color: #2D3142; }

        .question {
            font-size: 1.3rem;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 2rem;
            font-size: 2rem;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            color: white;
        }

        .btn-yes { background: #6BCF7F; }
        .btn-no { background: #FF6B6B; }
        .btn:hover { opacity: 0.9; transform: scale(1.02); }

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 15px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .nav a, .nav button {
            padding: 0.7rem 1.5rem;
            background: white;
            border: 2px solid #2D3142;
            border-radius: 10px;
            text-decoration: none;
            color: #2D3142;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        .nav a:hover, .nav button:hover { background: #2D3142; color: white; }
        .disabled { opacity: 0.3; pointer-events: none; }
        
        .btn-review {
            background: #FFA500 !important;
            color: white !important;
            border-color: #FFA500 !important;
        }
        
        .btn-review:hover {
            background: #FF8C00 !important;
        }
    </style>
</head>
<body>
    <div class="progress-bar"><div class="progress-fill"></div></div>

    <div class="container">
        <div class="counter">Question <?php echo e($currentQuestionNumber); ?> of <?php echo e($totalQuestions); ?></div>
        <div class="answered-info"><?php echo e($answeredCount); ?> of <?php echo e($totalQuestions); ?> answered</div>
        
        <div class="domain-header">
            <div class="icon"><?php echo e($domainIcon); ?></div>
            <div class="title"><?php echo e($currentDomain->name); ?></div>
        </div>

        <p class="question"><?php echo e($currentQuestion->display_text ?? $currentQuestion->text); ?></p>

        <form action="<?php echo e(route('test.submit-answer', $test->test_id)); ?>" method="POST" id="form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="question_id" value="<?php echo e($currentQuestion->question_id); ?>">
            <input type="hidden" name="response" id="response">

            <div class="buttons">
                <button type="button" class="btn btn-yes" onclick="submitAnswer('yes')">YES</button>
                <button type="button" class="btn btn-no" onclick="submitAnswer('no')">NO</button>
            </div>

            <textarea name="notes" placeholder="Add comment (optional)"><?php echo e($existingResponse->notes ?? ''); ?></textarea>

            <div class="nav">
                <?php if($previousQuestionId): ?>
                    <a href="<?php echo e(route('test.question', [$test->test_id, $previousQuestionId])); ?>">← Previous</a>
                <?php else: ?>
                    <span class="nav a disabled">← Previous</span>
                <?php endif; ?>

                <?php if($isLastQuestion): ?>
                    <a href="<?php echo e(route('test.review', $test->test_id)); ?>" class="btn-review">Review & Submit →</a>
                <?php elseif($nextQuestionId): ?>
                    <a href="<?php echo e(route('test.question', [$test->test_id, $nextQuestionId])); ?>">Skip / Next →</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        function submitAnswer(answer) {
            document.getElementById('response').value = answer;
            document.getElementById('form').submit();
        }
    </script>
</body>
</html><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views/test/question.blade.php ENDPATH**/ ?>