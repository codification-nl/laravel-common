<?php

namespace Codification\Common\Validation\Rules
{
	use Carbon\CarbonPeriod;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	/**
	 * @template-implements \Codification\Common\Validation\Contracts\ValidatorRule<list<string>, string>
	 */
	class Period implements ValidatorRule
	{
		/**
		 * @return $this
		 */
		public static function make() : self
		{
			return new static();
		}

		/**
		 * @param string                           $attribute
		 * @param string                           $value
		 * @param string[]                         $parameters
		 * @psalm-param list<string>               $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return bool
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			try
			{
				CarbonPeriod::createFromIso($value);
			}
			catch (\Exception $e)
			{
				return false;
			}

			return true;
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return 'period';
		}
	}
}