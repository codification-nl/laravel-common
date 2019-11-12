<?php

namespace Codification\Common\Support
{
	use Codification\Common\Country\Country;
	use Illuminate\Container\Container;
	use Illuminate\Contracts\Container\BindingResolutionException;

	final class ContainerUtils
	{
		/**
		 * @template     T
		 * @param string $abstract
		 *
		 * @return mixed
		 * @psalm-return T
		 * @throws \Codification\Common\Support\Exceptions\ResolutionException
		 */
		public static function resolve(string $abstract)
		{
			try
			{
				/** @psalm-var T $container */
				$container = Container::getInstance()->make($abstract);
			}
			catch (BindingResolutionException $e)
			{
				throw new Exceptions\ResolutionException($abstract, $e->getPrevious());
			}

			return $container;
		}

		/**
		 * @param string|null $locale
		 * @param int         $case = CASE_LOWER
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function resolveLocale(string $locale = null, int $case = CASE_LOWER) : string
		{
			$locale = sanitize($locale);

			if ($locale === null)
			{
				try
				{
					/** @var \Illuminate\Foundation\Application $app */
					$app = static::resolve('app');
				}
				catch (Exceptions\ResolutionException $e)
				{
					throw new Exceptions\ShouldNotHappenException('Failed to resolve [app]', $e);
				}

				$locale = $app->getLocale();
			}

			Country::ensureValid($locale);

			switch ($case)
			{
				case CASE_UPPER:
					return strtoupper($locale);

				default:
					return strtolower($locale);
			}
		}
	}
}