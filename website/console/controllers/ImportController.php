<?php
namespace console\controllers;

use common\models\User;
use common\models\UserProfile;
use common\models\UserScore;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionIndex($filename)
    {
        $file = fopen($filename, "r");
        while (!feof($file)) {
            $line = fgetcsv($file);
            if (!empty($line[0]) && !empty($line[1]) && !empty($line[2]) && !empty($line[3])) {
                $this->stderr(implode(',', $line));
                $transaction = \Yii::$app->db->beginTransaction();
                $username = $line[0];
                $nickName = $line[1];
                $class = $line[2];
                $email = $line[3];
                $user = new User();
                $user->username = $username;
                $user->email = $email;
                $user->setPassword($username);
                $user->generateAuthKey();
                $profile = new UserProfile();
                $profile->nickName = $nickName;
                $profile->class = $class;
                $score = new UserScore();
                $score->gameId = 1; //TODO: add multi-game support
                $user->save();
                $profile->link('user', $user);
                $score->link('user', $user);
                $user->link('profile', $profile);
                $user->link('score', $score);
                $transaction->commit();
            }
        }
        return 0;
    }
}