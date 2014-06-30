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
                    'name:ntext',
                    [
                        'class' => DataColumn::className(),
                        'label' => 'Stability',
                        'attribute' => 'stability',
                        'enableSorting' => true,
                        'content' => function ($model) {
                                switch ($model->stability) {
                                    case Program::STABILITY_STABLE:
                                        return 'Stable';
                                    case Program::STABILITY_BETA:
                                        return 'Beta';
                                    case Program::STABILITY_ALPHA;
                                        return 'Alpha';
                                    default:
                                        return 'Development';
                                }
                            },
                    ],
                    'lastCreated',
                ],
            ]
        ); ?>
    <?php endif ?>
    <p><?= Html::a('Upload New AI', ['upload'], ['class' => 'btn btn-primary']) ?> </p>
</div>