<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 12.05.2015
 * Time: 15:49
 */

namespace app\components;

use yii;
use yii\swiftmailer\Message;


class SwiftHeaders {
    /**
     * Устанавливаем дополнительные заголовки писем
     *
     * @param Message $letter
     * @param array $aData
     *
     */
    public static function serAntiSpamHeaders(&$letter, $aData = []) {
        $oMsg = $letter->getSwiftMessage();
        $headers = $oMsg->getHeaders();
        $headers->addTextHeader('Precedence', 'bulk');
        $headers->addTextHeader('Auto-Submitted', 'auto-generated');
        $email = '';
        $site = $_SERVER['HTTP_HOST'];

        if( isset($aData['email']) ) {
            $email = $aData['email'];
        }
        else if( isset(Yii::$app->params['contactEmail']) ) {
            $email = Yii::$app->params['contactEmail'];
        }

        if( isset($aData['site']) ) {
            $site = $aData['site'];
        }

        if( $email !== '' ) {
            $headers->addTextHeader('Error-to', '<' . $email . '>');
            $headers->addTextHeader('List-Owner', '<' . $email . '>');
            $headers->addTextHeader('List-Unsubscribe', '<mailto:' . $email . '>,<http://' . $site .'/>');
        }
    }
}