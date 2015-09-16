<?php

namespace app\modules\cron\models;

use Yii;
use yii\base\InvalidValueException;
use yii\db\Expression;

/**
 * This is the model class for table "{{%crontab}}".
 *
 * @property integer $cron_id
 * @property string $cron_min
 * @property string $cron_hour
 * @property string $cron_day
 * @property string $cron_wday
 * @property string $cron_path
 * @property string $cron_tstart
 * @property string $cron_tlast
 * @property string $cron_comment
 * @property integer $cron_isactive
 */
class Crontab extends \yii\db\ActiveRecord
{
    public $aIntervals = [
        'cron_min' => [0, 59],
        'cron_hour' => [0, 23],
        'cron_day' => [0, 31],
        'cron_wday' => [1, 7],
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%crontab}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cron_path', 'cron_min', 'cron_hour', 'cron_day', 'cron_wday', 'cron_isactive', ], 'required', ],
            [['cron_min', 'cron_hour', 'cron_day', 'cron_wday'], 'string', 'max' => 96],
            [['cron_min', 'cron_hour', 'cron_day', 'cron_wday', ], 'validateInterval', ],
            [['cron_tstart', 'cron_tlast'], 'safe'],
            [['cron_isactive'], 'integer'],
            [['cron_path', 'cron_comment'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cron_id' => 'Cron ID',
            'cron_min' => 'Минуты',
            'cron_hour' => 'Часы',
            'cron_day' => 'Число', //  месяца
            'cron_wday' => 'День', //  недели
            'cron_path' => 'Путь',
            'cron_tstart' => 'Начато',
            'cron_tlast' => 'Окончено',
            'cron_comment' => 'Комментарий',
            'cron_isactive' => 'Активно',
        ];
    }

    /**
     * Проверка интервалов
     * @param string $attribute
     * @param array $params
     */
    public function validateInterval($attribute, $params) {
        try {
            $aRet = $this->getPeriodValues($this->$attribute, $this->aIntervals[$attribute][0], $this->aIntervals[$attribute][1]);
        }
        catch(\Exception $e) {
            $this->addError($attribute, $e->getMessage());
        }
    }

    /**
     * Вывод в одну строку с разделителямивременной части задания
     * @param string $delim
     * @return string
     */
    public function getFulltime($delim = "\t:\t") {
        return $this->cron_min
            . $delim
            . $this->cron_hour
            . $delim
            . $this->cron_day
            . $delim
            . $this->cron_wday;
    }

    /**
     * Получение чисел из диапазона $nMin .. $nMax, соответствующих строке $sPeriod
     *
     * @param string $sPeriod
     * @param int $nMin
     * @param int $nMax
     * @return array
     */
    public function getPeriodValues($sPeriod, $nMin = 0, $nMax = 60) {
//        Yii::info("getPeriodValues({$sPeriod}, {$nMin}, {$nMax})");
        // формат такой d1-d2/d3,
        // состояния - подготовка(пробелы) [0], первая звездочка[1], первая цифра[2], вторая цифра[3], третья цифра[4]
        $nPeriod = strlen($sPeriod);
        $aAllParts = [];
        $aPart = [0 => '', 1 => '', 2 =>'',];
        $nState = 0;
        $aStates = [
            [ 0,  1,  2,  3,  4 ],   // на входе: пробелы - состояние не меняется
            [ 1, -1, -1, -1, -1 ],   // на входе: *
            [ 2, -1,  2,  3,  4 ],   // на входе: цифра
            [-1, -1,  3, -1, -1 ],    // на входе: -
            [-1,  4,  4,  4, -1 ],    // на входе: /
            [-1,  0,  0,  0,  0 ],    // на входе: ,
        ];

//        Yii::info("aStates = " . print_r($aStates, true));
        for($i = 0; $i<$nPeriod; $i++) {
            $ch = substr($sPeriod, $i, 1);
            $nRow = -1;
            if( preg_match('|\\s|', $ch) ) {
                // пробелы пропускаем
                // остаемся в том же состоянии
                $nRow = 0;
            }
            else if( $ch == '*' ) {
                $nRow = 1;
            }
            else if( preg_match('|\\d|', $ch) ) {
                $nRow = 2;
            }
            else if( $ch == '-' ) {
                $nRow = 3;
            }
            else if( $ch == '/' ) {
                $nRow = 4;
            }
            else if( $ch == ',' ) {
                $nRow = 5;
            }

            $nNextState = (isset($aStates[$nRow]) ? $aStates[$nRow][$nState] : -1);
//            Yii::info("nState = {$nState} ch = {$ch} nRow = {$nRow} nNextState = {$nNextState}");

            if( $nNextState == -1 ) {
                // ошибка в строке
                throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ' позиция ' . $i . ' символ ' . $ch);
            }

            if( $nNextState < $nState ) {
                // перешли в начальное состояние - нужно добавить разобранную часть
                $aAllParts[] = $aPart;
                $aPart = [0 => '', 1 => '', 2 => '', ];
                $nState = 0;
            }
            else {
                $nState = $nNextState;
                if( ($nRow == 1) || ($nRow == 2) ) {
                    $aPart[$nState-$nRow] .= $ch;
                }
            }
        }

        if( $nState > 0 ) {
            $aAllParts[] = $aPart;
        }

        // проверка на корректность чисел в диапазонах
        $aValues = [];
        foreach($aAllParts As $k=>$v) {
            if( ($v[0] == '*')  ) {
                if( $v[1] != '' ) {
                    throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v));
                }
                if( ($k != 0) || (count($aAllParts) > 1) ) {
                    throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v) . ', звездочка должна быть единственной частью в периоде'); // ' [k = '.$k.' count = '.count($aAllParts).']'
                }
            }
            else {
                if( ($v[0] < $nMin) || ($v[0] > $nMax) ) {
                    throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v) . ' ' . $v[0] . ' не входит в диапазон ' . $nMin . ' .. ' . $nMax);
                }
                if( $v[1] == '' ) {
                    if( $v[2] != '' ) {
                        throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v));
                    }
                }
                else {
                    if( ($v[1] < $nMin) || ($v[1] > $nMax) ) {
                        throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v) . ' ' . $v[1] . ' не входит в диапазон ' . $nMin . ' .. ' . $nMax);
                    }
                    if( $v[1] < $v[0] ) {
                        throw new InvalidValueException('Ошибка в строке ' . $sPeriod . ', неверная часть ' . $this->printPart($v) . ' ' . $v[0] . ' > ' . $v[1]);
                    }
                }
            }
            $aAllParts[$k][] = $this->generateValues($v, $nMin, $nMax);
        }

        return $aAllParts;
    }

    /**
     * Печать части интервала
     *
     * @param array $aData
     * @return string
     */
    public function printPart($aData) {
        $s = $aData[0];
        $s .= (($aData[1] != '') ? '-' : '') . $aData[1];
        $s .= (($aData[2] != '') ? '/' : '') . $aData[2];
        return $s;
    }

    /**
     * Генератор чисел для диапазона, заданного массивом данных $aData[0]-$aData[1]/$aData[2]
     *
     * @param array $aData
     * @param integer $nMin
     * @param integer $nMax
     * @return array
     */
    public function generateValues($aData, $nMin, $nMax) {
        if( $aData[0] == '*' ) {
            $aData[0] = $nMin;
            $aData[1] = $nMax;
        }

        if( $aData[1] == '' ) {
            return [$aData[0]];
        }

        if( $aData[2] == '' ) {
            $aData[2] = 1;
        }

        $aRet = [];
        for($i = $aData[0]; $i <= $aData[1]; $i++) {
            if( $i % $aData[2] == 0 ) {
                $aRet[] = $i;
            }
        }
        return $aRet;
    }

    /**
     * получение всех значений элементов cron
     * @return array
     */
    public function getTime() {
        $aRet = [];
        foreach (['cron_min', 'cron_hour', 'cron_day', 'cron_wday'] as $v) {
            $aPeriods = $this->getPeriodValues($this->$v, $this->aIntervals[$v][0], $this->aIntervals[$v][1]);
            $sKey = substr($v, 5);
            $aRet[$sKey] = [];
            foreach($aPeriods As $a) {
//                Yii::info('aRet['.$sKey.'] = ' . print_r($aRet[$sKey], true));
                $aRet[$sKey] = array_merge($aRet[$sKey], $a[3]);
            }
        }
        return $aRet;
    }

    /**
     * Сравнение времени в массиве $aTime с возможными значениями в $aCron
     * @param array $aCron
     * @param array $aTime
     * @return bool
     */
    public function isTimeInRange($aCron, $aTime) {
        $bRet = true;
        foreach(['min', 'hour', 'day', 'wday'] as $v) {
            if( !in_array($aTime[$v], $aCron[$v]) ) {
                $bRet = false;
                break;
            }
        }
        return $bRet;
    }

    /**
     * Равны ли 2 времени
     * @param array $t1
     * @param array $t2
     * @return bool
     */
    public function isTimeEqual($t1, $t2) {
        $bRet = true;
        foreach(['min', 'hour', 'day', 'wday'] as $v) {
            if( $t1[$v] != $t2[$v] ) {
                $bRet = false;
                break;
            }
        }
        return $bRet;
    }

    /**
     * Получаем запись для выполнения в текущее время
     * @return Crontab
     */
    public static function getTaskForRun() {
        self::clearOldCronTask();
        
        $t = time();
        $oRet = null;

        $aTime = [
            'min' => intval(date('i', $t), 10),
            'hour' => date('G', $t),
            'day' => date('j', $t),
            'wday' => date('N', $t),
        ];

        Yii::info('Cron cur time = ' . print_r($aTime, true));

        $aTask = self::find()
            ->where([
                'cron_isactive' => 1,
                'cron_tstart' => null,
            ])
            ->orderBy('cron_tlast')
            ->all();

        foreach($aTask As $ob) {
            /** @var Crontab $ob */
            $t1 = strtotime($ob->cron_tlast !== null ? $ob->cron_tlast : '2014-01-01');
            $aLast = [
                'min' => intval(date('i', $t1), 10),
                'hour' => date('G', $t1),
                'day' => date('j', $t1),
                'wday' => date('N', $t1),
            ];
            $aCron = $ob->getTime();

            Yii::info('Cron record ' . $ob->cron_id . ' : ' . $ob->getFulltime("\t"));

            if( ($ob->cron_tstart === null)
             && $ob->isTimeInRange($aCron, $aTime) ) {
                Yii::info('Cron record ' . $ob->getFulltime("\t") . ' cur time in cron range');
                if( !$ob->isTimeEqual($aTime, $aLast) ) {
                    Yii::info('Cron record ' . $ob->getFulltime("\t") . ' now != last need to run');
                    $nUpdated = self::updateAll(
                        [
                            'cron_tstart' => date('Y-m-d H:i:s', $t),
                        ],
                        [
                            'cron_id' => $ob->cron_id,
                            'cron_tstart' => null,
                        ]
                    );
                    if( $nUpdated > 0 ) {
                        // мы поставили время запуска, а не другой процесс
                        $oRet = $ob;
                        Yii::info('Cron record ' . $ob->getFulltime("\t") . ' will run ' . $ob->cron_path);
                        break;
                    }
                }
                else {
                    Yii::info('Cron record ' . $ob->getFulltime("\t") . ' last run time is now ' . $ob->cron_tlast);
                }
            }
        }

        return $oRet;
    }

    /**
     * @param int $id
     */
    public static function finishTask($id) {
        $ob = self::findOne($id);
        Yii::info('Cron finishTask('.$id.'): Task ' . $id . ' = '.($ob === null ? 'null' : print_r($ob->attributes, true)));
        if( ($ob !== null) && ($ob->cron_tstart !== null) ) {
            $nUpdated = self::updateAll(
                [
                    'cron_tstart' => null,
                    'cron_tlast' => $ob->cron_tstart,
                ],
                [
                    'cron_id' => $ob->cron_id,
                ]
            );
//            Yii::info('Finish task: ' . $ob->cron_id . ' ['.$nUpdated.']');
        }

    }

    /**
     *
     */
    public static function clearOldCronTask() {
        $nUpdated = self::updateAll(
            [
                'cron_tstart' => null,
            ],
            [
                '<', 'cron_tstart', new Expression('DATE_SUB(NOW(), INTERVAL 3 MINUTE)'),
            ]
        );
    }

    /**
     *
     */
    public function createJsRunScript() {
        return 'setTimeout( function(){ console.log("Run cron '.$this->cron_path.' ['.$this->cron_id.']"); jQuery.getJSON("'.$this->cron_path.'", function(data){ console.log("Finish cron '.$this->cron_path.' ['.$this->cron_id.']"); console.log(data); }); }, 1000 );';
    }
}
