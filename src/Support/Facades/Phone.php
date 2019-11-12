<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static \Codification\Common\Phone\Phone|null make(string|null $number, string $region_code, \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null)
	 * @method static bool validate(string|null $number, string $region_code, \Codification\Common\Phone\PhoneType|null $type = null, \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null)
	 * @method static string|null getRegionCode(string|null $number, string|null $region_code = null)
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