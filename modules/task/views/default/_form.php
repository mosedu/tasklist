<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;

use app\modules\user\models\Department;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use app\modules\task\models\File;

use mosedu\multirows\MultirowsWidget;
use app\modules\task\models\Subject;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $form yii\widgets\ActiveForm */

$sDepartmentId = Html::getInputId($model, 'task_dep_id');
$sWorkerId = Html::getInputId($model, 'task_worker_id');
$sCurWorkersId = Html::getInputId($model, 'curworkers');
$sUrl = Url::to(['/user/worker/list'], true);

$aTextParam = [
    'horizontalCssClasses' => [
        'label' => 'col-sm-3',
        'offset' => 'col-sm-offset-3',
        'wrapper' => 'col-sm-9',
    ],
];

$aTextParamSummary = [
    'horizontalCssClasses' => [
        'label' => 'col-sm-4',
        'offset' => 'col-sm-offset-4',
        'wrapper' => 'col-sm-8',
    ],
];

$aDisable = [];
if( !Yii::$app->user->can('createUser') ) {
    $aDisable = ['readonly' => true, 'disabled' => true];
}
$bCanChange = $model->canChangeDate();

$aDirect = []; // Tasklist::getAllStatuses();

$aSetting = [
    'data' => [], // $aDirect,
    'language' => 'ru',
    'name' => 'selectdirection',
    'options' => [
        'multiple' => false,
        'placeholder' => 'Выберите из списка ...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
];

$bHideSummary = ($model->isNewRecord || (strlen($model->task_summary) == 0));

$aWorker = [0 => ''];
foreach(User::getWorkerList($model->task_dep_id) As $k=>$v) {
    $aWorker[$k] = $v;
}

$bFinished = ($model->task_progress == Tasklist::PROGRESS_FINISH);
$bEditDates = !$bFinished || Yii::$app->user->can('createUser');

$aWorkerSelect = [
    'data' =>  $model->getTaskAvailWokers(),//ArrayHelper::map(Tags::getTagslist(Tags::TAGTYPE_SUBJECT), 'tag_id', 'tag_title'),
    'language' => 'ru',
    'options' => [
        'placeholder' => 'Выберите из списка ...',
        'multiple' => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'ajax' => [
            'url' => $sUrl,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {id: jQuery("#'.$sDepartmentId.'").val(), type: "select2"}; }')
        ],
    ],
];

/*
var oSelDepartment = jQuery("#{$sDepartmentId}"),
    oSelWorker = jQuery("#{$sWorkerId}");
oSelDepartment.on("change", function(event){
    jQuery.get("{$sUrl}", {id: oSelDepartment.val()}, function(data, textStatus, jqXHR){ oSelWorker.html(''); jQuery('<option>').val(0).text("").appendTo(oSelWorker); for(var i in data) { jQuery('<option>').val(i).text(data[i]).appendTo(oSelWorker); } }, 'json');
});

*/

if( !$bEditDates ) {
    $aWorkerSelect = array_merge($aWorkerSelect, ['readonly' => true, 'disabled' => true]);
}

?>

<div class="tasklist-form">
    <?php
/*
    <?= $form->field($model, 'task_num')->textInput() ?>
    <?= $form->field($model, 'task_createtime')->textInput() ?>
    <?= $form->field($model, 'task_finaltime')->textInput() ?>
    <?= $form->field($model, 'task_numchanges')->textInput() ?>
    <?= $form->field($model, 'task_reasonchanges')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'task_progress')->textInput() ?>

*/
    ?>

    <?php $form = ActiveForm::begin([
        'id' => 'task-form',
        'layout' => 'horizontal',

        'enableAjaxValidation' => true,
        'enableClientValidation' => false,

        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,

        'validationUrl' => ['default/validatetask', 'id' => $model->isNewRecord ? 0 : $model->task_id],

        'options'=>[
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'offset' => 'col-sm-offset-3',
                'wrapper' => 'col-sm-9',
                'hint' => 'col-sm-9 col-sm-offset-3',
            ],
        ],
    ]);

    ?>

    <div class="col-sm-8">
        <?= $form->field($model, 'task_direct', $aTextParam)
            ->textarea(array_merge(['rows' => 2], ['readonly' => true, ])) // $bEditDates ? [] : $aDisable
            ->hint(Html::tag('div', Html::a('Выбрать направление', '', ['class' => 'btn btn-default', 'id'=>'idshowselectdirection',]), ['style'=>'text-align: right;'])) ?>

        <?= ''
        // Subject::getList()
        ?>

        <?= $form->field($model, 'task_name', $aTextParam)->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable)) ?>

        <?= $form->field($model, 'task_expectation', array_merge($aTextParam) )->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable)) ?>

        <?= $form->field($model, 'task_summary', array_merge($aTextParam, ['options' => $bHideSummary ? ['style' => 'display: none;', 'class'=>'form-group'] : ['class'=>'form-group']]) )->textarea(['rows' => 4, 'data-req' => $bFinished ? 1 : 0, ]) ?>

        <div id="file-forsummary-region">

        </div>

        <?php

//        if( $model->isNewRecord ) { // new record
            $this->registerJs('var aChache = {},
            oSel = jQuery("#idselectdirectlist"),
            setDirectList = function(data) {
                oSel.find("option").remove();
                for(var i in data) {
                    oSel.append("<option value=\\""+i+"\\">"+data[i]+"</option>");
                }
            };
            jQuery("#idshowselectdirection").on("click", function(event) {
            var nDepId = jQuery("#'.Html::getInputId($model, 'task_dep_id').'").val();
            event.preventDefault();
            if( nDepId in aChache ) {
                setDirectList(aChache[nDepId]);
            }
            else {
                jQuery.ajax({url: "' . Url::to(['default/lastdirect']) . '", data: {depid: nDepId}, dataType: "json", success: function(data, textStatus, jqXHR){
                    aChache[nDepId] = data;
                    setDirectList(data);
                }});
            }
            $("#selectmodal").modal("show");
            return false; });',
            View::POS_READY,
            'showselectinstr');

            Modal::begin([
                'header' => 'Выбрать направление из списка',
                'id' => 'selectmodal',
                'size' => "modal-lg",
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" id="setselecteddata">Вставить</button>',
            ]);

            // echo Select2::widget($aSetting);
            echo Html::listBox(
                'selectdir',
                '',
                $aDirect,
                ['id' => 'idselectdirectlist', 'class'=>'form-control', 'multiple' => true]
            );

            Modal::end();
            $this->registerJs('jQuery("#setselecteddata")
                .on("click", function(event) {
                    var s = jQuery("#idselectdirectlist option:selected").text();
                    // console.log("Selected data : " + s);
                    event.preventDefault();
                    if( s.length > 0 ) { jQuery("#'.Html::getInputId($model, 'task_direct').'").val(s); }
                    jQuery("#selectmodal").modal("hide");
                    return false;
                });
                jQuery("#idselectdirectlist").on("dblclick", function(event){
                    jQuery("#setselecteddata").trigger("click");
                });',
                View::POS_READY,
                'selectdirection');
//        }

        $sIdProgress = Html::getInputId($model, 'task_progress');
        $sIdSummary = Html::getInputId($model, 'task_summary');
        $nFinish = Tasklist::PROGRESS_FINISH;
        $sJs = <<<EOT
var oSelProgress = jQuery("#{$sIdProgress}"),
    oSummary = jQuery("#{$sIdSummary}"),
    oDivSummary = jQuery(".field-tasklist-task_summary"),
    oShowSummaryButton = jQuery('#showsummaryfield');

oDivSummary.append(jQuery("#file-forsummary-region"));

oSelProgress.on(
    "change",
    function(event) {
        if( jQuery(this).val() == {$nFinish} ) {
            oSummary.attr("data-req", 1);
            oDivSummary.show();
        }
        else {
            oSummary.attr("data-req", 0);
//            oDivSummary.hide();
        }
    }
);

oShowSummaryButton.on('click', function(event){
    event.preventDefault();
    oDivSummary.show();
    return false;
});
EOT;
        $this->registerJs($sJs, View::POS_READY, 'changeprogress');

        ?>

        <?= $form->field(
            $model,
            'task_reasonchanges',
            array_merge($aTextParam, ['options' => ['style' => (strlen($model->task_reasonchanges) > 0 ? '' : 'display: none;'), 'class' => "form-group field-tasklist-reasonchange"]])
        )->textarea(array_merge(['rows' => 3, 'data-old'=>$model->isNewRecord ? '' : $model->_oldAttributes['task_actualtime'],], $bEditDates ? [] : $aDisable)) ?>

        <?php
/*
            $sFile = $this->findViewFile('/file/_loadfile', $context = $this);
            echo $this->renderPhpFile(
                $sFile,
                [
                    'form' => $form,
                    'model' => new File(),
                ]
            );
*/
        ?>
    </div>

    <div class="col-sm-4">
                <?= $form->field($model, 'task_dep_id')->dropDownList(Department::getList(false), $aDisable) ?>
                <?= $form->field($model, 'task_type')->dropDownList(Tasklist::getAllTypes(), $bEditDates ? [] : $aDisable) ?>
                <?= '' // $form->field($model, 'task_worker_id')->dropDownList($aWorker, $bEditDates ? [] : $aDisable) ?>
                <?= $form->field($model, 'curworkers')->widget(Select2::classname(), $aWorkerSelect) ?>
                <?= $form->field($model, 'task_progress')->dropDownList(Tasklist::getAllProgresses(), $bEditDates ? [] : $aDisable) ?>
                <?= $form->field(
                    $model,
                    'task_actualtime',
                    [
                        'labelOptions'=>[
                            'label'=>$model->getAttributeLabel($model->isNewRecord ? 'task_finaltime' : 'task_actualtime'),
                        ],
                    ]
                )->widget(
                    DatePicker::className(),
                    [
                        'model' => $model,
                        'attribute' => 'task_actualtime',
                        'disabled' => !$bEditDates,
                        'readonly' => !$bEditDates,
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        //            'pickerButton' => false,
                        'removeButton' => false,
                        'options' => [
                            'placeholder' => 'Срок исполнения',
                        ],
                        'pluginOptions' => [
                            'autoclose' => true,
                        ],
                        'pluginEvents' => [
                            "changeDate" => "function(e) {
//                    console.log(e);
                     var dt = new Date(e.date),
                         s = '',
                         sCompare = dt.getFullYear() + '',
                         n = dt.getDate(),
                         ob = jQuery('.field-tasklist-reasonchange');
                     s += ((n < 10) ? '0' : '') + n + '.';
                     n = dt.getMonth() + 1;
                     s += ((n < 10) ? '0' : '') + n + '.';
                     sCompare += ((n < 10) ? '0' : '') + n;
                     s += dt.getFullYear()
                     n = dt.getDate();
                     sCompare += ((n < 10) ? '0' : '') + n;
                     jQuery('#" . Html::getInputId($model, 'task_reasonchanges') . "').attr('data-old', s);
                     if( ".($bCanChange ? "false" : "true")." ) {
                         if( (sCompare > '" . preg_replace('|(\\d+)\\.(\\d+)\\.(\\d+)|', '${3}${2}${1}', $model->task_actualtime) . "' && ".($model->isNewRecord ? 'false' : 'true').") || ".(strlen($model->task_reasonchanges) > 0 ? 'true' : 'false')." ) {
                            ob.show();
                         }
                         else {
                            ob.hide();
                         }
                     }
                     }",
                        ]
                    ]
                    ) ?>
                <?= '' // $form->field($model, 'reasonchange', ['options' => ['style' => 'display: none;', 'class' => "form-group field-tasklist-reasonchange"]])->textarea(['rows' => 2, 'data-old'=>$model->isNewRecord ? '' : $model->_oldAttributes['task_actualtime'], ]) ?>

        <div class="file-data" id="filedata">
            <div class="form-group">
                <label class="control-label col-sm-3"></label>
                <div class="col-sm-9">
                    <?php

                    echo MultirowsWidget::widget(
                        [
                            'model' => File::className(),
                            'form' => $form,
                            'records' => $model->taskfiles, // [new OrderItems(),],
                            'additionalData' => File::FILE_TASK_GROUP, // [new OrderItems(),],
                            'rowview' => '@app/modules/task/views/file/_loadfile2.php',
//                    'tag' => 'tr',
                            'defaultattributes' => ['file_group' => File::FILE_TASK_GROUP],
//                    'tagOptions' => ['class' => 'clear-item'],
                            'addlinkselector' => '#add-task-file-link',
                            'dellinkselector' => '.remove-file',
//                    'formselector' => '#task-form',
                            'afterInsert' => 'function(ob){ console.log("Insert row : task-file"); }',
                            'afterDelete' => 'function(){ console.log("Delete row : task-file"); }',
//                    'scenario' => 'userform',
                            'canDeleteLastRow' => true,
                        ]
                    );
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>


        <div class="file-data" id="filesummdata">
            <div class="form-group">
                <label class="control-label col-sm-3"></label>
                <div class="col-sm-9">
                    <?php

                    echo MultirowsWidget::widget(
                        [
                            'model' => File::className(),
                            'form' => $form,
                            'records' => $model->taskfiles, // [new OrderItems(),],
                            'additionalData' => File::FILE_SUMMARY_GROUP, // [new OrderItems(),],
                            'rowview' => '@app/modules/task/views/file/_loadfile2.php',
//                    'tag' => 'tr',
                            'defaultattributes' => ['file_group' => File::FILE_SUMMARY_GROUP],
//                    'tagOptions' => ['class' => 'clear-item'],
                            'addlinkselector' => '#add-summary-file-link',
                            'dellinkselector' => '.remove-file',
                            'afterInsert' => 'function(ob){ console.log("Insert row : summary-file"); }',
                            'afterDelete' => 'function(){ console.log("Delete row : summary-file"); }',
//                    'scenario' => 'userform',
                            'canDeleteLastRow' => true,
                        ]
                    );

                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>


        <div class="file-data" id="filedata">
            <div class="form-group">
                <div class="col-sm-3">&nbsp;</div>
                <div class="col-sm-9">
                    <?= Html::a('<span class="glyphicon glyphicon-cloud-upload"></span> Добавить файл', '', ['id' => 'add-task-file-link', 'class'=>'btn btn-default btn-lg']) ?>
                </div>
                <?php
                /*
                    <div class="col-sm-4">
                        <?= Html::a('Файл к отчету', '', ['id' => 'add-summary-file-link', 'class'=>'btn btn-default']) ?>
                    </div>
                */
                ?>
                <div class="clearfix"></div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

    <div class="col-sm-8">
        <div class="form-group">
            <div class="col-sm-3">&nbsp;</div>
            <div class="col-sm-3">
                <?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> Сохранить', ['class' => 'btn btn-success btn-lg']) ?>
                <?= '' // Html::submitButton('Сохранить изменения', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <div class="col-sm-6">
                <?= ($bHideSummary ? Html::a('<span class="glyphicon glyphicon-file"></span> Добавить промежуточный результат', '', ['class' => 'btn btn-default btn-lg', 'id'=>'showsummaryfield']) : '') ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    // $model->isNewRecord ? 'Создать' :
    $sJs = <<<EOT
var oSelDepartment = jQuery("#{$sDepartmentId}"),
    oSelWorker = jQuery("#{$sWorkerId}");
oSelDepartment.on("change", function(event){
    $("#{$sCurWorkersId}").select2("val", null);
//    jQuery.get("{$sUrl}", {id: oSelDepartment.val()}, function(data, textStatus, jqXHR){ oSelWorker.html(''); jQuery('<option>').val(0).text("").appendTo(oSelWorker); for(var i in data) { jQuery('<option>').val(i).text(data[i]).appendTo(oSelWorker); } }, 'json');
});
EOT;
    $this->registerJs($sJs, View::POS_READY, 'fillselectworker');


    ?>

</div>
