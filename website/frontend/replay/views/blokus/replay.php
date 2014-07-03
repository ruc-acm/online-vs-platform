<?php
/**
 * @var yii\web\View $this
 * @var common\models\ExecutionRecord $model
 */

$this->title = 'Replay - #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Status', 'url' => ['competition/status']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competition-replay-<?= $model->id ?>">
<h1>Warning</h1>
<p>The page you are going to visit may contain certain content that is not suitable for you to view. And because of the fact that the destination page is outside this website, we have no control with the page.</p>
<p>If</p>
</div>
