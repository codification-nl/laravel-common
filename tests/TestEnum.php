<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Enums\Enum;

	/**
	 * @method static TestEnum|string HELLO()
	 * @method static TestEnum|string HELLO2()
	 * @method static TestEnum|string WORLD()
	 * @method static TestEnum|string FOO()
	 */
	class TestEnum extends Enum
	{
		public const HELLO  = 'hello';
		public const HELLO2 = 'hello';
		public const WORLD  = 'world';
		public const FOO    = 'foo';
	}
}