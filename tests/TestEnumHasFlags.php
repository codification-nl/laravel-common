<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Support\Enum;

	/**
	 * @method static TestEnumHasFlags|int NONE()
	 * @method static TestEnumHasFlags|int FIRST()
	 * @method static TestEnumHasFlags|int SECOND()
	 * @method static TestEnumHasFlags|int BOTH()
	 */
	class TestEnumHasFlags extends Enum
	{
		use \Codification\Common\Enums\Concerns\EnumFlags;

		public const NONE   = 0;
		public const FIRST  = 1 << 0;
		public const SECOND = 1 << 1;
		public const BOTH   = TestEnumHasFlags::FIRST | TestEnumHasFlags::SECOND;
	}
}