<?php

namespace Codification\Common\Support
{
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
	}
}