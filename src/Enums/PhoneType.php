<?php

namespace Codification\Common\Enums
{
	use Codification\Common\Support\Enum;

	/**
	 * @method static PhoneType|int NONE()
	 * @method static PhoneType|int FIXED()
	 * @method static PhoneType|int MOBILE()
	 * @method static PhoneType|int BOTH()
	 */
	final class PhoneType extends Enum
	{
		use Concerns\EnumFlags;

		public const NONE   = 0;
		public const FIXED  = 1 << 0;
		public const MOBILE = 1 << 1;
		public const BOTH   = PhoneType::FIXED | PhoneType::MOBILE;
	}
}