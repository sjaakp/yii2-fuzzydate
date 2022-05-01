<?php
/**
 * MIT licence
 * Version 1.2.3
 * Sjaak Priester, Amsterdam 04-06-2015...01-05-2022.

 * Input widget to handle fuzzy dates. Consists of a spin control for the year, a dropdown list for the month,
 * and a datepicker for the day. Both month and day can be blank.
 */

namespace sjaakp\fuzzydate;

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
     * 'LLLL' long name, 'LLL' short month name, 'LL' two digits ('stand-alone month in year')
     * @link https://unicode-org.github.io/icu/userguide/format_parse/datetime/
     */
    public $monthFormat = 'LLLL';

    /**
     * @var string | array | null
     * - If string: text for 'Today' button, will not be HTML encoded
     * - If array: HTML options for button. Member with key 'content' is button text, will not be HTML encoded; other options will
     * - If null, no 'Today'-button is displayed
     */
    public $today = 'Today';

    /**
     * @return string|void
     * @throws InvalidConfigException
     */
    public function run()   {
        if (! $this->hasModel() || ! $this->attribute)    {
            throw new InvalidConfigException('Fuzzydate DatePicker must have model and attribute.');
        }

        $view = $this->getView();

        $asset = new DatePickerAsset();
        $asset->register($view);

        $view->registerJs("fuzzyReg();");

        if (! $this->minYear) $this->minYear = date('Y');
        if (! $this->maxYear) $this->maxYear = date('Y');

        $widget = $this->getViewPath() . DIRECTORY_SEPARATOR . 'datePicker.php';
        echo $view->renderFile($widget, [
            'widget' => $this,
        ]);
    }
}
