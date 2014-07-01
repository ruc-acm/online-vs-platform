<?php
namespace frontend\controllers;

use common\models\ExecutionRecord;
use common\models\UserScore;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

class CompetitionController extends Controller
{

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserScore::find()->where(['gameId' => 1])
                    ->with(['user']),
            'sort' => ['defaultOrder' => ['rating' => SORT_DESC]],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionStatus()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ExecutionRecord::find()->with(['attacker','defender']),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        return $this->render('status', ['dataProvider' => $dataProvider]);
    }

    public function actionCompete($id)
    {
        $user = Yii::$app->user;
        if ($id == $user->getId()) {
            Yii::$app->session->setFlash('error', 'You cannot compete with yourself.');
            return $this->redirect(['index']);
        } else {
            $record = new ExecutionRecord();
            $record->attackerId = $user->getId();
            $record->defenderId = $id;
            $record->status = ExecutionRecord::STATUS_PENDING;
            $record->winner = 0;
            $record->save();
            Yii::$app->session->setFlash('success', 'Your request has been submitted.');
            return $this->redirect(['status']);
        }
    }

    public function actionReplay($id)
    {
        $record = ExecutionRecord::findOne($id);
        if ($record == null) {
            throw new NotFoundHttpException('The specific record could not be found.');
        }
        if ($record->status == ExecutionRecord::STATUS_PENDING || ExecutionRecord::STATUS_RUNNING) {
            Yii::$app->session->setFlash('warning', 'Please come back later');
            return $this->redirect(['status']);
        }
        if ($record->status != ExecutionRecord::STATUS_FINISHED) {
            Yii::$app->session->setFlash('error', 'This execution did not finish.');
            return $this->redirect(['status']);
        }
    }
}