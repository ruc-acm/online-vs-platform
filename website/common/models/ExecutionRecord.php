<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "executionRecord".
 *
 * @property integer $id
 * @property integer $attackerId
 * @property integer $defenderId
 * @property integer $winner
 * @property string $replay
 * @property integer $status
 * @property string $submitted
 * @property string $log
 * @property Program $defender
 * @property Program $attacker
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
    const STATUS_COMPILE_ERROR = 7;
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
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['submitted'],
                ],
                'value' => new Expression('NOW()'),
            ],
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
            'submitted' => 'Submitted Time'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefender()
    {
        return $this->hasOne(Program::className(), ['id' => 'defenderId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttacker()
    {
        return $this->hasOne(Program::className(), ['id' => 'attackerId']);
    }
}
