<?=t('notifications.hello_x', ['name' => $user->name]) . PHP_EOL?>

<?=t('notifications.account_activation.body.txt', [
    'title' => setting("title"),
    'host'  => Request::schemeAndHttpHost(),
    'path'  => routeUrl('account_activation', [
        'activation_code' => $activationCode
    ])
]) . PHP_EOL?>

<?=Request::schemeAndHttpHost() . routeUrl('account_activation', [
    'activation_code' => $activationCode
])?>
