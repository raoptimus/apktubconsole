<?php
/**
 * Часть конфигурационного файла для баз данных
 * mongodb для общих данных: Video,VideoCategory,Tag,SearchQuery,Channel,Actor...
 * mongodb2 для раздельных данных (!если нет mongodb): VideoHistory,VideoComment,User,RpcStat,Push*,Log,Device,*Stat...
 * @return array
 */

/**
 * @return bool|string
 */
function local()
{
    $long = ip2long(gethostbyname(gethostname()));
    if (
        ($long >= ip2long("10.0.0.0") && $long <= ip2long("10.255.255.255")) ||
        ($long >= ip2long("100.64.0.0") && $long <= ip2long("100.127.255.255")) ||
        ($long >= ip2long("172.16.0.0") && $long <= ip2long("172.31.255.255")) ||
        ($long >= ip2long("192.168.0.0") && $long <= ip2long("192.168.255.255")) ||
        ($long >= ip2long("127.0.0.0") && $long <= ip2long("127.255.255.255"))
    ) {
        return "localhost:27017";
    }
    return false;
}

//Если мы на локальной машине, то продакшн базу запрещено использовать и будет всегда localhost
$authDb = $mongodb3 = $mongodb2 = $mongodb = @[
    "sun" => "192.168.0.176:27017"
][gethostname()] ?: local();

$authDb = $authDb ? $authDb ."/tubeadmins" : "host.com:27017,host.com:27017/tubeadmins?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
$mongodb = $mongodb ? $mongodb . "/tubeserver" : false;
$mongodbUpdate = $mongodb2 = $mongodb2 ? $mongodb2 . "/tubeserver" : false;
$mongodb3 = $mongodb3 ? $mongodb3 . "/storage" : "host.com:27017,host.com:27017/storage?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";

switch ($_PROJECT) {
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = $mongodb ?: "host.com:27017,host.com:27017/tubeserver?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case ".......": {
        $mongodb = $mongodb ?: "host.com:27017/tubeserver";
        $mongodbUpdate = $mongodb2 = $mongodb2 ?: "host.com:27017/tubeserver2";
        break;
    }
    case ".......": {
        $mongodb = $mongodb ?: "host.com:27017/tubeserver";
        $mongodb2 = $mongodb2 ?: "host.com:27017/tubeserver2";
        $mongodbUpdate = "host.com:27017/...";
        break;
    }
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017,host.com:27017/...?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017,host.com:27017/...?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017,host.com:27017/...?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017,host.com:27017/...?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case ".......": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017,host.com:27017/...?replicaSet=tubeserver&w=1&readPreference=primaryPreferred";
        break;
    }
    case "....net": {
        $mongodbUpdate = $mongodb2 = $mongodb = "host.com:27017/...";
        break;
    }
    case "....net": {
        $mongodb = $mongodb ?: "host.com:27017/tubeserver";
        $mongodbUpdate = $mongodb2 = $mongodb2 ?: "host.com:27017/...";
        break;
    }
    default: {
        throw new RuntimeException("Config for project '{$_PROJECT}' not found");
    }
}

return [
    "mongodb" => [
        "class" => '\yii\mongodb\Connection',
        "dsn" => "mongodb://${mongodb}",
    ],
    "mongodb2" => [
        "class" => '\yii\mongodb\Connection',
        "dsn" => "mongodb://${mongodb2}",
    ],
    "mongodbUpdate" => [
        "class" => '\yii\mongodb\Connection',
        "dsn" => "mongodb://${mongodbUpdate}",
    ],
    "mongodb3" => [
        "class" => '\yii\mongodb\Connection',
        "dsn" => "mongodb://${mongodb3}",
    ],
    "authDb" => [
        "class" => '\yii\mongodb\Connection',
        "dsn" => "mongodb://${authDb}",
    ],
];