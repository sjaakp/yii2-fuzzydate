<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 04-06-2015.
 *
 * Model behavior to handle fuzzy dates.
 */

namespace sjaakp\fuzzydate;

use yii\base\Behavior;
use yii\base\Model;
use DateTime;

class FuzzyDateBehavior extends Behavior {

    /**
     * @var array
     * Base names of the fuzzy date attributes.
     * Each attribute is represented by two 'real' attributes, the names of which are formed by appending
     * '1' and '2' to the base name.
     * Example: if $attributes = [ 'born', 'died' ], the model has the attributes 'born1', 'born2', 'died1', and 'died2'
     */
    public $attributes = [];

    /**
     * @var string
     * Format string to format the underlying 'real' dates.
     * Default is MySQL's DATE format.
     */
    public $format = 'Y-m-d';

    // Magic functions to handle attributes.
    public function __get($name)    {
        if (in_array($name, $this->attributes)) {
            /* @var $owner Model; */
            $owner = $this->owner;
            $attr1 = $name . '1';
            $attr2 = $name . '2';
            $d1 = $owner->{$attr1};
            $d2 = $owner->{$attr2};
            if (! $d1 || ! $d2) return null;

            $date1 = DateTime::createFromFormat($this->format, $d1);
            $date2 = DateTime::createFromFormat($this->format, $d2);

            // if years are different, we take the first
            $y1 = $date1->format('Y');
            $r = [
                'y' => $y1
            ];
            $m1 = $date1->format('n');
            if ($y1 == $date2->format('Y') && $m1 == $date2->format('n')) {
                $r['m'] = $m1;

                $d1 = $date1->format('j');
                $r['d'] = $d1 == $date2->format('j') ? $d1 : null;
            }
            else{
                $r['m'] = null;
                $r['d'] = null;
            }

            return $r;

        }
        return parent::__get($name);
    }

    public function __set($name, $value)    {
        if (in_array($name, $this->attributes)) {
            $owner = $this->owner;
            $attr1 = $name . '1';
            $attr2 = $name . '2';

            if (is_array($value))   {
                if (! isset($value['y']) || empty($value['y']))   { // year not set
                    $owner->{$attr1} = $owner->{$attr2} = null;
                }
                else    {
                    $date1 = new DateTime();
                    $date2 = new DateTime();

                    $y = $value['y'];
                    if (isset($value['m']) && ! empty($value['m'])) {
                        $m = $value['m'];

                        if (isset($value['d']) && ! empty($value['d'])) {   // exact date, $date1 = $date2
                            $date1->setDate($y, $m, $value['d']);
                            $date2 = $date1;
                        }
                        else    {   // day not set, $value represents full month
                            $date1->setDate($y, $m, 1);
                            $date2->setDate($y, $m, $date1->format('t'));   // last day of month
                        }
                    }
                    else    {   // month not set, $value represents full year
                        $date1->setDate($y, 1, 1);
                        $date2->setDate($y, 12, 31);
                    }

                    $owner->{$attr1} = $date1->format($this->format);
                    $owner->{$attr2} = $date2->format($this->format);
                }
            }
            else {  // $value not an array
                $owner->{$attr1} = $owner->{$attr2} = null;
            }

        } else {
            parent::__set($name, $value);
        }
    }

    public function canGetProperty($name, $checkVars = true)    {
        if (in_array($name, $this->attributes)) return true;
        return parent::canGetProperty($name, $checkVars);
    }

    public function canSetProperty($name, $checkVars = true)    {
        if (in_array($name, $this->attributes)) return true;
        return parent::canSetProperty($name, $checkVars);
    }
}