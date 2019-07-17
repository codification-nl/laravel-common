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

			$money = new static();

			$money->instance = $instance;

			return $money;
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
				$instance = $result;

				$result = new static();

				$result->instance = $instance;
			}

			return $result;
		}

		/**
		 * @return $this
		 */
		public function copy() : self
		{
			$money = new static();

			$money->instance = clone $this->instance;

			return $money;
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