<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\modules\user\models\User;
use app\components\widgets\Alert;

use yii\helpers\Json;

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
                'brandLabel' => 'Задачи ГАУ ТемоЦентр',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            // ['label' => 'About', 'url' => ['/site/about']],
            // ['label' => 'Контакт', 'url' => ['/contact']],
            $aLists = [];
            $aItems = [];
            if( isset(Yii::$app->params['panelcheckbox']) ) {
                // выводим дополнительные кнопки, если они есть
                $aItems[] = '<li class="panelcheckbox"></li>';
                $sAttr = Json::encode(Yii::$app->params['panelcheckbox']);
                $sJs = <<<EOT
var oButton = {$sAttr},
    oIns = jQuery(".panelcheckbox"),
    prepareButton = function(i, oData){
        var oCheckbox = jQuery("#" + i),
            val = ( oCheckbox.length > 0 ) ? oCheckbox.prop('checked') : false,
            oLink = null,
            fOnClick = function(ob){
                return function(event){
                    event.preventDefault();
                    ob.trigger("click");
                    ob.parents("form:first").trigger("submit");
                    return false;
                };
            };

        if( oCheckbox.length > 0 ) {
            oLink = jQuery("<a href=\"#\" style=\"float: left;\" class=\""+(val ? "panelcb-on" : "panelcb-of")+"\" title=\""+oData.title+"\"><span class=\"glyphicon glyphicon-"+oData.icon+"\"></span></a>"); //  "+val+"
            if( 'callback' in oData ) {
                oLink.on("click", function(event){
                    event.preventDefault();
                    oCheckbox.trigger("click");
                    var f = new Function("oButton", oData.callback);
                    f(oLink);
                    return false;
                });
            }
            else {
                oLink.on("click", fOnClick(oCheckbox));
            }
        }
        else {
            if( oData.link != '' ) {
                var aOpt = { href: oData.link, style: "float: left;", title: oData.title},
                    sOpt = "";
                if( "linkOptions" in oData ) {
                    for(var j in oData.linkOptions) {
                        if( j in aOpt ) {
                            aOpt[j] += oData.linkOptions[j];
                        }
                        else {
                            aOpt[j] = oData.linkOptions[j];
                        }
                    }
                }
                for(var j in aOpt) {
                    sOpt += " " + j + "=\"" + aOpt[j] + "\"";
                }
                oLink = jQuery("<a"+sOpt+"><span class=\"glyphicon glyphicon-"+oData.icon+"\"></span></a>");
            }
        }

        if( oLink !== null ) {
            oIns.append(oLink);
        }
    };

for(var i in oButton) {
    prepareButton(i, oButton[i]);
}
EOT;
                $sCss = <<<EOT
.navbar-inverse .navbar-nav > li > a.panelcb-on {
    color: #33ff33;
}
.navbar-inverse .navbar-nav > li > a.panelcb-on:hover {
    color: #ffffff;
}
EOT;

                $this->registerJs($sJs);
                $this->registerCss($sCss);
            }

            $aItems[] = ['label' => 'Задачи', 'url' => ['/']];

            if( Yii::$app->user->can('createUser') ) {
                $aLists = array_merge(
                    $aLists,
                    [
                        ['label' => 'Лог задач', 'url' => ['/task/action']],
                        ['label' => 'Пользователи', 'url' => ['/user']],
                        ['label' => 'Отделы', 'url' => ['/user/department']],
                    ]
                );
            }
            if( count($aLists) > 0 ) {
                $aItems[] = [
                    'label' => 'Дополнительно',
                    'items' => $aLists,
                ];
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
            <?= Alert::widget(); ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; ТемоЦентр <?= date('Y') ?></p>
            <p class="pull-right"></p> <?php /* <?= Yii::powered() ?> */ ?>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
