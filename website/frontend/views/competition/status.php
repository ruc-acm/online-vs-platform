<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */
use common\models\ExecutionRecord;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Status';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="competition-status">
    <h1><?= Html::encode($this->title) ?></h1>
    <?=
    GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => DataColumn::className(),
                    'label' => 'Run ID',
                    'attribute' => 'id',
                    'enableSorting' => false,
                ],
                [
                    'class' => DataColumn::className(),
                    'label' => 'Attacker',
                    'attribute' => 'attackerId',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            return $model->attacker->user->profile->nickName;
                        },
                ],
                [
                    'class' => DataColumn::className(),
                    'label' => 'Defender',
                    'attribute' => 'defenderId',
                    'enableSorting' => false,
                    'content' => function ($model) {
                            return $model->defender->user->profile->nickName;
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
                                    return '<span style="color: #00CCFF">Pending</span>';
                                case ExecutionRecord::STATUS_RUNNING:
                                    return '<span style="color: #faff43">Running</span>';
                                case ExecutionRecord::STATUS_FINISHED:
                                    return '<span style="color: #009900">Finished</span>';
                                case ExecutionRecord::STATUS_RUNTIME_ERROR:
                                    return '<span style="color: #bb0000">Runtime Error</span>';
                                case ExecutionRecord::STATUS_TLE:
                                    return '<span style="color: #bb065c">Time Limit Exceeded</span>';
                                case ExecutionRecord::STATUS_BAD_FORMAT:
                                    return '<span style="color: #a45c00">Bad Output</span>';
                                case ExecutionRecord::STATUS_ILLEGAL_MOVE:
                                    return '<span style="color: #8e5866">Illegal Movement</span>';
                                case ExecutionRecord::STATUS_INTERNAL_ERROR:
                                    return '<span style="color: #233333">Internal Error</span>';
                                case ExecutionRecord::STATUS_COMPILE_ERROR:
                                    return '<span style="color: #ff5410;">Compile Error</span>';
                                default:
                                    return '-';
                            }

                        },
                ],
                [
                    'class' => Column::className(),
                    'header' => 'Actions',
                    'content' =>
                        function ($model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-play"></span> Replay',
                                ['replay', 'id' => $model->id],
                                ['class' => 'btn btn-default btn-xs']
                            ) . '' . Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span> Detail',
                                ['detail', 'id' => $model->id],
                                ['class' => 'btn btn-default btn-xs']
                            );
                        }
                ]
            ]
        ]
    ) ?>
</div>
