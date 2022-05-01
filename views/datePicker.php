<?php
/**
 * MIT licence
 * Version 1.2.0
 * Sjaak Priester, Amsterdam 04-06-2015... 24-04-2019.
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\jui\DatePicker as JuiDatePicker;
use sjaakp\fuzzydate\DatePicker;

/**
 * @var $this View
 * @var $widget DatePicker
 */

$model = $widget->model;
$attribute = $widget->attribute;
$inputId = Html::getInputId($model, $attribute);

$months = [];
$di = new DateInterval('P1M');
$dt = new DateTime('1/1/2000'); // just a random year, we're only interested in month names

for ($m = 1; $m <= 12; $m++) {
    $months[$m] = Yii::$app->formatter->asDate($dt, $widget->monthFormat);
    $dt->add($di);
}

$today = $widget->today;
$todayContent = '';
if (is_string($today)) $today = [
    'content' => $today,
    'class' => 'btn btn-secondary btn-sm fuzzy-today',
];
if ($today) {
    $today['id'] = $inputId . '-t';
    $todayContent = ArrayHelper::remove($today, 'content', 'Today');
}
?>
<div class="form-group">
    <div class="form-inline flex-nowrap">
        <?= Html::activeInput('number', $model, "{$attribute}[y]", [
            'class' => 'fuzzy-year ' . $widget->controlClass,
            'size' => 4,
            'min' => $widget->minYear,
            'max' => $widget->maxYear
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
            'encode' => false
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
