<?php
namespace svk\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Скрипты для выпадающего списка bootstrap
 */
class BootstrapDropDownInputAsset extends AssetBundle
{
    public $sourcePath = '@vendor/internet-design/yii2-svk-common/assets/static';
    public $css = [];
    public $js = [
        'bootstrap-drop-down-input.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    ];

    /**
     * Регистрация скриптов
     *
     * @param View $view
     * @param string $wrapperId
     * @param array $options настройки для инпута
     * @return mixed
     */
    public static function register($view)
    {
        $wrapperId = func_get_arg(1);
        $options = func_get_arg(2);
        $ret = parent::register($view);
        $view->registerJs(new JsExpression("$('#{$wrapperId}').dropdownInput(" . Json::encode($options) . ");"));
        return $ret;
    }
}