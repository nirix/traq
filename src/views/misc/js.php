$(document).ready(function () {
    moment.locale('<?=moment_locale()?>');

    window.traq.uri = "<?=Request::$basePath?>";
    window.traq.locale = {
      confirm: {
        yes: "<?=t('confirm.yes')?>",
        no: "<?=t('confirm.no')?>"
      }
    };
});
