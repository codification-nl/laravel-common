<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Enums\Enum;

	/**
	 * @method static PhoneType|int NONE()
	 * @method static PhoneType|int FIXED()
	 * @method static PhoneType|int MOBILE()
	 * @method static PhoneType|int BOTH()
	 */
	final class PhoneType extends Enum
	{
		use \Codification\Common\Enums\EnumFlags;

		protected static $hidden = [
			'NONE',
			'BOTH',
		];

		public const NONE   = 0;
		public const FIXED  = 1 << 0;
		public const MOBILE = 1 << 1;
		public const BOTH   = PhoneType::FIXED | PhoneType::MOBILE;
	}
}