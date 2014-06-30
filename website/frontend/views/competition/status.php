<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
use common\models\ExecutionRecord;
use common\models\User;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Status';
$this->params['breadcrumbs'][] = $this->title;


function getUsernameById($id)
{
    return User::findOne($id)->username;
}

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
                    'class' => DataColumn::className(),
                    'label' => 'Attacker',
                    'attribute' => 'attackerId',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            return getUsernameById($model->attackerId);
                        },
                ],
                [
                    'class' => DataColumn::className(),
                    'label' => 'Defender',
                    'attribute' => 'defenderId',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            return getUsernameById($model->defenderId);
                        },
                ],
                [
                    'class' => DataColumn::className(),
                    'label' => 'Winner',
                    'attribute' => 'winner',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            switch ($model->winner) {
                                case ExecutionRecord::WINNER_ATTACKER:
                                    return 'Attacker';
                                case ExecutionRecord::WINNER_DEFENDER:
                                    return 'Defender';
                                default:
                                    return '-';
                            }
                        },
                ],
                [
                    'class' => DataColumn::className(),
                    'label' => 'Status',
                    'attribute' => 'status',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            switch ($model->status) {
                                case ExecutionRecord::STATUS_PENDING:
                                    return 'Pending';
                                case ExecutionRecord::STATUS_RUNNING:
                                    return 'Running';
                                case ExecutionRecord::STATUS_FINISHED:
                                    return 'Finished';
                                case ExecutionRecord::STATUS_RUNTIME_ERROR:
                                    return 'Runtime Error';
                                case ExecutionRecord::STATUS_TLE:
                                    return 'Time Limit Exceeded';
                                case ExecutionRecord::STATUS_INTERNAL_ERROR:
                                    return 'Internal Error';
                                default:
                                    return '-';
                            }

                        },
                ],
            ]
        ]
    ) ?>
</div>
