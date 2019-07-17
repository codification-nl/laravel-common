<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Country implements ValidatorRule
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
		 * @param mixed                            $value
		 * @param string[]                         $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return bool
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			return \Codification\Common\Country\Country::isValid($value);
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return 'country';
		}
	}
}