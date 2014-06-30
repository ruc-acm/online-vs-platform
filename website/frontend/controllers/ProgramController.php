<?php


namespace frontend\controllers;


use common\models\Program;
use common\models\SourceCode;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ProgramController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'submit', 'view'],
                'rules' => [
                    [
                        'actions' => ['index', 'submit', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->user->identity->getPrograms(),
        ]);

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
            ]
        );

    }

    public function actionUpload()
    {
        $program = new Program();
        $code = new SourceCode();
        /* default values for uploading only */
        $program->stability = Program::STABILITY_DEVELOPMENT;
        $code->language = SourceCode::LANGUAGE_CPP;
        $program->userId = Yii::$app->user->identity->getId();
        $program->gameId = 1; // TODO: multi-game support
        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($program->load(Yii::$app->request->post()) && $program->save()) {

                $code->programId = $program->id;
                if ($code->load(Yii::$app->request->post()) && $code->save()) {
                    $transaction->commit();
                    return $this->redirect(['index']);
                }
            }
            $transaction->rollBack();
        }
        return $this->render('upload', ['program' => $program, 'code' => $code]);
    }

    public function actionView($id)
    {
        $program = Program::find($id)->with('sourceCode')->one();
        if ($program == null) {
            throw new NotFoundHttpException();
        }
        if ($program->userId != Yii::$app->user->identity->getId()) {
            throw new ForbiddenHttpException();
        }
        return $this->render('view', ['model' => $program]);
    }
} 