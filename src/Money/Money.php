<?php

namespace Codification\Common\Money
{
	/**
	 * @method bool equals(\Codification\Common\Money\Money $other)
	 * @method bool greaterThan(\Codification\Common\Money\Money $other)
	 * @method bool greaterThanOrEqual(\Codification\Common\Money\Money $other)
	 * @method bool lessThan(\Codification\Common\Money\Money $other)
	 * @method bool lessThanOrEqual(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money add(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money subtract(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money multiply(\Codification\Common\Money\Money $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 * @method \Codification\Common\Money\Money divide(\Codification\Common\Money\Money $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 * @method static \Codification\Common\Money\Money min(\Codification\Common\Money\Money ...$values)
	 * @method static \Codification\Common\Money\Money max(\Codification\Common\Money\Money ...$values)
	 * @method static \Codification\Common\Money\Money avg(\Codification\Common\Money\Money ...$values)
	 * @method static \Codification\Common\Money\Money sum(\Codification\Common\Money\Money ...$values)
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
			return static::call($name, $parameters, $this->instance);
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 */
		public static function __callStatic(string $name, array $parameters)
		{
			return static::call($name, $parameters, \Money\Money::class);
		}

		/**
		 * @param string              $name
		 * @param array               $parameters
		 * @param \Money\Money|string $instance
		 *
		 * @return mixed
		 */
		private static function call(string $name, array $parameters, $instance)
		{
			if (is_array($parameters))
			{
				$parameters = array_map(function ($parameter)
					{
						return ($parameter instanceof static) ? $parameter->instance : $parameter;
					}, $parameters);
			}

			$result = call_user_func_array([$instance, $name], $parameters);

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