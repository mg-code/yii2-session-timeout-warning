<?php

namespace mgcode\sessionWarning\components;

use \Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Object;
use yii\web\Application;

/**
 * @link https://github.com/mg-code/yii2-session-timeout-warning
 * @author Maris Graudins <maris@mg-interactive.lv>
 */
class SessionWarningBootstrap extends Object implements BootstrapInterface
{
    const COOKIE_USER = 'session_warning_user_id';
    const COOKIE_TIMEOUT = 'session_warning_time';

    public $initMessages = false;

    /** @inheritdoc */
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_AFTER_REQUEST, [$this, 'setTimeoutCookie']);
        if($this->initMessages) {
            $app = \Yii::$app->i18n;
            if (!array_key_exists('mgcode/sessionWarning', $app->translations)) {
                $app->translations['mgcode/sessionWarning'] = [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@mgcode/sessionWarning/messages',
                ];
            }
        }
    }

    /**
     * Sets timeout cookie based on current user status
     * @param Event $event
     * @return bool
     */
    public function setTimeoutCookie(Event $event)
    {
        /** @var Application $application */
        $application = $event->sender;
        $user = $application->user;

        if (!$user || !$user->getIdentity()) {
            return $this->setCookie(null, null);
        }

        $session = Yii::$app->session;
        $authTimeout = $session->get($user->authTimeoutParam);
        $timeout = $authTimeout ? $authTimeout : time() + $session->getTimeout();

        return $this->setCookie($user->id, $timeout);
    }

    /**
     * Sets cookie
     * @param $userId
     * @param $timeout
     * @return bool
     */
    protected function setCookie($userId, $timeout)
    {
        $expire = $timeout ? $timeout : time() - 10;

        // We are using native functions, because we don't want to encrypt cookies. Cookies should be readable from JS.
        setcookie(static::COOKIE_USER, $userId, $expire, '/');
        setcookie(static::COOKIE_TIMEOUT, $timeout, $expire, '/');

        return true;
    }
}
