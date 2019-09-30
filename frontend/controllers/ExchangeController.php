<?php

namespace frontend\controllers;

use common\models\ExchangeSearch;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class ApiController
 * @package frontend\controllers
 */
class ExchangeController extends ActiveController
{
    public $modelClass = 'common\models\Exchange';
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['view', 'index'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }



}