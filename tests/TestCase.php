<?php

namespace Codification\Common\Tests
{
	use Orchestra\Testbench\TestCase as BaseTestCase;

	class TestCase extends BaseTestCase
	{
		/**
		 * @param \Illuminate\Foundation\Application $app
		 *
		 * @return \Illuminate\Support\ServiceProvider[]
		 */
		protected function getPackageProviders($app)
		{
			return [
				\Codification\Common\Support\Providers\CommonServiceProvider::class,
			];
		}
	}
}