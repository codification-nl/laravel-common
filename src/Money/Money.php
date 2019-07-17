<?php

namespace Codification\Common\Money
{
	/**
	 * @method bool equals(Money $other)
	 * @method bool greaterThan(Money $other)
	 * @method bool greaterThanOrEqual(Money $other)
	 * @method bool lessThan(Money $other)
	 * @method bool lessThanOrEqual(Money $other)
	 * @method Money add(Money $other)
	 * @method Money subtract(Money $other)
	 * @method Money multiply(Money $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 * @method Money divide(Money $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 */
	final class Money implements \JsonSerializable
	{
		/** @var \Money\Money */
		private $instance;

		/**
		 * @param \Money\Money $instance
		 */
		private function __construct(\Money\Money $instance)
		{
			$this->instance = $instance;
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
			$instance = MoneyUtils::getInstance()->parse($value, $currency, $locale);

			if ($instance === null)
			{
				return null;
			}

			return new static($instance);
		}

		/**
		 * @return string
		 */
		public function format() : string
		{
			return MoneyUtils::getInstance()->format($this->instance);
		}

		/**
		 * @return int
		 */
		public function getCurrencyCode() : int
		{
			return MoneyUtils::getInstance()->getCurrencyCode($this->instance);
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 */
		public function __call(string $name, array $parameters)
		{
			if (is_array($parameters))
			{
				$parameters = array_map(function ($parameter)
					{
						if ($parameter instanceof static)
						{
							return $parameter->instance;
						}

						return $parameter;
					}, $parameters);
			}

			$result = $this->instance->{$name}(...$parameters);

			if ($result instanceof \Money\Money)
			{
				$result = new static($result);
			}

			return $result;
		}

		/**
		 * @return $this
		 */
		public function copy() : self
		{
			return new static(clone $this->instance);
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