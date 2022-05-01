<?php
/**
 * MIT licence
 * Version 1.2.3
 * Sjaak Priester, Amsterdam 04-06-2015 ... 01-05-2022.
 *
 * Formatter class, extends Yii's standard formatter yii\i18n\Formatter.
 * Use this by setting it as component in the configuration file (often config/web.php, or config/main.php) like:
 *
 *      $config = [
 *          // ... lots of other configurations ...
 *          'components' => [
 *              // ... other components ...
 *              [
 *                  'class' => 'sjaakp\fuzzydate\Formatter'
 *              ],
 *              /// ..
 *          ],
 *          // ...
 *      ];
 *      // ...
 *
 * Another way is using:
 *     Yii::$app->set('formatter', 'sjaakp\fuzzydate\Formatter');
 */

namespace sjaakp\fuzzydate;

use yii\base\InvalidArgumentException;
use yii\base\NotSupportedException;
use yii\helpers\FormatConverter;
use IntlDateFormatter;
use DateTime;

class Formatter extends \yii\i18n\Formatter {

    /**
     * @var string|array
     * - if string: 'short', 'medium', 'long', or 'full'. Formatter tries to figure out the formatting of a fuzzy date
     *      based on the formatting of a standard date. With most locales, this works OK, however with some locales
     *      the results will not be correct.
     * - if array: the keys are 'full', 'month', and 'year', corresponding to the granularity of the fuzzy date.
     *      The values are ICU date formats.
     *      Example:
     *          [
     *              'full' => 'MM/dd/yyyy',
     *              'month' => 'MM/yyyy',
     *              'year' => 'yyyy'
     *          ]
     *      @link http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $fuzzyDateFormat = 'medium';

    /**
     * @param $value array - a fuzzy date in array form (having keys 'y', 'm', and 'd').
     * @param null $format - see $fuzzyDateFormat; if null $fuzzyDateFormat is used
     * @return string - the formatted fuzzy date
     * @throws NotSupportedException
     */
    public function asFuzzyDate($value, $format = null)    {
        $_dateFormats = [
            'short'  => IntlDateFormatter::SHORT,
            'medium' => IntlDateFormatter::MEDIUM,
            'long'   => IntlDateFormatter::LONG,
            'full'   => IntlDateFormatter::FULL,
        ];

        if ($value === null) {
            return $this->nullDisplay;
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException('Formatting fuzzy date failed.');
        }
        if (! extension_loaded('intl')) {   // can't use $this->_intlLoaded (private)
            throw new NotSupportedException('Extension \'Intl\' not loaded');
        }

        if (! $format)  {
            $format = $this->fuzzyDateFormat;
        }

        $granularity = 'full';
        if (! isset($value['d']) || empty($value['d'])) {
            $granularity = isset($value['m']) && ! empty($value['m']) ? 'month' : 'year';
        }

        $dt = new DateTime();

        $tokens = null;

        switch ($granularity)   {
            case 'month':
                $dt->setDate($value['y'], $value['m'], 1);
                $tokens = 'cDdEeFWw';   // ICU-tokens having to do with day
               break;
            case 'year':
                $dt->setDate($value['y'], 1, 1);
                $tokens = 'cDdEeFLMWw'; // ICU-tokens having to do with day or month
                break;
            default:    // 'full', finest granularity, use full pattern
                $dt->setDate($value['y'], $value['m'], $value['d']);
                break;
        }

        $formatter = null;

        if (is_array($format))  {
            $pattern = $format[$granularity];
        }
        else    {
            if (isset($_dateFormats[$format]))  {
                $formatter = new IntlDateFormatter($this->locale, $_dateFormats[$format], IntlDateFormatter::NONE);
                $format = $formatter->getPattern();
            }
            else if (strncmp($format, 'php:', 4) === 0) {
                $format = FormatConverter::convertDatePhpToIcu(substr($format, 4));
            }

            if ($tokens)    {
                // use PCRE_UTF8 modifier ('u') for regular expressions
                $pattern = preg_replace("/'[^']+'\\s?[$tokens]+\\S?\\s?|(?:('[^']+')|[$tokens]+\\S?\\s?)/u", '$1', $format);    // remove tokens, possibly
                // with prepended quoted string, possibly with appended non-space and space, unless in single quoted string

                $pattern = preg_replace('/^(\'[^\']*\'\s*)+|(\s*\'[^\']*\')+$|\W$/u', '', $pattern);   // remove (possibly multiple) quoted strings at begin or end, non-word character from end
            }
            else $pattern = $format;
        }
        return $this->asDate($dt, $pattern);
    }
}
