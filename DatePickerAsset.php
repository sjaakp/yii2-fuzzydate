<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 04-06-2015.
 */

namespace sjaakp\fuzzydate;

use yii\web\AssetBundle;

class DatePickerAsset extends AssetBundle {
    public $depends = [
        'yii\jui\JuiAsset',
    ];

    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';

    public $css = [
        'fuzzy.css'
    ];

    public function init()    {
        parent::init();

        $this->js[] = YII_DEBUG ? 'fuzzy_assist.js' : 'fuzzy_assist.min.js';
        $this->publishOptions['forceCopy'] = YII_DEBUG;
    }
}