<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Enum;

	/**
	 * @method static TestEnum NONE()
	 * @method static TestEnum HELLO()
	 * @method static TestEnum HELLO_ALSO()
	 * @method static TestEnum WORLD()
	 * @method static TestEnum FOO()
	 */
	class TestEnum extends Enum\Enum
	{
		/**
		 * @var array<int, string>
		 * @psalm-var list<string>
		 */
		protected static $hidden = [
			'NONE',
		];

		/** @var string */
		public const NONE = 'none';

		/** @var string */
		public const HELLO = 'hello';

		/** @var string */
		public const HELLO_ALSO = 'hello';

		/** @var string */
		public const WORLD = 'world';

		/** @var string */
		public const FOO = 'foo';
	}
}