<?php


namespace frontend\replay;


use yii\helpers\Json;
use yii\web\Response;
use Yii;

class BlokusReplayHandler extends BaseReplayHandler
{

    /**
     * @inheritdoc
     */
    public function translateReplay($record)
    {
        $replay = explode(PHP_EOL, $record->replay);
        $output = [];
        $output[] = [
            'name' => [
                $record->attacker->user->profile->nickName,
                $record->defender->user->profile->nickName,
                $record->attacker->user->profile->nickName,
                $record->defender->user->profile->nickName,
            ]
        ];
        foreach ($replay as $raw_line) {
            $line = trim($raw_line);
            if (!empty($line)) {
                $item = explode(' ', $line);
                $output[] = [
                    'color' => (int) $item[0],
                    'chess' => (int) $item[1],
                    'x' => (int) $item[2],
                    'y' => (int) $item[3],
                    'rotate' => (int) $item[4],
                    'flipX' => (int) $item[5],
                    'flipY' => (int) $item[6],
                ];
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $output;
    }

    /**
     * @inheritdoc
     */
    public function handleReplay($record)
    {
    }
}