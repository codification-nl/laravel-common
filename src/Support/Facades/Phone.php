<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static \Codification\Common\Phone\Phone|null make(string $number, string $region_code, \Codification\Common\Phone\ParseErrorType &$parse_error_type = null)
	 * @method static bool validate(string $number, string $region_code, \Codification\Common\Phone\PhoneType|null $type = null, \Codification\Common\Phone\ParseErrorType &$parse_error_type = null)
	 * @method static string|null getRegionCode(string $number, string|null $region_code = null)
	 */
	class Phone extends Facade
	{
		/**
		 * @return string
		 */
		protected static function getFacadeAccessor()
		{
			return 'phone';
		}
	}
}