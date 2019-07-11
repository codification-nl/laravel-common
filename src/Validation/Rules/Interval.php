<?php

namespace Codification\Common\Validation\Rules
{
	use Carbon\CarbonInterval;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Interval implements ValidatorRule
	{
		/** @var bool */
		protected $allowEmpty;

		public function __construct(bool $allow_empty = false)
		{
			$this->allowEmpty = $allow_empty;
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
			try
			{
				$interval = CarbonInterval::create($value);
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