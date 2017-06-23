<?php
use app\components\MyHtml as Html;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Nav;
?>
<nav id="wnav" class="navbar-default navbar-fixed-top navbar" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#wnav-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="dropdown navbar-brand">
                <a href="<?= Yii::$app->homeUrl ?>" data-toggle="dropdown"
                   class="dropdown-toggle"><?= Yii::$app->params['project']; ?><b class="caret"></b></a>
                <?php
                 echo Dropdown::widget([
                    'items' => array_filter(array_map(function ($v) {
                        if ($v == Yii::$app->params['project']) {
                            return false;
                        }
                        if ((Yii::$app->user->can('beVideoManager') && !Yii::$app->user->can('beManager')) && !Yii::$app->user->can('access_' . $v)) {
                            return false;
                        }

                        $path = explode("/", ltrim(Yii::$app->request->url, "/"));
                        if (in_array($path[0], Yii::$app->params['projects'])) {
                            array_shift($path);
                            array_unshift($path, $v);
                        } else {
                            array_unshift($path, $v);
                        }
                        $url = "/" . implode("/", $path);
                        return ['label' => $v, 'url' => $url];
                    }, Yii::$app->params['projects'])),
                ]);
                ?>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="wnav-collapse">
            <?php
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'encodeLabels' => false,
                'activateParents' => true,
                'items' => [
                    [
                        'label' => Html::glyphicon("dashboard"),
                        'url' => ['dashboard/index'],
                        'options' => ['title' => 'Рабочий стол'],
                    ],
                    [
                        'label' => Html::glyphicon("stats") . ' Статистика',
                        'url' => ['daily-stat/index'],
                        'visible' => Yii::$app->user->can('bePartner') || Yii::$app->user->can('beManager'),
                    ],
                    [
                        'label' => Html::glyphicon("film") . ' Видео',
                        'url' => ['video/index'],
                        'visible' => Yii::$app->user->can('beVideoManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("list-alt") . ' Категории',
                                'url' => ['video-category/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("film") . ' Список видео',
                                'url' => ['video/index'],
                            ],
                            [
                                'label' => Html::glyphicon("list") . ' Комментарии к видео',
                                'url' => ['video-comment/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("tags") . ' Теги',
                                'url' => ['tag/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("filter") . ' Каналы',
                                'url' => ['channel/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("star-empty") . ' Актёры',
                                'url' => ['actor/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("tasks") . ' В обработке',
                                'url' => ['video-task/index'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                            [
                                'label' => Html::glyphicon("plus") . ' Добавить',
                                'url' => ['video-task/create'],
                                'visible' => !Yii::$app->user->can('beDumbVideoManager'),
                            ],
                        ]
                    ],
                    [
                        'label' => Html::glyphicon("send") . ' Пуши',
                        'url' => '#',
                        'visible' => (Yii::$app->user->can('beManager') || Yii::$app->user->can('bePushManager')),
                        'items' => [
                            [
                                'label' => Html::glyphicon("list") . ' Список задач',
                                'url' => ['push-task/index'],
                            ],
                            [
                                'label' => Html::glyphicon("plus") . ' Создать задачу',
                                'url' => ['push-task/create'],
                            ],
                        ]
                    ],
                    [
                        'label' => Html::glyphicon("eye-open") . ' Приложения',
                        'url' => ['application/index'],
                        'visible' => Yii::$app->user->can('beManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("user") . ' Пользователи',
                                'url' => ['app-user/index'],
                            ],
                            [
                                'label' => Html::glyphicon("phone") . ' Устройства',
                                'url' => ['device/index'],
                            ],
                            [
                                'label' => Html::glyphicon("list") . ' Приложения',
                                'url' => ['application/index'],
                            ],
                        ]
                    ],
                    [
                        'label' => Html::glyphicon("piggy-bank") . ' Реклама',
                        'url' => ['ads/index'],
                        'visible' => Yii::$app->user->can('beManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("euro") . ' Маркет приложений',
                                'url' => ['ads/index'],
                            ]
                        ]
                    ],
                    [
                        'label' => Html::glyphicon("star") . ' Премиум',
                        'visible' => Yii::$app->user->can('beManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("euro") . ' Тарифы',
                                'url' => ['premium-tariff/index'],
                            ],
                            [
                                'label' => Html::glyphicon("stats") . ' Статистика',
                                'url' => ['premium-stat/index'],
                            ]
                        ]
                    ],
                    [
                        'label' => Html::glyphicon("euro") . ' Бухгалтерия',
                        'visible' => Yii::$app->user->can('beManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("film") . ' Копирайт видео',
                                'url' => ['accounting-video/index'],
                            ]
                        ]
                    ],
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Html::glyphicon("menu-hamburger"),
                        'url' => '#',
                        'visible' => Yii::$app->user->can('beVideoManager'),
                        'items' => [
                            [
                                'label' => Html::glyphicon("knight") . ' Управление пользователями',
                                'url' => ['admin-user/index'],
                                'visible' => Yii::$app->user->can('beAdmin'),
                            ],
                            '<li class="divider"></li>',
                            [
                                'label' => Html::glyphicon("book") . ' Журнал',
                                'url' => ['journal/index'],
                                'visible' => Yii::$app->user->can('beAdmin'),
                            ],
                            [
                                'label' => Html::glyphicon("fire") . ' Журнал ошибок',
                                'url' => ['error-log/index'],
                                'visible' => Yii::$app->user->can('beAdmin'),
                            ],
                            '<li class="divider"></li>',
                            [
                                'label' => Html::glyphicon("floppy-disk") . ' Хранилища',
                                'url' => ['storage/index'],
                                'visible' => Yii::$app->user->can('beAdmin'),
                            ],
                            [
                                'label' => Html::glyphicon("file") . ' Файлы в хранилище',
                                'url' => ['storage-files/index'],
                                'visible' => Yii::$app->user->can('beAdmin'),
                            ],
                        ],
                    ],
                    [
                        'label' => Html::glyphicon("log-in") . ' Вход',
                        'url' => ['site/login'],
                        'visible' => Yii::$app->user->isGuest,
                    ],
                    [
                        'label' => Html::glyphicon("user") . ' ' . (Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username),
                        'url' => '#',
                        'visible' => !Yii::$app->user->isGuest,
                        'items' => [
                            [
                                'label' => Html::glyphicon("floppy-saved") . ' Настройки',
                                'url' => ['admin-user/settings'],
                                'linkOptions' => ['data-method' => 'post'],
                            ],
                            [
                                'label' => Html::glyphicon("cog") . ' Конструктор ссылок',
                                'url' => ['dashboard/link-manager'],
                                'linkOptions' => ['data-method' => 'post'],
                            ],
                            '<li class="divider"></li>',
                            [
                                'label' => Html::glyphicon("log-out") . ' Выход',
                                'url' => ['site/logout'],
                                'linkOptions' => ['data-method' => 'post'],
                            ]
                        ],
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</nav>
