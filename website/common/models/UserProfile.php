<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "userProfile".
 *
 * @property integer $userId
 * @property string $nickName
 *
 * @property User $user
 */
class UserProfile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%userProfile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId'], 'required'],
            [['userId'], 'integer'],
            [['nickName'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userId' => 'User ID',
            'nickName' => 'Nick Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
