<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 04-06-2015.
 */

use yii\helpers\Html;
use yii\web\View;
use yii\jui\Spinner;
use yii\jui\DatePicker as JuiDatePicker;
use sjaakp\fuzzydate\DatePicker;

/**
 * @var $this View
 * @var $widget DatePicker
 */

$model = $widget->model;
$attribute = $widget->attribute;

setlocale(LC_ALL, Yii::$app->language); // need this for strftime()
$months = [];
for ($m = 1; $m <= 12; $m++) $months[$m] = strftime($widget->monthFormat, mktime(0, 0, 0, $m, 1));
?>

<div class="form-group">
    <div class="form-inline">
        <?= Spinner::widget([
            'model' => $model,
            'attribute' => "{$attribute}[y]",
            'options' => [
                'class' => 'fuzzy-year ' . $widget->controlClass,
                'size' => 4
            ],
            'clientOptions' => [
                'min' => $widget->minYear,
                'max' => $widget->maxYear
            ],
        ]) ?>
        <?= Html::activeDropDownList($model, "{$attribute}[m]", $months, [
            'prompt' => '',
            'class' => 'fuzzy-month ' . $widget->controlClass,
        ]) ?>
        <?= JuiDatePicker::widget([
            'model' => $model,
            'attribute' => "{$attribute}[d]",
            'dateFormat' => 'd',
            'options' => [
                'class' => 'fuzzy-day ' . $widget->controlClass,
                'size' => 2,
            ],
            'clientOptions' => [
                'hideIfNoPrevNext' => true,
            ],
        ]) ?>
    </div>
</div>
