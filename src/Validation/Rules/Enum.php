<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Enum implements ValidatorRule
	{
		/** @var \Codification\Common\Enums\Enum */
		protected $enum;

		/** @var bool */
		protected $strict;

		/**
		 * @param string|\Codification\Common\Enums\Enum $enum
		 * @param bool                                   $strict
		 */
		public function __construct(string $enum, bool $strict = true)
		{
			$this->enum   = $enum;
			$this->strict = $strict;
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
			/** @var \Codification\Common\Enums\Enum $enum */
			[$enum, $strict] = $parameters;

			return $enum::isValid($value, boolval($strict));
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return "enum:{$this->enum},{$this->strict}";
		}
	}
}