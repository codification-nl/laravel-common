<?php

namespace Codification\Common\Money
{
	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasPrices
	{
		/**
		 * @param string        $key
		 * @param mixed|string &$value
		 *
		 * @return bool
		 */
		public function getHasPricesValue(string $key, &$value) : bool
		{
			$currency = 'eur';

			if (!$this->isPriceAttribute($key, $currency))
			{
				return false;
			}

			$value = $this->asPrice($value, $currency);

			return true;
		}

		/**
		 * @param string                                  $key
		 * @param mixed|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function setHasPricesValue(string $key, &$value) : bool
		{
			if (!$this->isPriceAttribute($key))
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
			return $this->hasCast($key, 'price');
		}

		/**
		 * @param string       $key
		 * @param string|null &$currency
		 *
		 * @return bool
		 */
		protected function isPriceAttribute(string $key, string &$currency = null) : bool
		{
			$parts = explode(':', $key);

			if ($currency !== null && count($parts) === 2)
			{
				$currency = $parts[1];
			}

			return $this->isPriceCastable($parts[0]);
		}
	}
}