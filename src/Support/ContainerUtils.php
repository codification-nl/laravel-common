<?php

namespace Codification\Common\Support
{
	use Codification\Common\Country\Country;
	use Illuminate\Container\Container;
	use Illuminate\Contracts\Container\BindingResolutionException;

	final class ContainerUtils
	{
		/**
		 * @param string $abstract
		 *
		 * @return mixed
		 */
		public static function resolve(string $abstract)
		{
			try
			{
				return Container::getInstance()->make($abstract);
			}
			catch (BindingResolutionException $e)
			{
				throw new \RuntimeException("Failed to resolve [$abstract] container", 0, $e->getPrevious());
			}
		}

		/**
		 * @param string|null $locale
		 * @param int         $case = CASE_LOWER
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public static function resolveLocale(string $locale = null, int $case = CASE_LOWER) : string
		{
			$locale = sanitize($locale);

			if ($locale === null)
			{
				/** @var \Illuminate\Foundation\Application $app */
				$app    = static::resolve('app');
				$locale = $app->getLocale();
			}

			Country::ensureValid($locale);

			switch ($case)
			{
				case CASE_LOWER:
					return strtolower($locale);

				case CASE_UPPER:
					return strtoupper($locale);

				default:
					throw new \UnexpectedValueException();
			}
		}
	}
}