<?php

namespace Codification\Common\Validation\Rules
{
	use Carbon\CarbonInterval;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	/**
	 * @template-implements \Codification\Common\Validation\Contracts\ValidatorRule<list<string>, string>
	 */
	class Interval implements ValidatorRule
	{
		/** @var bool */
		protected $allowEmpty = false;

		/**
		 * @param bool $allow_empty = false
		 *
		 * @return $this
		 */
		public static function make(bool $allow_empty = false) : self
		{
			$rule = new static();

			$rule->allowEmpty = $allow_empty;

			return $rule;
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
				$interval = CarbonInterval::fromString($value);
			}
			catch (\Exception $e)
			{
				return false;
			}

			[$allow_empty] = $parameters;

			return (boolval($allow_empty) || !$interval->isEmpty());
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return "interval,{$this->allowEmpty}";
		}
	}
}