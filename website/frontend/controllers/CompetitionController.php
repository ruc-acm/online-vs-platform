<?php
namespace frontend\controllers;

use common\models\ExecutionRecord;
use common\models\User;
use common\models\UserScore;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\redis\Connection;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;
use yii\data\ActiveDataProvider;

class CompetitionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['compete'],
                'rules' => [
                    [
                        'actions' => ['compete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'compete' => ['post'],
                ],
            ],
        ];
    }

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
            'query' => ExecutionRecord::find()->with(['attacker.user.profile', 'defender.user.profile']),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        return $this->render('status', ['dataProvider' => $dataProvider]);
    }

    public function actionCompete($id)
    {
        $attackerUser = Yii::$app->user->identity;
        $defenderUser = User::findOne($id);
        if ($defenderUser == null) {
            throw new NotFoundHttpException('Specific user cannot be found.');
        }
        if ($id == $attackerUser->getId()) {
            Yii::$app->session->setFlash('error', 'You cannot compete with yourself.');
            return $this->redirect(['index']);
        } else {
            $record = new ExecutionRecord();
            if (($attacker = $attackerUser->getLatestProgram()) == null) {
                Yii::$app->session->setFlash('error', 'You has not uploaded any AI yet.');
                return $this->redirect(['index']);
            }
            if (($defender = $defenderUser->getFlagshipProgram()) == null) {
                Yii::$app->session->setFlash('error', 'Your opponent has not uploaded any AI yet.');
                return $this->redirect(['index']);
            }
            $record->attackerId = $attacker->id;
            $record->defenderId = $defender->id;
            $record->status = ExecutionRecord::STATUS_PENDING;
            $record->winner = 0;
            $record->save();
            $redis = new Connection();
            $redis->open();
            $redis->executeCommand('RPUSH', ['judge_queue', $record->id]);
            $redis->close();
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
        if ($record->status == ExecutionRecord::STATUS_PENDING || $record->status == ExecutionRecord::STATUS_RUNNING) {
            Yii::$app->session->setFlash('warning', 'Please come back later');
            return $this->redirect(['status']);
        }
        if ($record->status != ExecutionRecord::STATUS_FINISHED) {
            Yii::$app->session->setFlash('error', 'This execution did not finish.');
            return $this->redirect(['status']);
        }
    }

    public function actionDetail($id)
    {
        $record = ExecutionRecord::findOne($id);
        if ($record == null) {
            throw new NotFoundHttpException('The specific record could not be found.');
        }
        if ($record->status == ExecutionRecord::STATUS_PENDING || $record->status == ExecutionRecord::STATUS_RUNNING) {
            Yii::$app->session->setFlash('warning', 'Please come back later');
            return $this->redirect(['status']);
        }
        return $this->render('detail', ['model' => $record]);
    }
}