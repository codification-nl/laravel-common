<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Money;

	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasMoney
	{
		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Common\Money\Money>
		 */
		public function getHasMoneyType() : string
		{
			return Money\Money::class;
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
		 * @param string                                        $key
		 * @param mixed|string|\Codification\Common\Money\Money $out
		 * @param-out mixed|string|\Codification\Common\Money\Money $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 */
		public function getHasMoneyValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isMoneyAttribute($key))
			{
				return false;
			}

			$currency_code = $this->getMoneyCurrencyCode($key);
			$out           = $this->asMoney($out, $currency_code);

			return true;
		}

		/**
		 * @param string                                        $key
		 * @param mixed|string|\Codification\Common\Money\Money $out
		 * @param-out mixed|string|\Codification\Common\Money\Money $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function setHasMoneyValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isMoneyAttribute($key))
			{
				return false;
			}

			$out = $this->fromMoney($out);

			return true;
		}

		/**
		 * @param string $value
		 * @param string $currency_code = 'eur'
		 *
		 * @return \Codification\Common\Money\Money
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 */
		public function asMoney(string $value, string $currency_code = 'eur') : Money\Money
		{
			return Money\Money::make($value, $currency_code);
		}

		/**
		 * @param \Codification\Common\Money\Money $value
		 *
		 * @return string
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function fromMoney(Money\Money $value) : string
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