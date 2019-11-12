<?php

namespace Codification\Common\Test\TestCase
{
	class UrlTest extends TestCase
	{
		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \InvalidArgumentException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \PHPUnit\Framework\Exception
		 */
		public function it_has_helper_function() : void
		{
			$object = url_parse('https://codification.nl/');
			static::assertEquals('codification.nl', $object->host);
		}
	}
}