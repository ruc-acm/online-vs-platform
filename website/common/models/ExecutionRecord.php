<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "executionRecord".
 *
 * @property integer $id
 * @property integer $attackerId
 * @property integer $defenderId
 * @property integer $winner
 * @property string $replay
 * @property integer $status
 *
 * @property User $defender
 * @property User $attacker
 */
class ExecutionRecord extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISHED = 2;
    const STATUS_RUNTIME_ERROR = 3;
    const STATUS_TLE = 4;
    const STATUS_ILLEGAL_MOVE = 5;
    const STATUS_BAD_FORMAT = 6;
    const STATUS_INTERNAL_ERROR = -1;
    const STATUS_REJECTED = -2;
    
    const WINNER_ATTACKER = 1;
    const WINNER_DEFENDER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%executionRecord}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attackerId', 'defenderId', 'winner'], 'required'],
            [['attackerId', 'defenderId', 'winner', 'status'], 'integer'],
            [['replay'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attackerId' => 'Attacker ID',
            'defenderId' => 'Defender ID',
            'winner' => 'Winner',
            'replay' => 'Replay',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefender()
    {
        return $this->hasOne(User::className(), ['id' => 'defenderId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttacker()
    {
        return $this->hasOne(User::className(), ['id' => 'attackerId']);
    }
}
