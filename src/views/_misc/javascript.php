traq.base = '<?= Request::base(); ?>';
<?php if (isset($projectSlug) && !empty($projectSlug)): ?>
    traq.project_slug = '<?= $projectSlug; ?>';
<?php endif; ?>

// Translations
<?php foreach (
    [
        'yes',
        'no',
        'summary',
        'description',
        'type',
        'owner',
        'assigned_to',
        'component',
        'milestone',
        'version',
        'status',
        'priority'
    ] as $string
): ?>
    language.<?= $string; ?> = '<?= l($string); ?>';
<?php endforeach; ?>
