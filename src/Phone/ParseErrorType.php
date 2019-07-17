<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Enums\Enum;
	use libphonenumber\NumberParseException;

	/**
	 * @method static ParseErrorType|int INVALID_COUNTRY_CODE()
	 * @method static ParseErrorType|int NOT_A_NUMBER()
	 * @method static ParseErrorType|int TOO_SHORT_AFTER_IDD()
	 * @method static ParseErrorType|int TOO_SHORT_NSN()
	 * @method static ParseErrorType|int TOO_LONG()
	 */
	final class ParseErrorType extends Enum
	{
		public const INVALID_COUNTRY_CODE = NumberParseException::INVALID_COUNTRY_CODE;
		public const NOT_A_NUMBER         = NumberParseException::NOT_A_NUMBER;
		public const TOO_SHORT_AFTER_IDD  = NumberParseException::TOO_SHORT_AFTER_IDD;
		public const TOO_SHORT_NSN        = NumberParseException::TOO_SHORT_NSN;
		public const TOO_LONG             = NumberParseException::TOO_LONG;
	}
}