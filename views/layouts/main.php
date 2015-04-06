<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\modules\user\models\User;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Задачи ГАУ Темоцентр',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            // ['label' => 'About', 'url' => ['/site/about']],
            // ['label' => 'Контакт', 'url' => ['/contact']],
            $aItems = [
                ['label' => 'Главная', 'url' => ['/']],
            ];

            if( Yii::$app->user->can('createUser') ) {
                $aItems = array_merge(
                    $aItems,
                    [
                        ['label' => 'Пользователи', 'url' => ['/user']],
                        ['label' => 'Отделы', 'url' => ['/user/department']],
                    ]
                );
            }

            $aItems[] = Yii::$app->user->isGuest ?
                ['label' => 'Вход', 'url' => ['/user/default/login']] :
                ['label' => 'Выход (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/user/default/logout'],
                    'linkOptions' => ['data-method' => 'post']];

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $aItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= '' /*Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) */ ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Темоцентр <?= date('Y') ?></p>
            <p class="pull-right"></p> <?php /* <?= Yii::powered() ?> */ ?>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
