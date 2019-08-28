<?php

namespace Codification\Common\Money
{
	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasMoney
	{
		/**
		 * @return string
		 */
		public function getHasMoneyType() : string
		{
			return \Codification\Common\Money\Money::class;
		}

		/**
		 * @return string
		 */
		public function getHasMoneyCast() : string
		{
			return 'money';
		}

		/**
		 * @param string $key
		 *
		 * @return string
		 */
		private function getMoneyCurrencyCode(string $key) : string
		{
			$cast          = $this->getCasts()[$key];
			$parts         = explode(':', $cast, 2);
			$currency_code = $parts[1] ?? 'eur';

			return $currency_code;
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function getHasMoneyValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isMoneyAttribute($key))
			{
				return false;
			}

			$currency_code = $this->getMoneyCurrencyCode($key);
			$value         = $this->asMoney($value, $currency_code);

			return true;
		}

		/**
		 * @param string                                         $key
		 * @param mixed|string|\Codification\Common\Money\Money &$value
		 *
		 * @return bool
		 */
		public function setHasMoneyValue(string $key, &$value) : bool
		{
			if ($value === null || !$this->isMoneyAttribute($key))
			{
				return false;
			}

			$value = $this->fromMoney($value);

			return true;
		}

		/**
		 * @param string $value
		 * @param string $currency_code = 'eur'
		 *
		 * @return \Codification\Common\Money\Money
		 */
		public function asMoney(string $value, string $currency_code = 'eur') : Money
		{
			return Money::make($value, $currency_code);
		}

		/**
		 * @param \Codification\Common\Money\Money $value
		 *
		 * @return string
		 */
		public function fromMoney(Money $value) : string
		{
			return $value->format();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isMoneyCastable(string $key) : bool
		{
			return $this->hasCast($key, $this->getHasMoneyCast());
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isMoneyAttribute(string $key) : bool
		{
			return $this->isMoneyCastable($key);
		}
	}
}