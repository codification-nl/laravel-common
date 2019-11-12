<?php

namespace Codification\Common\Test\TestCase
{
	use Illuminate\Support\Collection;

	class CollectionUtilsTest extends TestCase
	{
		/**
		 * @test
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 * @throws \BadMethodCallException
		 */
		public function it_can_paginate() : void
		{
			/** @noinspection PhpUndefinedMethodInspection */
			/** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginate */
			$paginate = Collection::make(['foo', 'bar'])->paginate();
			static::assertEquals(2, $paginate->total());
		}
	}
}