<?php
/**
 * @var yii\web\View $this
 */
use yii\helpers\Html;

$this->title = 'Online Versus Platform';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Online Versus Platform</h1>

        <p class="lead">Yet another way to kill time during the endless summer.</p>

        <p><?= Html::a('Get started', ['intro'], ['class' => 'btn btn-lg btn-primary']); ?></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Games</h2>

                <p>See what you want to play.</p>

                <p><?= Html::a('View Games', ['/game/index'], ['class' => 'btn btn-default']); ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Status</h2>

                <p>See what is going on and watch the replays.</p>

                <p><?= Html::a('View Status', ['/competition/status'], ['class' => 'btn btn-default']); ?></p>
            </div>
            <div class="col-lg-4">
                <h2>My AIs</h2>

                <p>Write an AI and compete.</p>

                <p><?= Html::a('View AIs', ['/program/index'], ['class' => 'btn btn-default']); ?></p>
            </div>
        </div>

    </div>
</div>
