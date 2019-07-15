<?php

namespace Codification\Common\Support
{
	use Codification\Common\Exceptions\CurrencyException;
	use Money\Currencies\ISOCurrencies;
	use Money\Currency;
	use Money\Formatter\DecimalMoneyFormatter;
	use Money\Parser\AggregateMoneyParser;
	use Money\Parser\DecimalMoneyParser;
	use Money\Parser\IntlLocalizedDecimalParser;

	final class MoneyUtils
	{
		/** @var \Codification\Common\Support\MoneyUtils */
		private static $instance = null;

		/** @var \Money\Currencies\ISOCurrencies */
		private $isoCurrencies;

		/** @var \Money\Formatter\DecimalMoneyFormatter */
		private $decimalFormatter;

		/** @var \Money\Parser\DecimalMoneyParser */
		private $decimalParser;

		/** @var \Money\Parser\AggregateMoneyParser[] */
		private $parsers = [];

		/** @var \Money\Currency[] */
		private $currencies = [];

		private function __construct()
		{
			$this->isoCurrencies    = new ISOCurrencies();
			$this->decimalParser    = new DecimalMoneyParser($this->isoCurrencies);
			$this->decimalFormatter = new DecimalMoneyFormatter($this->isoCurrencies);
		}

		/**
		 * @return $this
		 */
		public static function getInstance() : self
		{
			if (static::$instance === null)
			{
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * @param \Money\Money $instance
		 *
		 * @return int
		 */
		public function getCurrencyCode(\Money\Money $instance) : int
		{
			return $this->isoCurrencies->numericCodeFor($instance->getCurrency());
		}

		/**
		 * @param \Money\Money $instance
		 *
		 * @return string
		 */
		public function format(\Money\Money $instance) : string
		{
			return $this->decimalFormatter->format($instance);
		}

		/**
		 * @param string $code
		 *
		 * @return \Money\Currency
		 */
		private function getCurrency(string $code) : Currency
		{
			$code = sanitize($code);

			if ($code === null)
			{
				throw new CurrencyException();
			}

			$code = strtoupper($code);

			if (!array_key_exists($code, $this->currencies))
			{
				$currency = new Currency($code);

				if (!$this->isoCurrencies->contains($currency))
				{
					throw new CurrencyException();
				}

				$this->currencies[$code] = $currency;
			}

			return $this->currencies[$code];
		}

		/**
		 * @param string|null $locale
		 *
		 * @return \Money\Parser\AggregateMoneyParser
		 */
		private function getParser(string $locale = null) : AggregateMoneyParser
		{
			$locale = strtolower(Country::get($locale));

			if (!array_key_exists($locale, $this->parsers))
			{
				$formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
				$parser    = new IntlLocalizedDecimalParser($formatter, $this->isoCurrencies);

				$this->parsers[$locale] = new AggregateMoneyParser([
					$this->decimalParser,
					$parser,
				]);
			}

			return $this->parsers[$locale];
		}

		/**
		 * @param string|float|int       $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale
		 *
		 * @return \Money\Money
		 */
		public function parse($value, $currency, string $locale = null) : ?\Money\Money
		{
			$value = sanitize($value);

			if ($value === null)
			{
				return null;
			}

			if (is_string($currency))
			{
				$currency = $this->getCurrency($currency);
			}

			return $this->getParser($locale)->parse($value, $currency);
		}
	}
}