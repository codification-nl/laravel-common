<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Validation\Contracts\ValidatorRuleReplacer;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Enum implements ValidatorRule, ValidatorRuleReplacer
	{
		/** @var \Codification\Common\Enums\Enum */
		protected $enum;

		/** @var bool */
		protected $strict;

		/**
		 * @param string|\Codification\Common\Enums\Enum $enum
		 * @param bool                                   $strict = true
		 *
		 * @return $this
		 */
		public static function make(string $enum, bool $strict = true) : self
		{
			$rule = new static();

			$rule->enum   = $enum;
			$rule->strict = $strict;

			return $rule;
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
		 * @param string                           $message
		 * @param string                           $attribute
		 * @param string                           $rule
		 * @param string[]                         $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return string
		 */
		public function replace(string $message, string $attribute, string $rule, array $parameters, Validator $validator) : string
		{
			/** @var \Codification\Common\Enums\Enum $enum */
			[$enum] = $parameters;

			/** @var string $string */
			$string = str_replace(':values', implode(', ', $enum::values()), $message);

			return $string;
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