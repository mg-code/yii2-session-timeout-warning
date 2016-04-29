<?php

namespace mgcode\sessionWarning\controllers;

use yii\web\Controller;
use yii\web\Response;

/**
 * @link https://github.com/mg-code/yii2-session-timeout-warning
 * @author Maris Graudins <maris@mg-interactive.lv>
 */
class SessionWarningController extends Controller
{
    public function actionExtend()
    {
        $user = \Yii::$app->user;

        // auto renews
        $user = $user->getIdentity();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => $user ? 1 : 0];
    }
}