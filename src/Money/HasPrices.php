<?php

namespace Codification\Common\Money
{
	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasPrices
	{
		/**
		 * @return string
		 */
		public function getHasPricesType() : string
		{
			return \Codification\Common\Money\Money::class;
		}

		/**
		 * @return string
		 */
		public function getHasPricesCast() : string
		{
			return 'price';
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function getHasPricesValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isPriceAttribute($key))
			{
				return false;
			}

			$cast     = $this->getCasts()[$key];
			$parts    = explode(':', $cast, 2);
			$currency = array_value($parts, 1, 'eur');

			$value = $this->asPrice($value, $currency);

			return true;
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function setHasPricesValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isPriceAttribute($key))
			{
				return false;
			}

			$value = $this->fromPrice($value);

			return true;
		}

		/**
		 * @param string $value
		 * @param string $currency
		 *
		 * @return \Codification\Common\Money\Money
		 */
		public function asPrice(string $value, string $currency = 'eur') : Money
		{
			return Money::make($value, $currency);
		}

		/**
		 * @param \Codification\Common\Money\Money $value
		 *
		 * @return string
		 */
		public function fromPrice(Money $value) : string
		{
			return $value->format();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isPriceCastable(string $key) : bool
		{
			return $this->hasCast($key, $this->getHasPricesCast());
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isPriceAttribute(string $key) : bool
		{
			return $this->isPriceCastable($key);
		}
	}
}