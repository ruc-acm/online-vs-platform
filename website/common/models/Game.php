<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "game".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Program[] $programs
 * @property UserScore[] $userScores
 * @property User[] $users
 */
class Game extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
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
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::className(), ['gameId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserScores()
    {
        return $this->hasMany(UserScore::className(), ['gameId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->viaTable('userScore', ['gameId' => 'id']);
    }
}
