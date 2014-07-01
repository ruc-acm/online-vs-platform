<?php


namespace frontend\controllers;


use common\models\Game;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GameController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => Game::find()
            ]
        );
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id)
    {
        $game = Game::findOne($id);
        if ($game == null) {
            throw new NotFoundHttpException();
        }
        return $this->render('view', ['game' => $game]);
    }
} 