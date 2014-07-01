<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Select Opponent';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competition-status">
    <h1><?= Html::encode($this->title) ?></h1>
    <?=
    GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => Column::className(),
                    'header' => 'User',
                    'content' => function ($model) {
                            return $model->user->username;
                        },
                ],
                [
                    'class' => DataColumn::className(),
                    'attribute' => 'rating',
                    'enableSorting' => false,
                ],
                [
                    'class' => Column::className(),
                    'header' => 'Action',
                    'content' => function ($model) {
                            return Html::a(
                                'Compete',
                                ['compete', 'id' => $model->user->id],
                                ['data-method' => 'post']
                            );
                        },
                ],
            ]
        ]
    )
    ?>
</div>
