<?php

namespace Codification\Common\Validation\Rules
{
	use Carbon\CarbonPeriod;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Period implements ValidatorRule
	{
		/**
		 * @param string                           $attribute
		 * @param mixed                            $value
		 * @param string[]                         $parameters
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