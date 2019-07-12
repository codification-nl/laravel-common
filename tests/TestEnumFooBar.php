<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Support\Enum;

	/**
	 * @method static TestEnumFooBar|string HELLO()
	 */
	class TestEnumFooBar extends Enum
	{
		public const HELLO = 'hello';
	}
}