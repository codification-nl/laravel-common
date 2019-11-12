<?php

namespace Codification\Common\Money
{
	use Codification\Common\Support\ContainerUtils;
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;
	use Money\Currencies\ISOCurrencies;
	use Money\Currency;
	use Money\Formatter\DecimalMoneyFormatter;
	use Money\Formatter\IntlMoneyFormatter;
	use Money\Parser\AggregateMoneyParser;
	use Money\Parser\DecimalMoneyParser;
	use Money\Parser\IntlLocalizedDecimalParser;

	final class MoneyUtils
	{
		/** @var \Codification\Common\Money\MoneyUtils|null */
		private static $instance = null;

		/** @var \Money\Currencies\ISOCurrencies */
		private $isoCurrencies;

		/** @var \Money\Formatter\DecimalMoneyFormatter */
		private $decimalFormatter;

		/** @var \Money\Parser\DecimalMoneyParser */
		private $decimalParser;

		/** @var array<string, \Money\Parser\AggregateMoneyParser> */
		private $parsers = [];

		/** @var array<string, \Money\Formatter\IntlMoneyFormatter> */
		private $formatters = [];

		/** @var array<string, \Money\Currency> */
		private $currencies = [];

		/**
		 * MoneyUtils constructor.
		 */
		private function __construct()
		{
			$this->isoCurrencies    = new ISOCurrencies();
			$this->decimalParser    = new DecimalMoneyParser($this->isoCurrencies);
			$this->decimalFormatter = new DecimalMoneyFormatter($this->isoCurrencies);
		}

		/**
		 * @return static
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
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 */
		public function getCurrencyCode(\Money\Money $instance) : int
		{
			try
			{
				return $this->isoCurrencies->numericCodeFor($instance->getCurrency());
			}
			catch (\Money\Exception\UnknownCurrencyException $e)
			{
				throw new Exceptions\CurrencyCodeException();
			}
		}

		/**
		 * @param \Money\Money $instance
		 *
		 * @return string
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 */
		public function format(\Money\Money $instance) : string
		{
			try
			{
				return $this->decimalFormatter->format($instance);
			}
			catch (\Money\Exception\UnknownCurrencyException $e)
			{
				throw new Exceptions\CurrencyCodeException();
			}
		}

		/**
		 * @param \Money\Money $instance
		 * @param string|null  $locale = null
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function humanize(\Money\Money $instance, string $locale = null) : string
		{
			try
			{
				return $this->getFormatter($locale)->format($instance);
			}
			catch (\Money\Exception\UnknownCurrencyException $e)
			{
				throw new Exceptions\CurrencyCodeException();
			}
		}

		/**
		 * @param string $code
		 *
		 * @return \Money\Currency
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private function getCurrency(string $code) : Currency
		{
			$code = sanitize($code);

			if ($code === null)
			{
				throw new Exceptions\CurrencyCodeException();
			}

			$code = strtoupper($code);

			if (!array_key_exists($code, $this->currencies))
			{
				try
				{
					$currency = new Currency($code);
				}
				catch (\InvalidArgumentException $e)
				{
					throw new ShouldNotHappenException('Failed to instantiate [Currency]', $e);
				}

				if (!$this->isoCurrencies->contains($currency))
				{
					throw new Exceptions\CurrencyCodeException($currency->getCode());
				}

				$this->currencies[$code] = $currency;
			}

			return $this->currencies[$code];
		}

		/**
		 * @param string|null $locale = null
		 *
		 * @return \Money\Parser\AggregateMoneyParser
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private function getParser(string $locale = null) : AggregateMoneyParser
		{
			$locale = ContainerUtils::resolveLocale($locale);

			if (!array_key_exists($locale, $this->parsers))
			{
				$formatter   = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
				$intl_parser = new IntlLocalizedDecimalParser($formatter, $this->isoCurrencies);

				try
				{
					$this->parsers[$locale] = new AggregateMoneyParser([
						$this->decimalParser,
						$intl_parser,
					]);
				}
				catch (\InvalidArgumentException $e)
				{
					throw new ShouldNotHappenException('Failed to instantiate parser', $e);
				}
			}

			return $this->parsers[$locale];
		}

		/**
		 * @param string|null $locale = null
		 *
		 * @return \Money\Formatter\IntlMoneyFormatter
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private function getFormatter(string $locale = null) : IntlMoneyFormatter
		{
			$locale = ContainerUtils::resolveLocale($locale);

			if (!array_key_exists($locale, $this->formatters))
			{
				$formatter      = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
				$intl_formatter = new IntlMoneyFormatter($formatter, $this->isoCurrencies);

				$this->formatters[$locale] = $intl_formatter;
			}

			return $this->formatters[$locale];
		}

		/**
		 * @param string|float|int|null  $value
		 * @psalm-param numeric|null     $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale = null
		 *
		 * @return \Money\Money
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
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

			try
			{
				return $this->getParser($locale)->parse($value, $currency);
			}
			catch (\Money\Exception\ParserException $e)
			{
				throw new Exceptions\ParseException($value, $e->getPrevious());
			}
		}
	}
}