traq.base = '<?php echo Request::base(); ?>';
<?php if (isset($projectSlug) && !empty($projectSlug)): ?>
    traq.project_slug = '<?= $projectSlug; ?>';
<?php endif; ?>

// Yes and No translations
language.yes = '<?php echo l('yes'); ?>';
language.no = '<?php echo l('no'); ?>';
<?php foreach (['summary', 'description', 'type', 'owner', 'assigned_to', 'component', 'milestone', 'version', 'status', 'priority'] as $string): ?>
    language.<?php echo $string; ?> = '<?php echo l($string); ?>';
<?php endforeach; ?>
