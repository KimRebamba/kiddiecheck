<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #FFE66D;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        h1 {
            color: #2D3142;
            text-align: center;
            margin-bottom: 1rem;
        }

        .student-info {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .progress-summary {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #A770EF 0%, #CF8BF3 100%);
            color: white;
            border-radius: 20px;
        }

        .warning {
            background: #FF6B6B;
            color: white;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background: #6BCF7F;
            color: white;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .score-card {
            background: linear-gradient(135deg, #A770EF 0%, #CF8BF3 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 20px;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .score-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(167, 112, 239, 0.4);
        }

        .score-card.incomplete {
            background: linear-gradient(135deg, #FFA500 0%, #FFB733 100%);
            border: 3px solid #FF8C00;
        }

        .score-card.complete {
            background: linear-gradient(135deg, #6BCF7F 0%, #8FE3A0 100%);
        }

        .score-card h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .score-number {
            font-size: 2rem;
            font-weight: 900;
            margin: 0.5rem 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            font-weight: bold;
        }

        .badge-incomplete {
            background: rgba(255, 255, 255, 0.3);
        }

        .badge-complete {
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-answer-domain {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1.5rem;
            background: white;
            color: #2D3142;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-answer-domain:hover {
            background: #2D3142;
            color: white;
            transform: scale(1.05);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 3rem;
            border: none;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-submit {
            background: #6BCF7F;
            color: white;
        }

        .btn-submit:hover {
            background: #5AB86F;
            transform: translateY(-2px);
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-continue {
            background: #2D3142;
            color: white;
        }

        .btn-continue:hover {
            background: #1A1D2E;
            transform: translateY(-2px);
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 5px;
        }

        .legend-complete { background: #6BCF7F; }
        .legend-incomplete { background: #FFA500; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Test Review Dashboard</h1>
        <p class="student-info">Student: <strong><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></strong></p>

        <div class="progress-summary">
            <?php echo e($answeredCount); ?> / <?php echo e($totalQuestions); ?> Questions Answered
        </div>

        <?php if(!$canSubmit): ?>
            <div class="warning">
                ⚠️ You must answer ALL questions before submitting the test!
                <br><strong><?php echo e(count($unansweredQuestions)); ?> questions remaining</strong>
            </div>
        <?php else: ?>
            <div class="success">
                ✅ All questions answered! You can now submit the test.
            </div>
        <?php endif; ?>

        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color legend-complete"></div>
                <span>Complete</span>
            </div>
            <div class="legend-item">
                <div class="legend-color legend-incomplete"></div>
                <span>Incomplete (click to answer)</span>
            </div>
        </div>

        <!-- Domain Cards -->
        <div class="score-grid">
            <?php $__currentLoopData = $domainScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isComplete = $score['answered'] === $score['total'];
                    $unansweredCount = $score['total'] - $score['answered'];
                ?>
                
                <div class="score-card <?php echo e($isComplete ? 'complete' : 'incomplete'); ?>">
                    <h3><?php echo e($score['icon']); ?> <?php echo e($score['domain']); ?></h3>
                    <div class="score-number"><?php echo e($score['answered']); ?>/<?php echo e($score['total']); ?></div>
                    
                    <?php if($isComplete): ?>
                        <div class="status-badge badge-complete">✓ Complete</div>
                        <p style="font-size: 0.9rem; margin-top: 0.5rem;"><?php echo e($score['yes_count']); ?> YES</p>
                    <?php else: ?>
                        <div class="status-badge badge-incomplete"><?php echo e($unansweredCount); ?> unanswered</div>
                        <a href="<?php echo e(route('test.first-unanswered-in-domain', [$test->test_id, $score['domain_id']])); ?>" 
                           class="btn-answer-domain">
                            Answer Questions →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?php echo e(route('test.start', $test->test_id)); ?>" class="btn btn-continue">
                Continue Answering
            </a>
            
            <?php if($canSubmit): ?>
                <form action="<?php echo e(route('test.submit-test', $test->test_id)); ?>" method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Are you sure you want to submit? You cannot change answers after submission.');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-submit">
                        ✅ Submit Test
                    </button>
                </form>
            <?php else: ?>
                <button type="button" class="btn btn-submit" disabled title="Answer all questions first">
                    ✅ Submit Test (<?php echo e(count($unansweredQuestions)); ?> remaining)
                </button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views/test/review.blade.php ENDPATH**/ ?>