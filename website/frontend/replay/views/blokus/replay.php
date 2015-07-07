<?php
/**
 * @var yii\web\View $this
 * @var common\models\ExecutionRecord $model
 * @var $url
 */

$this->title = 'Replay - #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Status', 'url' => ['competition/status']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competition-replay-<?= $model->id ?>">
    <h1>Warning</h1>

    <p>The page you are going to visit may contain certain content that is not suitable for you to view. And because of
        the fact that the destination page is outside this website, we have no control with it.</p>

    <p>If you really want to proceed, please click the button below.</p>
    <a href="<?= $url ?>" class="btn btn-danger">I understand, please let me see it.</a>
    <button onclick="history.back();" class="btn btn-primary">No, let me back.</button>
</div>
<script src="js/show_replay.js" ></script>