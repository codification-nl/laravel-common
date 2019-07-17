<?php

namespace Codification\Common\Support\Providers
{
	use Codification\Common\Support\CollectionUtils;
	use Codification\Common\Support\ContainerUtils;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Codification\Common\Validation\Contracts\ValidatorRuleReplacer;
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

		/** @var string[] */
		private $facades = [
			'country' => \Codification\Common\Country\Country::class,
			'money'   => \Codification\Common\Money\Money::class,
			'phone'   => \Codification\Common\Phone\Phone::class,
		];

		/**
		 * @return void
		 */
		public function register()
		{
			foreach ($this->facades as $abstract => $class)
			{
				$this->app->bind($abstract, function () use ($class)
					{
						return new $class();
					});
			}
		}

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
				$implements = class_implements($validator);

				if (in_array(ValidatorRule::class, $implements))
				{
					$factory->extend($rule, "{$validator}@validate");
				}

				if (in_array(ValidatorRuleReplacer::class, $implements))
				{
					$factory->replacer($rule, "{$validator}@replace");
				}
			}
		}
	}
}