traq.base = '<?= Request::base(); ?>';
<?php if (isset($projectSlug) && !empty($projectSlug)): ?>
    traq.project_slug = <?= json_encode($projectSlug, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
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
