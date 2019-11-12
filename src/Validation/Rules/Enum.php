<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Codification\Common\Validation\Contracts\ValidatorRuleReplacer;
	use Illuminate\Validation\Validator;

	/**
	 * @template T of array-key
	 * @template-implements \Codification\Common\Validation\Contracts\ValidatorRule<array{0: class-string<\Codification\Common\Enum\Enum>, 1: string}, T>
	 * @template-implements \Codification\Common\Validation\Contracts\ValidatorRuleReplacer<array{0: class-string<\Codification\Common\Enum\Enum>, 1: string}>
	 */
	class Enum implements ValidatorRule, ValidatorRuleReplacer
	{
		/**
		 * @var string|\Codification\Common\Enum\Enum
		 * @psalm-var class-string<\Codification\Common\Enum\Enum>|null
		 */
		protected $enum = null;

		/** @var bool */
		protected $strict = true;

		/**
		 * @param string|\Codification\Common\Enum\Enum $enum
		 * @psalm-param class-string<\Codification\Common\Enum\Enum> $enum
		 * @param bool                                  $strict = true
		 *
		 * @return static
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
		 * @psalm-param T                          $value
		 * @param string[]                         $parameters
		 * @psalm-param array{0: class-string<\Codification\Common\Enum\Enum>, 1: string} $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			/** @var string|\Codification\Common\Enum\Enum $enum */
			[$enum, $strict] = $parameters;

			return $enum::isValid($value, boolval($strict));
		}

		/**
		 * @param string                           $message
		 * @param string                           $attribute
		 * @param string                           $rule
		 * @param string[]                         $parameters
		 * @psalm-param array{0: class-string<\Codification\Common\Enum\Enum>, 1: string} $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return string
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function replace(string $message, string $attribute, string $rule, array $parameters, Validator $validator) : string
		{
			/** @var string|\Codification\Common\Enum\Enum $enum */
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