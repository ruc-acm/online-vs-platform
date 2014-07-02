<?php
/**
 * @var yii\web\View $this
 * @var ExecutionRecord $model
 */
use common\models\ExecutionRecord;
use common\models\Program;
use yii\helpers\Html;

$this->title = 'Detail - #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Status', 'url' => ['status']];
$this->params['breadcrumbs'][] = $this->title;

function stabilityBadge($stability)
{
    $class = 'label-default';
    $text = 'dev';
    switch ($stability) {
        case Program::STABILITY_STABLE:
            $class = 'label-success';
            $text = 'stable';
            break;
        case Program::STABILITY_BETA:
            $class = 'label-warning';
            $text = 'beta';
            break;
        case Program::STABILITY_ALPHA:
            $class = 'label-danger';
            $text = 'alpha';
            break;
    }
    return '<span class="label ' . $class . '">' . $text . '</span>';
}

function statusDisplay($model)
{
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

}

?>

<div class="competition-detail">
    <h1><?= Html::encode($this->title) ?></h1>
    <table class="table table-striped table-bordered">
        <tr>
            <td>Run Id</td>
            <td><?= $model->id ?></td>
        </tr>
        <tr>
            <td>Submission Time</td>
            <td><?= $model->submitted ?></td>
        </tr>
        <?php

        ?>
        <tr>
            <td>Attacker Program</td>
            <td>
                <?= $model->attacker->name ?>
                <?= stabilityBadge($model->attacker->stability) ?>
            </td>
        </tr>
        <tr>
            <td>Attacker Program Author</td>
            <td><?= $model->attacker->user->profile->nickName ?>(<?= $model->attacker->user->username ?>)</td>
        </tr>
        <tr>
            <td>Attacker Program Submission Time</td>
            <td><?= $model->attacker->lastCreated ?></td>
        </tr>
        <tr>
            <td>Defender Program</td>
            <td>
                <?= $model->defender->name ?>
                <?= stabilityBadge($model->defender->stability) ?>
            </td>

        </tr>
        <tr>
            <td>Defender Program Author</td>
            <td><?= $model->defender->user->profile->nickName ?>(<?= $model->defender->user->username ?>)</td>
        </tr>
        <tr>
            <td>Defender Program Submission Time</td>
            <td><?= $model->defender->lastCreated ?></td>
        </tr>
        <tr>
            <td>Winner</td>
            <td>
                <b>
                    <?php switch ($model->winner) {
                        case ExecutionRecord::WINNER_ATTACKER:
                            echo 'Attacker';
                            break;
                        case ExecutionRecord::WINNER_DEFENDER:
                            echo 'Defender';
                            break;
                        default:
                            echo '-';
                    }
                    ?>
                </b></td>
        </tr>
        <tr>
            <td>Verdict</td>
            <td><?= statusDisplay($model) ?></td>
        </tr>
    </table>
<pre>
<?= $model->log ?>
</pre>