<?php

namespace Codification\Common\Money
{
	use Illuminate\Support\Facades\App;
	use Money\Currencies\ISOCurrencies;
	use Money\Currency;
	use Money\Formatter\DecimalMoneyFormatter;
	use Money\Parser\AggregateMoneyParser;
	use Money\Parser\DecimalMoneyParser;
	use Money\Parser\IntlLocalizedDecimalParser;

	final class MoneyUtil
	{
		/** @var \Codification\Common\Money\MoneyUtil */
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
		 * @param \Money\Currency $currency
		 *
		 * @return int
		 */
		public function numericCodeFor(\Money\Currency $currency) : int
		{
			return $this->isoCurrencies->numericCodeFor($currency);
		}

		/**
		 * @return \Money\Parser\DecimalMoneyParser
		 */
		public function getDecimalParser() : DecimalMoneyParser
		{
			return $this->decimalParser;
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
		 * @param string                 $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale
		 *
		 * @return \Money\Money
		 */
		public function parse(string $value, $currency, string $locale = null) : \Money\Money
		{
			if (is_string($currency))
			{
				$currency = sanitize($currency);

				if ($currency === null)
				{
					throw new \UnexpectedValueException();
				}

				$currency = strtoupper($currency);

				if (!array_key_exists($currency, $this->currencies))
				{
					$this->currencies[$currency] = new Currency($currency);
				}

				$currency = $this->currencies[$currency];
			}

			$locale = sanitize($locale);

			if ($locale === null)
			{
				$locale = App::getLocale();
			}

			$locale = strtolower($locale);

			if (!array_key_exists($locale, $this->parsers))
			{
				$formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
				$parser    = new IntlLocalizedDecimalParser($formatter, $this->isoCurrencies);

				$this->parsers[$locale] = new AggregateMoneyParser([
					$this->decimalParser,
					$parser,
				]);
			}

			return $this->parsers[$locale]->parse($value, $currency);
		}
	}
}