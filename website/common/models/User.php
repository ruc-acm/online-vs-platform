<?php
namespace common\models;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $passwordHash
 * @property string $passwordResetToken
 * @property string $email
 * @property string $authKey
 * @property integer $role
 * @property UserProfile $profile
 * @property integer $status
 * @property integer $lastCreated
 * @property integer $lastUpdated
 * @property integer $lastLogin
 * @property string $password write-only password
 * @property Program[] $programs
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_USER = 10;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lastCreated', 'lastUpdated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lastUpdated'],
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
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne(
            [
                'passwordResetToken' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->getSecurity()->generateRandomKey();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->getSecurity()->generateRandomKey() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * Update last logging in time
     */
    public function updateLastLogin()
    {
        $this->detachBehavior('timestamp');
        $this->lastLogin = new Expression('NOW()');
        $this->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Program::className(), ['userId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['userId' => 'id']);
    }

    /**
     * get the program that is used to defend others' attack.
     * returns null if none found.
     * @return \common\models\Program
     */
    public function getFlagshipProgram()
    {
        return $this->getPrograms()->orderBy(['stability' => 'DESC', 'lastCreated' => 'DESC'])->limit(1)->one();
    }

    /**
     * get the program that is used to attack others.
     * returns null if none found.
     * @return \common\models\Program
     */
    public function getLatestProgram()
    {
        return $this->getPrograms()->orderBy(['lastCreated' => 'DESC'])->limit(1)->one();
    }


}
