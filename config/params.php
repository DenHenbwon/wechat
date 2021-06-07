<?php

define('BASE_PATH', dirname(__DIR__ . '../'));
define('LOG_PATH', BASE_PATH . '/runtime/logs/');
define('UPLOADS_PATH', BASE_PATH . '/web/uploads/');

define('IMG_PATH', '/images/');

//laxsjc
//define('WECHAT_APPID', "wxbc00952e2ce81999");
//define('WECHAT_APPSECRET', "d181da5c18102c0548da5290772ad4de");
//define('WECHAT_TOKEN', "y9G54vH03mzSAx4ZbPYI1AKjOI10zg5B");
//define('WECHAT_ENCODINGAESKEY', "li1wpoP0f9hwNncPJqyOgq5sszkN25Ogwf7GezZnmC9");

//demo
define('WECHAT_APPID', "wxefd55d56a1d1c4ff");
define('WECHAT_APPSECRET', "1c8d80d826417f8db878efb0f57af488");
define('WECHAT_TOKEN', "wxDemo");
define('WECHAT_ENCODINGAESKEY', "");


define('THREE_DAYS_TIME_STAMP', 259200);
define('PUSH_THREE_DAYS_DIFF_TIME', 259000);

return [
    'allow_img_suffix' => ['jpg', 'jpeg', 'gif', 'png'],
    'preview_open_ids' => [
        'oK61o0TeV7HHonhnptrsCnAKzI1Q',//吕卫杰
        'oK61o0Rsv8U0934l2nO0hgjelO8s',
    ],
];

