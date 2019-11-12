<?php

namespace Codification\Common\Validation\Contracts
{
	use Illuminate\Validation\Validator;

	/**
	 * @template TParameters
	 */
	interface ValidatorRuleReplacer
	{
		/**
		 * @param string                           $message
		 * @param string                           $attribute
		 * @param string                           $rule
		 * @param string[]                         $parameters
		 * @psalm-param TParameters                $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return string
		 */
		public function replace(string $message, string $attribute, string $rule, array $parameters, Validator $validator) : string;
	}
}