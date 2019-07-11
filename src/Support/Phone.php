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
		 * @param string $number
		 * @param string $country
		 *
		 * @throws \libphonenumber\NumberParseException
		 */
		private function __construct(?string $number, string $country)
		{
			$this->util        = PhoneNumberUtil::getInstance();
			$this->phoneNumber = $this->util->parse(sanitize($number), strtoupper($country));
		}

		/**
		 * @param string $country
		 *
		 * @return string
		 */
		public function format(string $country) : string
		{
			return $this->util->formatOutOfCountryCallingNumber($this->phoneNumber, strtoupper($country));
		}

		/**
		 * @param \Codification\Common\Enums\PhoneType $type
		 * @param string                               $country
		 *
		 * @return bool
		 */
		public function isValid(PhoneType $type, string $country) : bool
		{
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
		 * @param null|string                          $number
		 * @param string                               $country
		 * @param \Codification\Common\Enums\PhoneType $type
		 *
		 * @return bool
		 */
		public static function validate(?string $number, string $country, PhoneType $type) : bool
		{
			$phone = static::make($number, $country);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($type, $country);
		}

		/**
		 * @param null|string $number
		 * @param string      $country
		 *
		 * @return \Codification\Common\Support\Phone|null
		 */
		public static function make(?string $number, string $country) : Phone
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