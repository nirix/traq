<?=$t("notifications.hello_x", ['name' => $user->name]) . PHP_EOL?>

<?=$t("notifications.account_activation.body.txt", [
    'title' => settings("title"),
    'host'  => Request::schemeAndHttpHost(),
    'path'  => Request::basePath($route("account_activation", [
        'activation_code' => $user->activation_code
    ]))
]) . PHP_EOL?>

<?=Request::schemeAndHttpHost() . Request::basePath($route("account_activation", [
        'activation_code' => $user->activation_code
]))?>
