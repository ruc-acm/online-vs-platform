<?php


namespace frontend\models;


use yii\base\Model;

/**
 * Class ChangePasswordForm
 * @package frontend\models
 *
 * @property string oldPassword
 * @property string newPassword
 * @property string newPasswordRepeat
 */
class ChangePasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oldPassword', 'newPasswordRepeat', 'newPassword'], 'required'],
            ['oldPassword', 'validatePassword'],
            [['oldPassword', 'newPasswordRepeat', 'newPassword'], 'string', 'min' => 6],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->oldPassword)) {
                $this->addError('oldPassword', 'Incorrect old password.');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = \Yii::$app->user->identity;
        }

        return $this->_user;
    }

    public function changePassword()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->password = $this->newPassword;
            return $user->save();
        } else {
            return false;
        }
    }
} 