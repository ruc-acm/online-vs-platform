<?php

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
use common\models\Program;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'My AI';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (!$dataProvider->query->count()): ?>
        <p>You don't have any AI programs uploaded. Try upload one.</p>
    <?php else: ?>
        <?=
        GridView::widget(
            [
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'class' => DataColumn::className(),
                        'label' => 'Name',
                        'attribute' => 'name',
                        'enableSorting' => true,
                        'content' => function ($model) {
                                return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                            },
                    ],
                    [
                        'class' => DataColumn::className(),
                        'label' => 'Stability',
                        'attribute' => 'stability',
                        'enableSorting' => true,
                        'content' => function ($model) {
                                $class = 'default';
                                $text = 'dev';
                                switch ($model->stability) {
                                    case Program::STABILITY_STABLE:
                                        $class = 'success';
                                        $text = 'stable';
                                        break;
                                    case Program::STABILITY_BETA:
                                        $class = 'warning';
                                        $text = 'beta';
                                        break;
                                    case Program::STABILITY_ALPHA:
                                        $class = 'danger';
                                        $text = 'alpha';
                                        break;
                                }
                                return Html::a($text, ['stability', 'id' => $model->id], ['class' => "btn btn-$class btn-xs"]);
                            },
                    ],
                    'lastCreated',
                ],
            ]
        ); ?>
    <?php endif ?>
    <p><?= Html::a('Upload New AI', ['upload'], ['class' => 'btn btn-primary']) ?> </p>
</div>