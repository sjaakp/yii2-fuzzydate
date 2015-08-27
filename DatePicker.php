<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 04-06-2015.

 * Input widget to handle fuzzy dates. Consists of a spin control for the year, a dropdown list for the month,
 * and a datepicker for the day. Both month and day can be blank.
 */

namespace sjaakp\fuzzydate;

use Yii;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;

class DatePicker extends InputWidget {

    /**
     * @var int
     * Minimum year of the year spin control. If null (default), it is set to the current year,
     */
    public $minYear;

    /**
     * @var int
     * Maximum year of the year spin control. If null (default), it is set to the current year,
     */
    public $maxYear;

    /**
     * @var string
     * Set this to the (Bootstrap) class(es) for the control elements.
     * F.i: for large elements you may set this to 'form-control input-lg'.
     */
    public $controlClass = 'form-control';

    /**
     * @var string
     * Month format of dropdown list
     * '%B' (default) long name, '%b' short month name, '%m' two digits
     */
    public $monthFormat = '%B';


    public function run()   {
        if (! $this->hasModel() || ! $this->attribute)    {
            throw new InvalidConfigException('Fuzzydate DatePicker must have model and attribute.');
        }

        $view = $this->getView();

        $asset = new DatePickerAsset();
        $asset->register($view);

        $view->registerJs("fuzzyReg();");

        $id = Html::getInputId($this->model, $this->attribute);
        $val = Html::getAttributeValue($this->model, $this->attribute);

        if ($val && is_array($val))   {
            $y = $val['y'];
            if ($y) {
                $m = $val['m'];
                if (! $m) $m = 'null';
                $d = $val['d'];
                if (! $d) $d = 'null';

                // Call this after initializing jQuery-objects
                $view->registerJs("fuzzySet('$id', $y, $m, $d);", View::POS_LOAD);
            }
        }

        if (! $this->minYear) $this->minYear = date('Y');
        if (! $this->maxYear) $this->maxYear = date('Y');

        $widget = $this->getViewPath() . DIRECTORY_SEPARATOR . 'datePicker.php';
        echo $view->renderFile($widget, [
            'widget' => $this,
        ]);
    }
}
