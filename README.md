Yii2 Fuzzydate
============

Often, when working with dates, data are incomplete. For instance, one might know that a person is born in 1980, or in April, 1980, but not on which day exactly.

I call these dates *fuzzy dates*. Here are a few classes to work with them in the Yii 2.x PHP framework.

## Fuzzy date representation ##

### In the database ###

In the database each fuzzy date is represented by **two** 'normal' dates. The field names of the two dates are derived from the attribute name of the fuzzy date, by appending `'1'` and `'2'` respectively. 


So, if the attribute name of the fuzzy date is `'born'`, the fields representing is in the database would be `'born1'` and `'born2'`.

- If we know the date exactly, the value of both date fields is equal.
- If we know the date with a 'granularity' of one month, the value of the first field is the first day of the month, the value of the second field is the last day of the month.
- If we only know the year, the value of the first field is the first day of the year, the value of the second field is the last day of the year.

In other words, given a fuzzy date `'xyz'`, the range of possible 'real' dates is given by `'xyz1'` and `'xyz2'`.

For instance, the exact date of December 3, 2008 (the day Yii was launched, by the way) would be represented by (assuming MySQL format):

    born1 = "2008-12-03"
    born2 = "2008-12-03"

If the date was December, 2008, we would have:

    born1 = "2008-12-01"
    born2 = "2008-12-31"

And, lastly, if the date was 2008:

    born1 = "2008-01-01"
    born2 = "2008-12-31"

### In PHP ###

In PHP, a fuzzy date is represented by a three member array, where the unknown values are set to `null`. Like so:

    <?php
		
		// December 3, 2008
		$born = [
			'y' => 2008,
			'm' => 12,
			'd' => 3
		];
		
		// December, 2008
		$born = [
			'y' => 2008,
			'm' => 12,
			'd' => null
		];
		
		// 2008
		$born = [
			'y' => 2008,
			'm' => null,
			'd' => null
		];

## Yii2 Fuzzydate classes ##

**Yii2 Fuzzydate** consists of three classes to work with fuzzy dates:

- **FuzzyDateBehavior** Model/ActiveRecord behavior that converts from database to PHP format, and vice versa;
- **DatePicker** Input widget for a fuzzy date;
- **Formatter** Extends from `yii\i18n\Formatter`, and adds a fuzzy date formatting function.

**DatePickerAsset** is a helper class for **DatePicker**.

A demonstration of the **Yii2 Fuzzydate** suit is [here](http://www.sjaakpriester.nl/software/fuzzydate).

## Installation ##

The preferred way to install **Yii2 Fuzzydate** is through [Composer](https://getcomposer.org/). Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-fuzzydate": "*"` 

Or run:

`composer require sjaakp/yii2-fuzzydate "*"` 

You can manually install **Yii2 Fuzzydate** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-fuzzydate/archive/master.zip).

## FuzzyDateBehavior ##

A class `Hero` with fuzzy date attributes `'born'` and `'died'` should have database fields `'born1'`, `'born2'`, `'died1'`, and `'died2'`. It should be set up like this:

	<?php

	namespace app\models;

	use sjaakp\fuzzydate\FuzzyDateBehavior;

	class Hero extends ActiveRecord    {

        public function rules()    {
	        return [
	            [['born', 'died'], 'safe'],
				// ...
	        ];
	    }

    	public function behaviors()
    	{
	        return [
	            'fuzzydate' => [
	                'class' => FuzzyDateBehavior::class,
					'attributes'=> [
						'born',
						'died'
					]
	            ]
	        ];
	    }
		// ...
	}

After that, class `Hero` has two 'virtual attributes', `'born'`, and `'died'`.

**Notice:** Don't forget to declare the attributes *safe* in the method `rules()`.

The behavior adds virtual fuzzy date attributes to the model class, which can be read from and written to like normal attributes. The underlying attributes (like `'born1'`, `'born2'`) are accessible as well.

### Attributes ###

#### $attributes ####

Array of names of the fuzzy date attributes.

#### $format ####

Format string to format the underlying 'real' dates with PHP DateTime.
Default is MySQL's DATE format: `'Y-m-d'`. If you use MySQL, there is no reason to change this.

## Formatter ##

This class extends Yii's standard formatter [**yii\i18n\Formatter**](http://www.yiiframework.com/doc-2.0/yii-i18n-formatter.html "Yii 2.0 API").

Use this by setting it as component in the application's  configuration file (often `config/web.php` or `config/main.php`) like:

	<?php
	
	  // ...
      $config = [
          // ... lots of other configurations ...

          'components' => [
              // ... other components ...
              'formatter' => [
                  'class' => 'sjaakp\fuzzydate\Formatter'
              ],
              /// ..
          ],

          // ...
      ];
      // ...

Another way is using:

	<?php

    Yii::$app->set('formatter', 'sjaakp\fuzzydate\Formatter');

After that, `'fuzzyDate'` is just another formatting option, like `'text'` or `'html'`. You can use it in **GridView**, **ListView** or **DetailView** in the following way:

	<?php

	<?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
	        'date:fuzzyDate',
	        // ...
	    ],
	]) ?>

Or, you can format a fuzzy date using code like this:

	<?php

	$formattedFuzzyDate = Yii::$app->formatter->asFuzzyDate($model->date, 'full');

You get the same result with:

	<?php

	$formattedFuzzyDate = Yii::$app->formatter->format($model->date, [ 'fuzzyDate', 'full' ]);

The text `'fuzzyDate'` is case independent. `'fuzzydate'` works as well.

### Attributes ###

#### $fuzzyDateFormat ####

- if string: `'short'`, `'medium'`, `'long'`, or `'full'`. **Formatter** tries to figure out the formatting of a fuzzy date based on the formatting of a standard date. With most locales, this works OK, however with some locales the results are less satisfying.
- if array: the keys are `'full'`, `'month'`, and `'year'`, corresponding to the granularity of the fuzzy date. The values are [ICU date formats](http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax "ICU user guide"). Example:

    	[
    		'full' => 'MM/dd/yyyy',
        	'month' => 'MM/yyyy',
        	'year' => 'yyyy'
    	]

The default value for **$fuzzyDateFormat** is `'medium'`.

### Methods ###

#### asFuzzyDate ####

	public function asFuzzyDate($value, $format = null)

Formats a fuzzy date, as delivered by a Model with a **FuzzyDateBehavior**.

 - **$value**: the fuzzy date to format;
 - **$format**: can have the same values as **$fuzzyDateFormat**; if `null` (default), `$fuzzyDateFormat` is used;
 - **return**: the formatted fuzzy date.

## DatePicker ##

This is an input widget to handle fuzzy dates. It consists of a spin control for the year, a dropdown list for the month, a datepicker for the day, and optionally a 'Today'-button. Both month and day can be blank, indicating an incomplete date.

**DatePicker** has all the attributes and methods of an [**InputWidget**](http://www.yiiframework.com/doc-2.0/yii-widgets-inputwidget.html "Yii 2.0 API") plus the following.

### Attributes ###

#### $minYear, $maxYear ####

Minimum and maximum year of the year spin control. If `null` (default), it is set to the current year. You would normally set at least one of these values to something other than `null`.

#### $controlClass ####

Set this to the (*Bootstrap*) class(es) for the control elements. Example: for large elements you may set this to `'form-control input-lg'`. Default is `'form-control'`.

#### $monthFormat ####

Sets the format of the month names in the dropdown list.

- `'%B'` (default) long name
- `'%b'` short name
- `'%m'` two digits

These are format strings from PHP's [strftime()](http://php.net/manual/en/function.strftime.php "PHP API") function.

#### $today ####

Options for the 'Today'-button.

- If `string`: the text for the 'Today'-button. This will not be HTML-encoded before it's rendered.
- If `array`: HTML options for the 'Today'-button. The arry value with the key `'content'` will be displayed as text and will not be HTML-encoded; other options will.
- If `null`: no 'Today'-button is rendered.

Default: `'Today'`.

### Usage ###

Use **DatePicker** like any other InputWidget. Example:

	<?= $form->field($model, 'date')->widget(DatePicker::className(), [
	    'minYear' => 1970,
	]) ?>

