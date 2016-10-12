<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "play_field".
 *
 * @property integer $id
 * @property string $filled_points
 * @property integer $created_at
 */
class PlayField extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'play_field';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => 'created_at'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filled_points'], 'required'],
            [['filled_points'], 'string'],
            [['created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filled_points' => 'Заполненные точки',
            'created_at' => 'Дата создания',
        ];
    }

    public function afterFind()
    {
        $this->filled_points = json_decode($this->filled_points, true);
        if (json_last_error()) {
            $this->filled_points = [];
        }
    }

    public function beforeValidate()
    {
        $this->filled_points = is_array($this->filled_points) ? json_encode($this->filled_points) : $this->filled_points;
        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->filled_points = is_string($this->filled_points) ? json_decode($this->filled_points, true) : $this->filled_points;
        return parent::afterSave($insert, $changedAttributes);
    }
}
