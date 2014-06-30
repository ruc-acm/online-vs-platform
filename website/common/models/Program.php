<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "program".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $gameId
 * @property string $name
 * @property integer $stability
 * @property SourceCode $sourceCode
 * @property Game $game
 * @property User $user
 * @property string $lastCreated;
 */
class Program extends ActiveRecord
{
    const STABILITY_STABLE = 1;
    const STABILITY_BETA = 2;
    const STABILITY_ALPHA = 3;
    const STABILITY_DEVELOPMENT = 10;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lastCreated'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'gameId', 'name', 'stability'], 'required'],
            [['userId', 'gameId', 'stability'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'gameId' => 'Game ID',
            'name' => 'Name',
            'stability' => 'Stability',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'gameId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    public function getSourceCode()
    {
        return $this->hasOne(SourceCode::className(), ['programId' => 'id']);
    }
}
