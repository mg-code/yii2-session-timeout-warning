<?php

namespace mgcode\sessionWarning\assets;

use Yii;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * @link https://github.com/mg-code/yii2-session-timeout-warning
 * @author Maris Graudins <maris@mg-interactive.lv>
 */
class SessionWarningAsset extends AssetBundle
{
    public $sourcePath = '@mgcode/sessionWarning/assets/files';
    public $js = [
        'js/session-warning.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'mgcode\helpers\HelpersAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'mgcode\assets\JsCookieAsset',
    ];

    /**
     * Initializes plugin
     * @param View $view
     * @return $this
     */
    public function initPlugin(View $view, $options = [])
    {
        $options = array_merge([
            'message' => Yii::t('mgcode/sessionWarning', 'Your session is going to expire at {time}.')
        ], $options);

        $json = Json::encode($options);
        $view->registerJs("$('#session-warning-modal').sessionWarning({$json});");
        return $this;
    }
}