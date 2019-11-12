<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Phone;

	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasPhoneNumbers
	{
		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Common\Phone\Phone>
		 */
		public function getHasPhoneNumbersType() : string
		{
			return Phone\Phone::class;
		}

		/**
		 * @return string
		 */
		public function getHasPhoneNumbersCast() : string
		{
			return 'phone';
		}

		/**
		 * @param string $key
		 *
		 * @return null|string
		 */
		private function getPhoneRegionCode(string $key) : ?string
		{
			$cast  = $this->getCasts()[$key];
			$parts = explode(':', $cast, 2);
			$key   = $parts[1] ?? null;

			if ($key === null)
			{
				return null;
			}

			$region_code = $this->attributes[$key] ?? null;

			return $region_code;
		}

		/**
		 * @param string                                        $key
		 * @param mixed|string|\Codification\Common\Phone\Phone $out
		 * @param-out mixed|string|\Codification\Common\Phone\Phone $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function getHasPhoneNumbersValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isPhoneAttribute($key))
			{
				return false;
			}

			$region_code = $this->getPhoneRegionCode($key);
			$out         = $this->asPhone($out, $region_code);

			return true;
		}

		/**
		 * @param string                                        $key
		 * @param mixed|string|\Codification\Common\Phone\Phone $out
		 * @param-out mixed|string|\Codification\Common\Phone\Phone $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function setHasPhoneNumbersValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isPhoneAttribute($key))
			{
				return false;
			}

			$out = $this->fromPhone($out);

			return true;
		}

		/**
		 * @param string $value
		 * @param string $region_code
		 *
		 * @return \Codification\Common\Phone\Phone
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function asPhone(string $value, string $region_code) : Phone\Phone
		{
			return Phone\Phone::make($value, $region_code);
		}

		/**
		 * @param \Codification\Common\Phone\Phone $value
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function fromPhone(Phone\Phone $value) : string
		{
			return $value->format();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isPhoneCastable(string $key) : bool
		{
			if (!$this->hasCast($key, $this->getHasPhoneNumbersCast()))
			{
				return false;
			}

			return ($this->getPhoneRegionCode($key) !== null);
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isPhoneAttribute(string $key) : bool
		{
			return $this->isPhoneCastable($key);
		}
	}
}