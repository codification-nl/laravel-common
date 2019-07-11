<?php

namespace Codification\Common\Support
{
	use Codification\Common\Enums\PhoneType;
	use libphonenumber\PhoneNumberType;
	use libphonenumber\PhoneNumberUtil;

	final class Phone
	{
		/** @var \libphonenumber\PhoneNumberUtil */
		private $util;

		/** @var \libphonenumber\PhoneNumber */
		private $phoneNumber;

		/**
		 * @param string|null $number
		 * @param string|null $country
		 *
		 * @throws \libphonenumber\NumberParseException
		 */
		private function __construct(?string $number, string $country = null)
		{
			if ($country === null)
			{
				$country = env('locale');
			}

			$this->util        = PhoneNumberUtil::getInstance();
			$this->phoneNumber = $this->util->parse(sanitize($number), strtoupper($country));
		}

		/**
		 * @param string|null $country
		 *
		 * @return string
		 */
		public function format(string $country = null) : string
		{
			if ($country === null)
			{
				$country = env('locale');
			}

			return $this->util->formatOutOfCountryCallingNumber($this->phoneNumber, strtoupper($country));
		}

		/**
		 * @param string|null                               $country
		 * @param \Codification\Common\Enums\PhoneType|null $type
		 *
		 * @return bool
		 */
		public function isValid(string $country = null, PhoneType $type = null) : bool
		{
			if ($country === null)
			{
				$country = env('locale');
			}

			if ($type === null)
			{
				$type = PhoneType::BOTH();
			}

			switch ($this->util->getNumberType($this->phoneNumber))
			{
				case PhoneNumberType::FIXED_LINE_OR_MOBILE:
					break;

				case PhoneNumberType::MOBILE:
					{
						if ($type->has(PhoneType::MOBILE()))
						{
							break;
						}

						return false;
					}

				case PhoneNumberType::FIXED_LINE:
					{
						if ($type->has(PhoneType::FIXED()))
						{
							break;
						}

						return false;
					}

				default:
					return false;
			}

			return $this->util->isValidNumberForRegion($this->phoneNumber, strtoupper($country));
		}

		/**
		 * @param null|string                               $number
		 * @param \Codification\Common\Enums\PhoneType|null $type
		 * @param string|null                               $country
		 *
		 * @return bool
		 */
		public static function validate(?string $number, string $country = null, PhoneType $type = null) : bool
		{
			$phone = static::make($number, $country);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($country, $type);
		}

		/**
		 * @param null|string $number
		 * @param string|null $country
		 *
		 * @return \Codification\Common\Support\Phone|null
		 */
		public static function make(?string $number, string $country = null) : Phone
		{
			try
			{
				return new static($number, $country);
			}
			catch (\Exception $e)
			{
			}

			return null;
		}
	}
}