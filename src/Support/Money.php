<?php

namespace Codification\Common\Support
{
	use Illuminate\Support\Facades\App;
	use Money\Currencies\ISOCurrencies;
	use Money\Currency;
	use Money\Formatter\DecimalMoneyFormatter;
	use Money\Parser\DecimalMoneyParser;
	use Money\Parser\AggregateMoneyParser;
	use Money\Parser\IntlLocalizedDecimalParser;

	final class Money
	{
		/** @var \Codification\Common\Support\Money */
		private static $instance;

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

		/** @var string[] */
		private $currencySymbols = [];

		private function __construct()
		{
			$this->isoCurrencies    = new ISOCurrencies();
			$this->decimalParser    = new DecimalMoneyParser($this->isoCurrencies);
			$this->decimalFormatter = new DecimalMoneyFormatter($this->isoCurrencies);
		}

		/**
		 * @return \Codification\Common\Support\Money
		 */
		private static function getInstance() : Money
		{
			if (static::$instance === null)
			{
				static::$instance = new Money();
			}

			return static::$instance;
		}

		/**
		 * @param string $locale
		 *
		 * @return \Money\Parser\AggregateMoneyParser
		 */
		private function getParser(string $locale) : AggregateMoneyParser
		{
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

			return $this->parsers[$locale];
		}

		/**
		 * @param string $symbol
		 *
		 * @return \Money\Currency
		 */
		private function getCurrency(string $symbol) : Currency
		{
			$symbol = strtoupper($symbol);

			if (!array_key_exists($symbol, $this->currencies))
			{
				$this->currencies[$symbol] = new Currency($symbol);
			}

			return $this->currencies[$symbol];
		}

		/**
		 * @param string|null $locale
		 *
		 * @return string
		 */
		private function getCurrencySymbol(string $locale = null) : string
		{
			$locale = sanitize($locale);

			if ($locale === null)
			{
				$locale = App::getLocale();
			}

			$locale = strtolower($locale);

			if (!array_key_exists($locale, $this->currencySymbols))
			{
				$formatter       = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
				$currency_symbol = $formatter->getTextAttribute(\NumberFormatter::CURRENCY_SYMBOL);

				$this->currencySymbols[$locale] = $currency_symbol;
			}

			return $this->currencySymbols[$locale];
		}

		/**
		 * @param string|float|int|null       $value
		 * @param string|\Money\Currency|null $currency
		 * @param string|null                 $locale
		 *
		 * @return \Money\Money|null
		 */
		public static function parse($value, $currency = null, string $locale = null) : ?\Money\Money
		{
			$value  = sanitize($value);
			$locale = sanitize($locale);

			if ($value === null)
			{
				return null;
			}

			$instance = static::getInstance();

			if ($currency === null)
			{
				$currency = $instance->getCurrencySymbol($locale);
			}

			if (is_string($currency))
			{
				$currency = $instance->getCurrency($currency);
			}

			return $instance->getParser($locale)->parse($value, $currency);
		}

		/**
		 * @param \Money\Money|null $money
		 *
		 * @return string|null
		 */
		public static function format(?\Money\Money $money) : ?string
		{
			if ($money === null)
			{
				return null;
			}

			return static::getInstance()->decimalFormatter->format($money);
		}

		/**
		 * @param \Money\Money|null $money
		 *
		 * @return int|null
		 */
		public static function getCurrencyCode(?\Money\Money $money) : ?int
		{
			if ($money === null)
			{
				return null;
			}

			return static::getInstance()->isoCurrencies->numericCodeFor($money->getCurrency());
		}
	}
}