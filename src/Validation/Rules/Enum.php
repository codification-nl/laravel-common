<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Enum implements ValidatorRule
	{
		/** @var \Codification\Common\Support\Enum */
		protected $enum;

		/** @var bool */
		protected $strict;

		/**
		 * @param string|\Codification\Common\Support\Enum $enum
		 * @param bool                                     $strict
		 */
		private function __construct(string $enum, bool $strict = true)
		{
			$this->enum   = $enum;
			$this->strict = $strict;
		}

		/**
		 * @param string|\Codification\Common\Support\Enum $enum
		 * @param bool                                     $strict
		 *
		 * @return $this
		 */
		public static function make(string $enum, bool $strict = true) : self
		{
			return new static($enum, $strict);
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
			/** @var \Codification\Common\Support\Enum $enum */
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