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
		 * @param string|null $locale
		 *
		 * @return \Money\Parser\AggregateMoneyParser
		 */
		private function getParser(string $locale = null) : AggregateMoneyParser
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
		 * @param string $code
		 *
		 * @return \Money\Currency
		 */
		private function getCurrency(string $code) : Currency
		{
			$code = strtoupper($code);

			if (!array_key_exists($code, $this->currencies))
			{
				$this->currencies[$code] = new Currency($code);
			}

			return $this->currencies[$code];
		}

		/**
		 * @param string|float|int|null  $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale
		 *
		 * @return \Money\Money|null
		 */
		public static function parse($value, $currency, string $locale = null) : ?\Money\Money
		{
			$value = sanitize($value);

			if ($value === null)
			{
				return null;
			}

			$instance = static::getInstance();

			if (is_string($currency))
			{
				$currency = sanitize($currency);

				if ($currency === null)
				{
					throw new \UnexpectedValueException();
				}

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