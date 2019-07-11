<?php

namespace Codification\Common\Validation\Contracts
{
	use Illuminate\Validation\Validator;

	interface ValidatorRule
	{
		/**
		 * @param string                           $attribute
		 * @param mixed                            $value
		 * @param string[]                         $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return bool
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool;

		/**
		 * @return string
		 */
		public function __toString() : string;
	}
}