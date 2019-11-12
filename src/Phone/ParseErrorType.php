<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Enum;
	use libphonenumber\NumberParseException;

	/**
	 * @method static ParseErrorType NONE()
	 * @method static ParseErrorType INVALID_COUNTRY_CODE()
	 * @method static ParseErrorType NOT_A_NUMBER()
	 * @method static ParseErrorType TOO_SHORT_AFTER_IDD()
	 * @method static ParseErrorType TOO_SHORT_NSN()
	 * @method static ParseErrorType TOO_LONG()
	 *
	 * @template-extends \Codification\Common\Enum\Enum<int>
	 */
	final class ParseErrorType extends Enum\Enum
	{
		/** @var int */
		public const NONE = -1;

		/** @var int */
		public const INVALID_COUNTRY_CODE = NumberParseException::INVALID_COUNTRY_CODE;

		/** @var int */
		public const NOT_A_NUMBER = NumberParseException::NOT_A_NUMBER;

		/** @var int */
		public const TOO_SHORT_AFTER_IDD = NumberParseException::TOO_SHORT_AFTER_IDD;

		/** @var int */
		public const TOO_SHORT_NSN = NumberParseException::TOO_SHORT_NSN;

		/** @var int */
		public const TOO_LONG = NumberParseException::TOO_LONG;
	}
}