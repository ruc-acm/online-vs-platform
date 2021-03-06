<?php
/**
 * @var yii\web\View $this
 * @var common\models\ExecutionRecord $model
 * @var $url
 */

$this->title = 'Replay - #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Status', 'url' => ['competition/status']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('js/show_replay.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerCss(<<< EOF
    .board .row {
        margin: 0;
    }
EOF
)
?>
<div class="competition-replay-<?= $model->id ?>">
    <div id="replay-container"></div>
    <div>
        <a href="<?= $url ?>" class="btn btn-danger" style="margin-top: 10px">Go to external replay player.</a>
    </div>
</div>
