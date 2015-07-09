<?php
namespace console\controllers;

use common\models\ExecutionRecord;
use common\models\User;
use yii\console\Controller;
use yii\redis\Connection;

class ContestController extends Controller
{
    public function actionClass($class, $tag = null)
    {
        if ($tag == null)
            $tag = 'class-' . $class;
        $users = User::find()->joinWith('profile')->where(['userProfile.class' => $class])->joinWith('programs', false, 'INNER JOIN')->all();
        $this->stderr('The following is the list of class ' . $class . PHP_EOL);
        foreach ($users as $user) {
            $this->stderr($user->username . '(' . $user->profile->nickName . ')' . PHP_EOL);
        }
        if (empty($users) || !$this->confirm('Proceed?')) {
            $this->stderr('Aborted.' . PHP_EOL);
            return 1;
        }
        $redis = new Connection();
        $redis->open();
        foreach ($users as $attackerUser) {
            foreach ($users as $defenderUser) {
                if ($attackerUser->id == $defenderUser->id)
                    continue;
                $attacker = $attackerUser->getFlagshipProgram();
                $defender = $defenderUser->getFlagshipProgram();
                $record = new ExecutionRecord();
                $record->attackerId = $attacker->id;
                $record->defenderId = $defender->id;
                $record->status = ExecutionRecord::STATUS_PENDING;
                $record->winner = 0;
                $record->tag = $tag;
                $record->save();
                $redis->executeCommand('RPUSH', ['judge_queue', $record->id]);
                $this->stderr('Added: ' . $attackerUser->username . ' vs ' . $defenderUser->username . PHP_EOL);
            }
        }
        $redis->close();
        return 0;
    }
}