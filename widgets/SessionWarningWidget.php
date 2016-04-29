<?php

namespace mgcode\sessionWarning\widgets;

use yii\base\Widget;

/**
 * @link https://github.com/mg-code/yii2-session-timeout-warning
 * @author Maris Graudins <maris@mg-interactive.lv>
 */
class SessionWarningWidget extends Widget
{
    public $extendUrl = ['/session-warning/extend'];

    /** @var int By default warns 5 minutes before session end. */
    public $warnBefore = 300;

    /** @var string|array Logout url, if defined shows logout button */
    public $logoutUrl;

    public function run()
    {
        $user = \Yii::$app->user;
        if(!$user || $user->isGuest) {
            return null;
        }

        $userId = $user->id;
        return $this->render('SessionWarningView', [
            'userId' => $userId,
            'extendUrl' => $this->extendUrl,
            'warnBefore' => $this->warnBefore,
            'logoutUrl' => $this->logoutUrl,
        ]);
    }
}
