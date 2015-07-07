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
?>
<div class="competition-replay-<?= $model->id ?>">
    <a href="<?= $url ?>" class="btn btn-danger">Go to external replay player.</a>
</div>
