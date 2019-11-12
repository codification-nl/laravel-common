<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Enum;

	/**
	 * @method static PhoneType NONE()
	 * @method static PhoneType FIXED()
	 * @method static PhoneType MOBILE()
	 * @method static PhoneType BOTH()
	 *
	 * @template-extends \Codification\Common\Enum\Enum<int>
	 */
	final class PhoneType extends Enum\Enum
	{
		use Enum\EnumFlags;

		/**
		 * @var array<int, string>
		 * @psalm-var list<string>
		 */
		protected static $hidden = [
			'NONE',
			'BOTH',
		];

		/** @var int */
		public const NONE = 0;

		/** @var int */
		public const FIXED = 1 << 0;

		/** @var int */
		public const MOBILE = 1 << 1;

		/** @var int */
		public const BOTH = PhoneType::FIXED | PhoneType::MOBILE;
	}
}