<?php

namespace app\modules\v1;

use Yii;

/**
 * v1 api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->setupApi();
    }

    public function setupApi()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        Yii::$app->response->charset = 'UTF-8';
    }
}
