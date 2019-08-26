<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Enums\Enum;

	/**
	 * @method static TestEnum|string NONE()
	 * @method static TestEnum|string HELLO()
	 * @method static TestEnum|string HELLO_ALSO()
	 * @method static TestEnum|string WORLD()
	 * @method static TestEnum|string FOO()
	 */
	class TestEnum extends Enum
	{
		protected static $hidden = [
			'NONE',
		];

		public const NONE       = 'none';
		public const HELLO      = 'hello';
		public const HELLO_ALSO = 'hello';
		public const WORLD      = 'world';
		public const FOO        = 'foo';
	}
}