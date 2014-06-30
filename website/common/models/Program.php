<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "program".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $gameId
 * @property string $name
 * @property integer $stability
 *
 * @property Game $game
 * @property User $user
 */
class Program extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'gameId', 'name', 'stability'], 'required'],
            [['id', 'userId', 'gameId', 'stability'], 'integer'],
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
}
