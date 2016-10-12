<?php

namespace app\controllers;

use app\models\PlayField;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @param null $id
     * @return string
     */
    public function actionIndex($id = null)
    {
        return $this->render('index', [
            'play_field' => $this->getPlayField($id)
        ]);
    }

    /**
     * Создать или получить игровое поле
     * @param $id
     * @return PlayField
     */
    private function getPlayField($id)
    {
        if ($id) {
            $model = PlayField::findOne(intval($id));
            if ($model) {
                return $model;
            }
            Yii::$app->getSession()->setFlash('danger', 'Неверный id. Сгенерирована новая комбинация.');
        }
        return $this->createPlayField();
    }

    /**
     * Создание игового поля
     * @return PlayField
     * @throws Exception
     */
    private function createPlayField()
    {
        $play_field = new \app\components\PlayField(Yii::$app->params['play_field']['size_x'], Yii::$app->params['play_field']['size_y']);
        foreach (Yii::$app->params['play_field']['ships'] as $number => $size_ship) {
            for ($i = 0; $i < $number; $i++) {
                $play_field->addShip($size_ship);
            }
        }
        $model = new PlayField();
        $model->filled_points = $play_field->getDataForSave();
        if (!$model->save()) {
            throw new Exception("Unable to save the play field");
        }
        return $model;
    }

}
