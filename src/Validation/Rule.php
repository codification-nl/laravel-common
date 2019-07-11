<?php

namespace Codification\Common\Validation
{
	final class Rule
	{
		/**
		 * @return \Codification\Common\Validation\Rules\Country
		 */
		public static function country() : Rules\Country
		{
			return new Rules\Country();
		}

		/**
		 * @param string|\Codification\Common\Support\Enum $enum
		 * @param bool                                     $strict
		 *
		 * @return \Codification\Common\Validation\Rules\Enum
		 */
		public static function enum(string $enum, bool $strict = true) : Rules\Enum
		{
			return new Rules\Enum($enum, $strict);
		}

		/**
		 * @param bool $allow_empty
		 *
		 * @return \Codification\Common\Validation\Rules\Interval
		 */
		public static function interval(bool $allow_empty = false) : Rules\Interval
		{
			return new Rules\Interval($allow_empty);
		}

		/**
		 * @return \Codification\Common\Validation\Rules\Period
		 */
		public static function period() : Rules\Period
		{
			return new Rules\Period();
		}

		/**
		 * @param string|null $country_field
		 *
		 * @return \Codification\Common\Validation\Rules\Phone
		 */
		public static function phone(string $country_field = null) : Rules\Phone
		{
			return new Rules\Phone($country_field);
		}
	}
}