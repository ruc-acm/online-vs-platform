<?php

/**
 * @var yii\web\View $this
 * @var common\models\Program $model
 */
use common\models\Program;
use common\models\SourceCode;
use yii\helpers\Html;

$this->title = 'View AI - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'My AI', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">
    <h1>
        <?= Html::encode($model->name); ?>
    </h1>
    <?php
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
    ?>
    <table class="table table-striped table-bordered">
        <tr>
            <td>Stability</td>
            <td><?= Html::a($text, ['stability', 'id' => $model->id], ['class' => "btn btn-$class btn-xs"]); ?></td>
        </tr>
        <tr>
            <td>Language</td>
            <td>
                <?php
                $language = 'Unknown';
                switch ($model->sourceCode->language) {
                    case SourceCode::LANGUAGE_C:
                        $language = 'C';
                        break;
                    case SourceCode::LANGUAGE_CPP:
                        $language = 'C++';
                        break;
                }
                ?>
                <?= $language ?>
            </td>
        </tr>
        <tr>
            <td>Created On</td>
            <td><?= $model->lastCreated ?></td>
        </tr>
    </table>
    <pre><?= Html::encode($model->sourceCode->code) ?></pre>
</div>
