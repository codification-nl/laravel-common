<?php

namespace Codification\Common\Support
{
	use Codification\Common\Money\MoneyUtil;

	/**
	 * @mixin \Money\Money
	 */
	final class Money implements \JsonSerializable
	{
		/** @var \Money\Money */
		private $instance;

		/** @var \Codification\Common\Money\MoneyUtil */
		private $util;

		/**
		 * @param string                 $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale
		 */
		private function __construct(string $value, $currency, string $locale = null)
		{
			$this->util     = MoneyUtil::getInstance();
			$this->instance = $this->util->parse($value, $currency, $locale);
		}

		/**
		 * @param string|float|int|null  $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale
		 *
		 * @return $this|null
		 */
		public static function make($value, $currency, string $locale = null) : ?self
		{
			$value = sanitize($value);

			if ($value === null)
			{
				return null;
			}

			return new static($value, $currency, $locale);
		}

		/**
		 * @return string
		 */
		public function format() : string
		{
			return $this->util->format($this->instance);
		}

		/**
		 * @return int
		 */
		public function getCurrencyCode() : int
		{
			return $this->util->numericCodeFor($this->instance->getCurrency());
		}

		/**
		 * @param string $name
		 * @param array  $arguments
		 *
		 * @return mixed
		 */
		public function __call($name, $arguments)
		{
			return call_user_func_array([$this->instance, $name], $arguments);
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->format();
		}

		/**
		 * @return string
		 */
		public function jsonSerialize()
		{
			return $this->format();
		}
	}
}