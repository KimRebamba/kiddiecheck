<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['frame', 'direction' => 'ltr']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['frame', 'direction' => 'ltr']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $file = $frame->file();
    $line = $frame->line();
?>

<div
    <?php echo e($attributes->merge(['class' => 'truncate font-mono text-xs text-neutral-500 dark:text-neutral-400'])); ?>

    dir="<?php echo e($direction); ?>"
>
    <span data-tippy-content="<?php echo e($file); ?>:<?php echo e($line); ?>">
        <?php if(config('app.editor')): ?>
            <a href="<?php echo e($frame->editorHref()); ?>" @click.stop>
                <span class="hover:underline decoration-neutral-400"><?php echo e($file); ?></span><span class="text-neutral-500">:<?php echo e($line); ?></span>
            </a>
        <?php else: ?>
            <?php echo e($file); ?><span class="text-neutral-500">:<?php echo e($line); ?></span>
        <?php endif; ?>
    </span>
</div>
<<<<<<<< Updated upstream:storage/framework/views/0551d9e098809d0f1ffbb238bb1710c7.php
<?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\vendor\laravel\framework\src\Illuminate\Foundation\resources\exceptions\renderer\components\file-with-line.blade.php ENDPATH**/ ?>
========
<?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\vendor\laravel\framework\src\Illuminate\Foundation\resources\exceptions\renderer\components\file-with-line.blade.php ENDPATH**/ ?>
>>>>>>>> Stashed changes:storage/framework/views/261f5c6f8cab688b03a6a1701f8b7056.php
