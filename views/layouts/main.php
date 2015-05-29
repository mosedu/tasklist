<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Modal;
use yii\web\View;

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
                'brandLabel' => str_replace("\n", "<br />\n", Html::encode("Система мониторинга текущих задач\nструктурных подразделений\nГАУ «ТемоЦентр»")), // 'Задачи ГАУ ТемоЦентр',
                'brandUrl' => false, // Yii::$app->homeUrl,
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
                $this->registerJs($sJs);
            }

        $sCss = <<<EOT
.navbar-inverse .navbar-nav > li > a.panelcb-on {
    color: #33ff33;
}
.navbar-inverse .navbar-nav > li > a.panelcb-on:hover {
    color: #ffffff;
}
.navbar-brand {
    font-size: 12px;
    line-height: 14px;
    padding: 5px 15px;
}

.navbar-header {
    color: #ffffff;
    font-size: 12px;
    line-height: 14px;
    padding: 5px 15px;
}
.grig-active-column {
    width: 120px;
    text-align: center;
}
EOT;
        $this->registerCss($sCss);
        $sJs = <<<EOT
var oToplink = jQuery("a.navbar-brand");
oToplink.replaceWith(oToplink.html());

var params = {};
params[jQuery('meta[name=csrf-param]').prop('content')] = jQuery('meta[name=csrf-token]').prop('content');

jQuery('.showinmodal').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#messagedata'),
        oBody = ob.find('.modal-body'),
        oLink = $(this);

    oBody.text("");
    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
    return false;
});

EOT;

        $this->registerJs($sJs);

        if( !Yii::$app->user->isGuest ) {
            $aItems[] = ['label' => 'Задачи', 'url' => ['/']];
            $aItems[] = ['label' => 'KPI', 'url' => ['/user/default/getkpi']];
        }

            if( Yii::$app->user->can('createUser') ) {
                $aLists = array_merge(
                    $aLists,
                    [
                        ['label' => 'Лог задач', 'url' => ['/task/action']],
                        ['label' => 'Пользователи', 'url' => ['/user']],
                        ['label' => 'Отделы', 'url' => ['/user/department']],
                        ['label' => 'Направления', 'url' => ['/task/subject']],
                        ['label' => 'Переносы дат', 'url' => ['/task/requestmsg']],
                    ]
                );
            }

            if( Yii::$app->user->can(User::ROLE_ADMIN) ) {
                $aLists = array_merge(
                    $aLists,
                    [
                        ['label' => 'Импорт Excel', 'url' => ['/user/import/xls']],
                    ]
                );
            }

            if( Yii::$app->user->can('createWorker') ) {
                $aLists = array_merge(
                    $aLists,
                    [
                        ['label' => 'Сотрудники', 'url' => ['/user/worker']],
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
                ['label' => 'Выход (' . Yii::$app->user->identity->us_email . ')', // username
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
            <p class="pull-right"><?= '' //Html::a('Сообщить о неполадках', '/message', ['id'=>'showmessagedialog'])  ?></p>
        </div>
    </footer>

<?php
Modal::begin([
    'header' => 'Срочное сообщение',
    'id' => 'sendmessagedialog',
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>'
        . '<button type="button" class="btn btn-primary" id="sendmessage">Отправить</button>',
]);

Modal::end();

Modal::begin([
    'header' => '<span></span>',
    'id' => 'messagedata',
    'size' => Modal::SIZE_LARGE,
]);
Modal::end();

$sJs =  <<<EOT
jQuery('#showmessagedialog').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#sendmessagedialog'),
        oBody = ob.find('.modal-body'),
        oLink = $(this);

    oBody.text("");
    oBody.load("/support");
    ob.modal('show');
    return false;
});

EOT;

// $this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
