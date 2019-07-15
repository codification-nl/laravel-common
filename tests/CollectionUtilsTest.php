<?php

namespace Codification\Common\Tests
{
	use Illuminate\Support\Collection;

	class CollectionUtilsTest extends TestCase
	{
		/** @test */
		public function it_can_paginate()
		{
			/** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginate */
			$paginate = Collection::make(['foo', 'bar'])->paginate();

			$this->assertEquals(2, $paginate->total());
		}
	}
}