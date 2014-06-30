<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sourceCode".
 *
 * @property integer $programId
 * @property integer $language
 * @property string $code
 *
 * @property Program $program
 */
class SourceCode extends ActiveRecord
{
    const LANGUAGE_C = 0;
    const LANGUAGE_CPP = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sourceCode}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'code'], 'required'],
            [['code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'programId' => 'Program ID',
            'language' => 'Language',
            'code' => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId']);
    }
}
