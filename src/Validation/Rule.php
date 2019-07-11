<?php

namespace Codification\Common\Validation
{
	use Codification\Common\Validation\Contracts\ValidatorRule;

	final class Rule
	{
		/**
		 * @return \Codification\Common\Validation\Rules\Country
		 */
		public static function country() : Rules\Country
		{
			return Rules\Country::make();
		}

		/**
		 * @param string|\Codification\Common\Support\Enum $enum
		 * @param bool                                     $strict
		 *
		 * @return \Codification\Common\Validation\Rules\Enum
		 */
		public static function enum(string $enum, bool $strict = true) : Rules\Enum
		{
			return Rules\Enum::make($enum, $strict);
		}

		/**
		 * @param bool $allow_empty
		 *
		 * @return \Codification\Common\Validation\Rules\Interval
		 */
		public static function interval(bool $allow_empty = false) : Rules\Interval
		{
			return Rules\Interval::make($allow_empty);
		}

		/**
		 * @return \Codification\Common\Validation\Rules\Period
		 */
		public static function period() : ValidatorRule
		{
			return Rules\Period::make();
		}

		/**
		 * @param string|null $country_field
		 *
		 * @return \Codification\Common\Validation\Rules\Phone
		 */
		public static function phone(string $country_field = null) : ValidatorRule
		{
			return Rules\Phone::make($country_field);
		}
	}
}