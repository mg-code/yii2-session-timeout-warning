<?php

namespace mgcode\sessionWarning\components;

use \Yii;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\Application;

/**
 * @link https://github.com/mg-code/yii2-session-timeout-warning
 * @author Maris Graudins <maris@mg-interactive.lv>
 */
class SessionWarningBootstrap extends BaseObject implements BootstrapInterface
{
    const COOKIE_USER = '__swuid';
    const COOKIE_TIMEOUT = '__swto';
    const COOKIE_TIMEOUT_ABSOLUTE = '__swato';

    public $initMessages = false;

    /** @inheritdoc */
    public function bootstrap($app)
    {
        if(!$this->getIsPjaxRequest()) {
            $app->on(Application::EVENT_AFTER_REQUEST, [$this, 'setTimeoutCookie']);
        }
        if ($this->initMessages) {
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
            return $this->clearCookies();
        }

        $session = Yii::$app->session;

        // Default session timeout
        $timeout = time() + $session->getTimeout();

        // If auth timeout is closer than default timeout
        $authTimeout = $session->get($user->authTimeoutParam);
        if ($authTimeout && $authTimeout < $timeout) {
            $timeout = $authTimeout;
        }

        // Absolute timeout
        $absoluteTimeout = $session->get($user->absoluteAuthTimeoutParam);

        return $this->setCookie($user->id, $timeout, $absoluteTimeout);
    }

    /**
     * Clears cookies
     */
    protected function clearCookies()
    {
        $time = time() - 60;
        setcookie(static::COOKIE_USER, null, $time, '/');
        setcookie(static::COOKIE_TIMEOUT, null, $time, '/');
        setcookie(static::COOKIE_TIMEOUT_ABSOLUTE, null, $time, '/');
    }

    /**
     * Sets session cookie
     * @param int|string $userId
     * @param int $timeout
     * @param int $absoluteTimeout
     * @return bool
     */
    protected function setCookie($userId, $timeout, $absoluteTimeout)
    {
        // We are using native functions, because we don't want to encrypt cookies. Cookies should be readable from JS.
        setcookie(static::COOKIE_USER, $userId, $timeout + 60, '/');
        setcookie(static::COOKIE_TIMEOUT, $timeout, $timeout + 60, '/');

        if ($absoluteTimeout) {
            setcookie(static::COOKIE_TIMEOUT_ABSOLUTE, $absoluteTimeout, $absoluteTimeout + 60, '/');
        } else {
            setcookie(static::COOKIE_TIMEOUT_ABSOLUTE, null, time() - 60, '/');
        }

        return true;
    }

    /**
     * @return boolean whether the current request requires pjax response
     */
    protected function getIsPjaxRequest()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        return $headers->get('X-Pjax');
    }
}
