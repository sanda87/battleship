<?php
use app\helpers\PlayFieldHelper;
use yii\bootstrap\Html;

/*
 * @var $this yii\web\View
 */

$this->title = 'Battleship';
?>
<div class="play-field">

    <h1 class="text-center">ID комбинации: <?= Html::a($play_field->id, ['site/index', 'id' => $play_field->id]) ?></h1>
    <p class="text-center"><?= Html::a("Сгенерировать новую комбинацию", ['site/index']) ?></p>
    <?php for ($y = 0; $y < Yii::$app->params['play_field']['size_y']; $y++): ?>
        <div class="row">
            <div class="col-xs-1"></div>
            <?php for ($x = 0; $x < Yii::$app->params['play_field']['size_x']; $x++): ?>
                <div class="cell col-xs-1 <?php if (PlayFieldHelper::isFilledPoint($play_field->filled_points, $x, $y)) echo "active" ?>  "></div>
            <?php endfor; ?>
            <div class="col-xs-1"></div>
        </div>
    <?php endfor; ?>
</div>