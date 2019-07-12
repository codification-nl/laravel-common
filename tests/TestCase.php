<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Support\Providers\CommonServiceProvider;
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
			return [CommonServiceProvider::class];
		}
	}
}