<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;

$this->title = 'Games';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?=
    \yii\grid\GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => SerialColumn::className()],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'displayName',
                    'label' => 'Name',
                ],
                [
                    'class' => Column::className(),
                    'header' => 'Action',
                    'content' => function ($model) {
                            return Html::a('View', ['view', 'id' => $model->id]);
                        }
                ]
            ],
        ]
    ) ?>
</div>