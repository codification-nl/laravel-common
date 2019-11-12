<?php

namespace Codification\Common\Validation\Contracts
{
	use Illuminate\Validation\Validator;

	/**
	 * @template TParameters
	 * @template TValue
	 */
	interface ValidatorRule
	{
		/**
		 * @param string                           $attribute
		 * @param mixed                            $value
		 * @psalm-param TValue                     $value
		 * @param string[]                         $parameters
		 * @psalm-param TParameters                $parameters
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