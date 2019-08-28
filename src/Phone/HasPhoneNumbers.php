<?php

namespace Codification\Common\Phone
{
	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasPhoneNumbers
	{
		/**
		 * @return string
		 */
		public function getHasPhoneNumbersType() : string
		{
			return \Codification\Common\Phone\Phone::class;
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
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Phone\Phone &$value
		 *
		 * @return bool
		 */
		public function getHasPhoneNumbersValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isPhoneAttribute($key))
			{
				return false;
			}

			$region_code = $this->getPhoneRegionCode($key);
			$value       = $this->asPhone($value, $region_code);

			return true;
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Phone\Phone &$value
		 *
		 * @return bool
		 */
		public function setHasPhoneNumbersValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isPhoneAttribute($key))
			{
				return false;
			}

			$value = $this->fromPhone($value);

			return true;
		}

		/**
		 * @param string $value
		 * @param string $region_code
		 *
		 * @return \Codification\Common\Phone\Phone
		 */
		public function asPhone(string $value, string $region_code) : Phone
		{
			return phone($value, $region_code);
		}

		/**
		 * @param \Codification\Common\Phone\Phone $value
		 *
		 * @return string
		 */
		public function fromPhone(Phone $value) : string
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