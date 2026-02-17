

<?php $__env->startSection('title', 'Family Dashboard - ECCD Checklist'); ?>

<?php $__env->startSection('content'); ?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: white;
        min-height: 100vh;
        padding: 2rem;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Welcome Card */
    .welcome-card {
        background: linear-gradient(135deg, #FFE66D 0%, #FFD93D 100%);
        border-radius: 25px;
        padding: 2.5rem 3rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        height: 240px;
    }

    .welcome-content h1 {
        font-size: 2.8rem;
        font-weight: 900;
        color: #2D3142;
        margin-bottom: 0.8rem;
        line-height: 1.2;
    }

    .welcome-subtitle {
        font-size: 1.05rem;
        color: #2D3142;
        font-weight: 400;
        line-height: 1.5;
    }

    .welcome-graphic {
        width: 200px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .graphic-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* New Grid Layout - Top Row (Welcome + Children) */
    .top-row {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    /* Bottom Row Grid Layout */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
        align-items: start;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.2rem;
        gap: 0.5rem;
    }

    .section-icon {
        font-size: 1.5rem;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 900;
        color: #2D3142;
        text-shadow: none;
    }

    /* Test Result, Assessment and Notification Section Containers */
    .test-result-section,
    .assessments-section,
    .notifications-section {
        background: white;
        border-radius: 25px;
        padding: 2rem 2.5rem;
        height: 520px;
        overflow-y: auto;
    }

    /* Test Result Card */
    .test-result-card {
        background: linear-gradient(135deg, #A770EF 0%, #CF8BF3 100%);
        border-radius: 25px;
        padding: 2rem 2.5rem;
        color: white;
        height: 380px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 2rem;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .test-result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(167, 112, 239, 0.4);
    }

    .test-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .test-avatar {
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 50%;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .test-name-section {
        text-align: left;
    }

    .test-child-name {
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 0.2rem;
    }

    .test-section {
        font-size: 1rem;
        opacity: 0.9;
    }

    .test-score-area {
        text-align: center;
    }

    .test-score {
        font-size: 4.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        line-height: 1;
    }

    .test-interpretation {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .test-date {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    /* Quick Action Buttons */
    .action-btn {
        display: inline-block;
        padding: 0;
        background: none;
        border: none;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        margin-top: 1rem;
        opacity: 0.9;
    }

    .action-btn:hover {
        opacity: 1;
        text-decoration: underline;
        transform: translateX(3px);
    }

    .action-btn-assessment {
        background: none;
        border: none;
        padding: 0;
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
        margin-top: 0.8rem;
        text-decoration: none;
        opacity: 0.95;
    }

    .action-btn-assessment:hover {
        opacity: 1;
        text-decoration: underline;
        transform: translateX(3px);
    }

    /* Progress Bar */
    .progress-container {
        margin-top: 0.5rem;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: white;
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 0.75rem;
        margin-top: 0.3rem;
        opacity: 0.9;
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 0.5rem;
    }

    .status-badge.upcoming {
        background: #6BCF7F;
        color: white;
    }

    .status-badge.in-progress {
        background: #FFD93D;
        color: #2D3142;
    }

    .status-badge.overdue {
        background: #FF6B6B;
        color: white;
    }

    /* Color-Coded Scores */
    .score-very-superior {
        color: #FFD93D !important;
    }

    .score-superior {
        color: #6BCF7F !important;
    }

    .score-high-average {
        color: #A0E7FF !important;
    }

    .score-average {
        color: white !important;
    }

    .score-low-average {
        color: #FFB366 !important;
    }

    .score-borderline {
        color: #FF9A9A !important;
    }

    /* Assessment Items */
    .assessment-item {
        margin-bottom: 1rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #FF6B9D 0%, #FFA3C1 100%);
        border-radius: 20px;
        color: white;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .assessment-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 107, 157, 0.3);
    }

    .assessment-item h3 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .assessment-item p {
        color: white;
        font-size: 0.95rem;
        opacity: 0.95;
    }

    .assessment-item span {
        display: inline-block;
        background: rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 10px;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .assessment-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 380px;
        color: #A8B4C9;
        font-size: 1.1rem;
    }

    /* Children Section */
    .children-section {
        background: white;
        border-radius: 25px;
        padding: 2rem 2.5rem;
        height: 240px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    /* Child Card */
    .child-card {
        background: linear-gradient(135deg, #FF9A56 0%, #FF6B6B 100%);
        border-radius: 20px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 0.8rem;
        color: white;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .child-card:hover {
        transform: translateY(-3px);
    }

    .child-card:last-child {
        margin-bottom: 0;
    }

    .child-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .child-avatar-small {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, 0.5);
        flex-shrink: 0;
        overflow: hidden;
    }

    .avatar-image {
        width: 70%;
        height: 70%;
        object-fit: cover;
    }

    .child-info h3 {
        font-size: 1.3rem;
        font-weight: 900;
        margin-bottom: 0.2rem;
    }

    .child-meta {
        font-size: 0.85rem;
        opacity: 0.95;
        line-height: 1.3;
    }

    /* Notification Cards */
    .notification-card {
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 1rem;
        color: white;
        cursor: pointer;
        transition: transform 0.3s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .notification-card:hover {
        transform: translateX(5px);
    }

    .notification-card.pink {
        background: linear-gradient(135deg, #FF6B9D 0%, #FFA3C1 100%);
    }

    .notification-card.yellow {
        background: linear-gradient(135deg, #FFD93D 0%, #FFE66D 100%);
        color: #2D3142;
    }

    .notification-card.green {
        background: linear-gradient(135deg, #6BCF7F 0%, #8FE3A0 100%);
    }

    .notification-avatar {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-size: 1.3rem;
        font-weight: 900;
        margin-bottom: 0.2rem;
    }

    .notification-text {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #A8B4C9;
    }

    .empty-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #7C8BA1;
    }

    .empty-description {
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .top-row {
            grid-template-columns: 1fr;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        body {
            padding: 1rem;
        }

        .welcome-card {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
            height: auto;
        }

        .welcome-content h1 {
            font-size: 2.5rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .test-score {
            font-size: 4rem;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Top Row: Welcome Card + Your Children -->
    <div class="top-row">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h1>Hello! Family</h1>
                <p class="welcome-subtitle">Good Day! Don't forget to track your children's development today</p>
            </div>
            <div class="welcome-graphic">
                <img src="<?php echo e(asset('images/kids.png')); ?>" alt="Welcome Bear" class="graphic-image">
            </div>
        </div>

        <!-- Your Children -->
        <div class="children-section">
            <div class="section-header">
                <span class="section-icon">✨</span>
                <h2 class="section-title">Your Children</h2>
            </div>

            <?php $__empty_1 = true; $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="child-card">
                    <div class="child-header">
                        <div class="child-avatar-small">
                            <?php if(isset($child['profile_image']) && $child['profile_image']): ?>
                                <img src="/images/<?php echo e($child['profile_image']); ?>" alt="<?php echo e($child['name']); ?>" class="avatar-image">
                            <?php endif; ?>
                        </div>
                        <div class="child-info">
                            <h3><?php echo e($child['name']); ?></h3>
                            <div class="child-meta">Section: Nursery</div>
                            <div class="child-meta">Age: <?php echo e($child['age']); ?></div>
                            <?php if(isset($child['total_tests']) && $child['total_tests'] > 0): ?>
                                <div class="progress-container">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo e(($child['completed'] / $child['total_tests']) * 100); ?>%"></div>
                                    </div>
                                    <div class="progress-text"><?php echo e($child['completed']); ?>/<?php echo e($child['total_tests']); ?> tests completed</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state">
                    <div class="empty-title">No children registered</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Row: Latest Test Result + Upcoming Assessments + Notifications -->
    <div class="content-grid">
        <!-- Latest Test Result -->
        <div class="test-result-section">
            <div class="section-header">
                <span class="section-icon">⭐</span>
                <h2 class="section-title">Latest Test Result</h2>
            </div>
            
            <?php if(count($latest_results) > 0): ?>
                <?php 
                    $result = $latest_results[0];
                    $score = $result['score'];
                    $scoreClass = 'score-average';
                    if ($score >= 130) $scoreClass = 'score-very-superior';
                    elseif ($score >= 120) $scoreClass = 'score-superior';
                    elseif ($score >= 110) $scoreClass = 'score-high-average';
                    elseif ($score >= 90) $scoreClass = 'score-average';
                    elseif ($score >= 80) $scoreClass = 'score-low-average';
                    elseif ($score >= 70) $scoreClass = 'score-borderline';
                ?>
                <div class="test-result-card">
                    <div class="test-header">
                        <div class="test-avatar">
                            <?php if(isset($result['profile_image']) && $result['profile_image']): ?>
                                <img src="/images/<?php echo e($result['profile_image']); ?>" alt="<?php echo e($result['child_name']); ?>" class="avatar-image">
                            <?php endif; ?>
                        </div>
                        <div class="test-name-section">
                            <div class="test-child-name"><?php echo e($result['child_name']); ?></div>
                            <div class="test-section">Section</div>
                        </div>
                    </div>
                    <div class="test-score-area">
                        <div class="test-score <?php echo e($scoreClass); ?>"><?php echo e($result['score']); ?></div>
                        <div class="test-interpretation"><?php echo e($result['interpretation']); ?></div>
                        <div class="test-date">Completed: <?php echo e(\Carbon\Carbon::parse($result['date'])->format('M d, Y')); ?></div>
                        <a href="#" class="action-btn">View Full Report →</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="test-result-card">
                    <div class="empty-state">
                        <div class="empty-title">No test results yet</div>
                        <div class="empty-description">Results will appear here</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Upcoming Assessments -->
        <div class="assessments-section">
            <div class="section-header">
                <span class="section-icon">📅</span>
                <h2 class="section-title">Upcoming Assessments</h2>
            </div>
            
            <?php $__empty_1 = true; $__currentLoopData = $upcoming_assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $now = now();
                    $status = 'upcoming';
                    $statusText = 'Upcoming';
                    if ($assessment->start_date <= $now && $assessment->end_date >= $now) {
                        $status = 'in-progress';
                        $statusText = 'In Progress';
                    } elseif ($assessment->end_date < $now) {
                        $status = 'overdue';
                        $statusText = 'Overdue';
                    }
                ?>
                <div class="assessment-item">
                    <h3>
                        <?php echo e($assessment->description); ?>

                        <span class="status-badge <?php echo e($status); ?>"><?php echo e($statusText); ?></span>
                    </h3>
                    <p><?php echo e($assessment->start_date->format('M d')); ?> - <?php echo e($assessment->end_date->format('M d, Y')); ?></p>
                    <span><?php echo e($assessment->student->first_name); ?> <?php echo e($assessment->student->last_name); ?></span>
                    <br>
                    <a href="#" class="action-btn-assessment">Start Assessment →</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="assessment-empty">
                    <p>No upcoming assessments scheduled</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Notifications -->
        <div class="notifications-section">
            <div class="section-header">
                <span class="section-icon">🔔</span>
                <h2 class="section-title">Notifications</h2>
            </div>

            <?php
                $hasIncompleteTests = collect($children)->sum(function($child) {
                    return $child['total_tests'] - $child['completed'];
                }) > 0;
            ?>

            <?php if($hasIncompleteTests): ?>
                <div class="notification-card pink">
                    <div class="notification-avatar"></div>
                    <div class="notification-content">
                        <div class="notification-title">Test Not Done</div>
                        <div class="notification-text">finish where you left >></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(count($latest_results) > 0): ?>
                <div class="notification-card yellow">
                    <div class="notification-avatar"></div>
                    <div class="notification-content">
                        <div class="notification-title">Check Results</div>
                        <div class="notification-text">View results >></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(count($upcoming_assessments) > 0): ?>
                <div class="notification-card green">
                    <div class="notification-avatar"></div>
                    <div class="notification-content">
                        <div class="notification-title">Check Results</div>
                        <div class="notification-text">View results >></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!$hasIncompleteTests && count($latest_results) == 0 && count($upcoming_assessments) == 0): ?>
                <div class="empty-state">
                    <div class="empty-title">No notifications</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\family\index.blade.php ENDPATH**/ ?>