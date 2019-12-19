<?php

namespace app\components;

use yii\filters\ContentNegotiator;
use yii\web\Response;

class ErrorAction extends \yii\web\ErrorAction
{
    public function run()
    {
        // ошибка произошла вне API
        if (substr(\Yii::$app->request->pathInfo, 0, 4) != 'api/') {
            return parent::run();
        }

        // подготовка данных

        $net = new ContentNegotiator([
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ]);
        // подготавливаем \yii\web\Response
        $net->negotiate();

        // обработка ошибки внутри API
        $response = \Yii::$app->getResponse();
        $response->setStatusCode($this->getExceptionCode());
        $response->data = [
            'name' => $this->getExceptionName(),
            'message' => $this->getExceptionMessage(),
            'code' => $this->getExceptionCode(),
        ];
        $response->send();
    }
}