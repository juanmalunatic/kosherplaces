<?php

$strs = [];

$theme_dir = dirname(__FILE__);
$theme_url = get_stylesheet_directory_uri();

$strs['login_loading'] = <<<HTML
    <img src="$theme_url/img/loading.gif" />
HTML;

return [
    'login_loading' => $strs['login_loading']
];