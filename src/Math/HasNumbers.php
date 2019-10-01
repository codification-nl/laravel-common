<?php

namespace Codification\Common\Math
{
	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasNumbers
	{
		/**
		 * @return string
		 */
		public function getHasNumbersType() : string
		{
			return \Codification\Math\Number::class;
		}

		/**
		 * @return string
		 */
		public function getHasNumbersCast() : string
		{
			return 'number';
		}

		/**
		 * @param string $key
		 *
		 * @return int|null
		 */
		private function getNumberScale(string $key) : ?int
		{
			$cast  = $this->getCasts()[$key];
			$parts = explode(':', $cast, 2);
			$scale = $parts[1] ?? null;

			return $scale;
		}

		/**
		 * @param string                                            $key
		 * @param mixed|int|float|string|\Codification\Math\Number &$value
		 *
		 * @return bool
		 */
		public function getHasNumbersValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isNumberAttribute($key))
			{
				return false;
			}

			$scale = $this->getNumberScale($key);
			$value = $this->asNumber($value, $scale);

			return true;
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function setHasNumbersValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isNumberAttribute($key))
			{
				return false;
			}

			$value = $this->fromNumber($value);

			return true;
		}

		/**
		 * @param int|float|string $value
		 * @param int|null         $scale
		 *
		 * @return \Codification\Math\Number
		 */
		public function asNumber($value, int $scale = null) : \Codification\Math\Number
		{
			return new \Codification\Math\Number($value, $scale);
		}

		/**
		 * @param \Codification\Math\Number $value
		 *
		 * @return string
		 */
		public function fromNumber(\Codification\Math\Number $value) : string
		{
			return $value->getValue();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isNumberCastable(string $key) : bool
		{
			return $this->hasCast($key, $this->getHasNumbersCast());
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isNumberAttribute(string $key) : bool
		{
			return $this->isNumberCastable($key);
		}
	}
}