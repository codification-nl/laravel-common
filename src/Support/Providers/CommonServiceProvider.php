<?php

namespace Codification\Common\Support\Providers
{
	use Codification\Common\Support\CollectionUtils;
	use Codification\Common\Support\ContainerUtils;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Support\Collection;
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
		 * @throws \ReflectionException
		 */
		public function boot() : void
		{
			$this->extendValidator();

			Collection::mixin(new CollectionUtils());
		}

		/**
		 * @return void
		 */
		private function extendValidator() : void
		{
			/** @var \Illuminate\Validation\Factory $factory */
			$factory = ContainerUtils::resolve('validator');

			foreach ($this->validators as $rule => $validator)
			{
				if (in_array(ValidatorRule::class, class_implements($validator)))
				{
					$factory->extend($rule, "{$validator}@validate");
				}
			}
		}
	}
}