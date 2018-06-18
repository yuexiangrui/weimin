<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
           // 'basePath' => '@api/modules/v1',
            'class' => 'api\modules\v1\Module',
        ],
        'v2' => [
          'class' => 'api\modules\v2\Module'
        ]
    ],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/files','v1/videos','v1/adpictures','v1/answers','v1/funds'],
                    'pluralize'=>false,
                    'extraPatterns'=>[
                        'GET allcategory'=>'allcategory',
                        'GET fundsguanli'=>'fundsguanli',
                        'GET fundstuoguan'=>'fundstuoguan',
                        'GET newindex'=>'newindex',
                        'GET questionlist'=>'questionlist'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v2/funds','v2/spiderfund','v2/guzhi','v2/question'],
                    'pluralize'=>false,
                    'extraPatterns'=>[
                        'GET allcategory'=>'allcategory',
                        'GET fundsguanli'=>'fundsguanli',
                        'GET fundstuoguan'=>'fundstuoguan',
                        'GET newindex'=>'newindex',
                        'GET funddetail'=>'funddetail',
                        'GET getallcompany'=>'getallcompany',
                        'GET getsign'=>'getsign',
                        'GET getcategory'=>'getcategory',
                        'GET gettwocategory'=>'gettwocategory',
                        'GET getfinallyimage'=>'getfinallyimage',
                        'GET getopenidbycode'=>'getopenidbycode',
                        'GET getpersonimg'=>'getpersonimg',
                        'GET getshareconfig'=>'getshareconfig',
                        'GET cuotilist'=>'cuotilist',
                        'GET geturl'=>'geturl',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];
