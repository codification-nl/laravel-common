<?php

namespace Codification\Common\Test\TestCase
{
	/**
	 * Class TestCase
	 * @package Codification\Common\Test\TestCase
	 */
	abstract class TestCase extends \Orchestra\Testbench\TestCase
	{
		/**
		 * @param \Illuminate\Foundation\Application $app
		 *
		 * @return array<int, string|\Illuminate\Support\ServiceProvider>
		 * @psalm-return list<class-string<\Illuminate\Support\ServiceProvider>>
		 */
		protected function getPackageProviders($app)
		{
			return [
				\Codification\Common\Support\Providers\CommonServiceProvider::class,
			];
		}
	}
}