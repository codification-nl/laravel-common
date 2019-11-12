<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Enum;

	/**
	 * @method static TestEnumHasFlags NONE()
	 * @method static TestEnumHasFlags FIRST()
	 * @method static TestEnumHasFlags SECOND()
	 * @method static TestEnumHasFlags THIRD()
	 * @method static TestEnumHasFlags BOTH()
	 */
	class TestEnumHasFlags extends Enum\Enum
	{
		use Enum\EnumFlags;

		/**
		 * @var string[]
		 * @psalm-var list<string>
		 */
		protected static $hidden = [
			'NONE',
			'BOTH',
		];

		/** @var int */
		public const NONE = 0;

		/** @var int */
		public const FIRST = 1 << 0;

		/** @var int */
		public const SECOND = 1 << 1;

		/** @var int */
		public const THIRD = 1 << 2;

		/** @var int */
		public const BOTH = TestEnumHasFlags::FIRST | TestEnumHasFlags::SECOND;
	}
}