<?php
/**
 * @var yii\web\View $this
 * @var common\models\Game $game
 */

use yii\helpers\Markdown;

$this->title = $game->displayName;
$this->params['breadcrumbs'][] = ['label' => 'Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-view-<?= $game->name ?>">
    <?=
    Markdown::process(readfile(Yii::getAlias('@frontend/views/game/details/' . $game->name . '.md', true)));

    ?>
</div>
