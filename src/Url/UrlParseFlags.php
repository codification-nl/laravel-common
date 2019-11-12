<?php

namespace Codification\Common\Url
{
	use Codification\Common\Enum;

	/**
	 * @method static UrlParseFlags NONE()
	 * @method static UrlParseFlags SECURE()
	 * @method static UrlParseFlags STRIP_WWW()
	 * @method static UrlParseFlags ALL()
	 *
	 * @template-extends \Codification\Common\Enum\Enum<int>
	 */
	final class UrlParseFlags extends Enum\Enum
	{
		use Enum\EnumFlags;

		/**
		 * @var string[]
		 * @psalm-var list<string>
		 */
		protected static $hidden = [
			'NONE',
			'ALL',
		];

		/** @var int */
		public const NONE = 0;

		/** @var int */
		public const SECURE = 1 << 0;

		/** @var int */
		public const STRIP_WWW = 1 << 1;

		/** @var int */
		public const ALL = UrlParseFlags::SECURE | UrlParseFlags::STRIP_WWW;

		/**
		 * @return int
		 */
		public function getValue() : int
		{
			return parent::getValue();
		}
	}
}