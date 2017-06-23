<?php

$defaults = [
    'adminEmail' => 'admin@example.com',
    'videoThumbCdnUrl' => 'http://i........',
    'videoCdnKey' => '...',
    'videoAntihotMod' => 'modsec',
    'videoCdnUrl' => "http://vc........",
    'thumbCachePath' => "/thumb_cache/$_PROJECT/",
    'videoPageSize' => 54,
    'pushTaskPageSize' => 100,
    'statPageSize' => 100,
    'categoryPageSize' => 20,
    'appUserPageSize' => 20,
    'deviceEventPageSize' => 50,
    'projects' => $_PROJECTS,
    'project' => $_PROJECT,
    'whiteList' => [

        '127.0.0.1',

    ],
    'cryptKey' => 'FreakY0u,Spilberg!',
    'google' => [
        'analytics' => [
            'secret' => '{"web":{"client_id":"....apps.googleusercontent.com","project_id":"xtvproject","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://accounts.google.com/o/oauth2/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_secret":"...","redirect_uris":["http://test.re/oauth2callback.php","http://test.re","http://apktub.re/report/oauth2callback","http://adm......../oauth2callback"]}}',
            'email' => 'xxx-317@...ect.iam.gserviceaccount.com',
            'file_path_secret' => dirname(__DIR__) . '/resource/google.analytics.p12',
        ],

    ],
    'apkSite' => '.......',
    'apkMailLink' => '/........apk?s=mail',
    'loginLifeTime' => 1 * 86400,
    'uploadDir' => "/upload/",
    'pushGoUrlDefaultScheme' => 'http://',
    'viewRole' => 'access_' . $_PROJECT,
    'slugTranslit' => [
        "ru" => false,
        "en" => false,
        "zh" => false,
        "es" => false,
        "ar" => false,
        "hi" => false,
        "bn" => false,
        "pt" => false,
        "ja" => false,
        "de" => false,
        "fr" => false,
        "ko" => false,
        "ta" => false,
        "it" => false,
        "ur" => false,
        "tr" => false,
        "pl" => false,
        "ms" => false,
        "fa" => false,
        "nl" => false,
    ],
    'excludeKeywords' => [
        '...', '...', '...', '...', '...',
    ],
];


switch ($_PROJECT) {
    case ".......": {
        $defaults['thumbCachePath'] = "/thumb_cache/......./";
        $defaults['apkSite'] = ".......";
        $defaults['apkMailLink'] = "/........apk?s=mail";
        $defaults['slugTranslit']['ru'] = true;
        $defaults['pushGoUrlDefaultScheme'] = "mob.tube.app";
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    case ".......": {
        $defaults['slugTranslit']['ru'] = true;
        $defaults['pushGoUrlDefaultScheme'] = "mobru.tube.app";
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    case ".......": {
        $defaults['videoThumbCdnUrl'] = "http://....cdn13.com";
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    case ".......": {
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    case ".......": {
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    case "....net": {
        $defaults['videoThumbCdnUrl'] = "http://....cdn13.com";
        $defaults['videoAntihotMod'] = "ucdn";
        $defaults['videoCdnUrl'] = "http://....cdn13.com";
        $defaults['videoCdnKey'] = '...';
        return $defaults;
    }
    case "....net": {
        $defaults['videoCdnUrl'] = "http://vc........";
        return $defaults;
    }
    default: {
        return $defaults;
        break;
    }
}
