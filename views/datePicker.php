<?php
/**
 * MIT licence
 * Version 1.1.0
 * Sjaak Priester, Amsterdam 04-06-2015.
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
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
$inputId = Html::getInputId($model, $attribute);

setlocale(LC_ALL, Yii::$app->language); // need this for strftime()
$months = [];
for ($m = 1; $m <= 12; $m++) $months[$m] = strftime($widget->monthFormat, mktime(0, 0, 0, $m, 1));

$today = $widget->today;
$todayContent = '';
if (is_string($today)) $today = [
    'content' => $today,
    'class' => 'btn btn-default btn-xs fuzzy-today',
];
if ($today) {
    $today['id'] = $inputId . '-t';
    $todayContent = ArrayHelper::remove($today, 'content', 'Today');
}
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

        <?= JuiDatePicker::widget([
            'model' => $model,
            'attribute' => "{$attribute}[p]",
            'options' => [
                'class' => $widget->controlClass,
                'style' => 'visibility:hidden;width:0;padding-left:0;padding-right:0;border-width:0',
            ],
        ]) ?>

        <?= Html::activeDropDownList($model, "{$attribute}[m]", $months, [
            'prompt' => '',
            'class' => 'fuzzy-month ' . $widget->controlClass,
        ]) ?>

        <?= Html::activeTextInput($model, "{$attribute}[d]", [
            'size' => 2,
            'class' => 'fuzzy-day ' . $widget->controlClass,
        ]) ?>

        <?php if ($today): ?>
        <?= Html::button($todayContent, $today) ?>
        <?php endif; ?>

    </div>
</div>
