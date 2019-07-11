<?php

namespace Codification\Common\Support\Providers
{
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Container\Container;
	use Illuminate\Support\ServiceProvider;

	class CommonServiceProvider extends ServiceProvider
	{
		/** @var \Codification\Common\Validation\Contracts\ValidatorRule[] */
		private $validators = [
			'country'  => \Codification\Common\Validation\Rules\Country::class,
			'enum'     => \Codification\Common\Validation\Rules\Enum::class,
			'interval' => \Codification\Common\Validation\Rules\Interval::class,
			'period'   => \Codification\Common\Validation\Rules\Period::class,
			'phone'    => \Codification\Common\Validation\Rules\Phone::class,
		];

		/**
		 * @return void
		 */
		public function boot() : void
		{
			$this->extendValidator();
		}

		/**
		 * @return void
		 */
		private function extendValidator() : void
		{
			try
			{
				/** @var \Illuminate\Contracts\Validation\Factory $validation_factory */
				$validation_factory = Container::getInstance()->make('validator');
			}
			catch (\Exception $e)
			{
				throw new \RuntimeException('Failed to resolve [Validator] container', 0, $e->getPrevious());
			}

			foreach ($this->validators as $rule => $validator)
			{
				if (in_array(ValidatorRule::class, class_implements($validator)))
				{
					$validation_factory->extend($rule, "{$validator}@validate");
				}
			}
		}
	}
}