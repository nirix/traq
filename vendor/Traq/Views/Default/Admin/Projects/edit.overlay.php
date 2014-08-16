<form id="form_id" action="<?php echo Request::base("/admin/projects/{$project->id}/edit"); ?>" method="post" class="form-horizontal">
    <?php echo TWBS::modalHeader($t('edit_project')); ?>
    <div class="modal-body">
        <?php echo View::render('ProjectSettings/Options/_form'); ?>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success"><?php echo $t('save'); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $t('cancel'); ?></button>
    </div>
</form>
