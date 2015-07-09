<?php
namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class UserController extends Controller {
    public function actionResetPassword($username) {
        $user = User::findByUsername($username);
        $user->password = \Yii::$app->security->generatePasswordHash($this->prompt('new password:'));
        $user->save();
        return 0;
    }
}