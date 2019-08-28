<?php

namespace Codification\Common\Money
{
	/**
	 * @method bool equals(\Codification\Common\Money\Money $other)
	 * @method bool greaterThan(\Codification\Common\Money\Money $other)
	 * @method bool greaterThanOrEqual(\Codification\Common\Money\Money $other)
	 * @method bool lessThan(\Codification\Common\Money\Money $other)
	 * @method bool lessThanOrEqual(\Codification\Common\Money\Money $other)
	 * @method bool isSameCurrency(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money add(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money subtract(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money multiply(float|int|string $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 * @method \Codification\Common\Money\Money divide(float|int|string $other, int $rounding_mode = PHP_ROUND_HALF_UP)
	 * @method \Codification\Common\Money\Money mod(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money[] allocate(int[]|float[] $ratios)
	 * @method \Codification\Common\Money\Money[] allocateTo(int $n)
	 * @method \Codification\Common\Money\Money ratioOf(\Codification\Common\Money\Money $other)
	 * @method \Codification\Common\Money\Money absolute()
	 * @method \Codification\Common\Money\Money negative()
	 * @method bool isZero()
	 * @method bool isPositive()
	 * @method bool isNegative()
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
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale = null
		 *
		 * @return static|null
		 */
		public static function zero($currency, string $locale = null) : ?Money
		{
			return Money::make(0, $currency, $locale);
		}

		/**
		 * @param string|float|int|null  $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale = null
		 *
		 * @return static|null
		 */
		public static function make($value, $currency, string $locale = null) : ?Money
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
		 * @param string|null $locale = null
		 *
		 * @return string
		 */
		public function humanize(string $locale = null) : string
		{
			return MoneyUtils::getInstance()->humanize($this->instance, $locale);
		}

		/**
		 * @return int
		 */
		public function getCurrencyCode() : int
		{
			return MoneyUtils::getInstance()->getCurrencyCode($this->instance);
		}

		/**
		 * @return string
		 */
		public function getAmount() : string
		{
			return $this->instance->getAmount();
		}

		/**
		 * @return \Money\Currency
		 */
		public function getCurrency() : \Money\Currency
		{
			return $this->instance->getCurrency();
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 */
		public function __call(string $name, array $parameters)
		{
			return static::call([$this->instance, $name], $parameters);
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 */
		public static function __callStatic(string $name, array $parameters)
		{
			return static::call([\Money\Money::class, $name], $parameters);
		}

		/**
		 * @param array $function
		 * @param array $parameters
		 *
		 * @return mixed
		 */
		private static function call(array $function, array $parameters)
		{
			$parameters = static::unwrap($parameters);

			$result = call_user_func_array($function, $parameters);

			return static::wrap($result);
		}

		/**
		 * @param mixed|\Money\Money|\Money\Money[] $value
		 *
		 * @return mixed|static|static[]
		 */
		private static function wrap($value)
		{
			if (is_array($value))
			{
				return array_map(function ($item)
					{
						return static::wrap($item);
					}, $value);
			};

			if ($value instanceof \Money\Money)
			{
				$result = new static();

				$result->instance = $value;

				return $result;
			}

			return $value;
		}

		/**
		 * @param mixed|static|static[] $value
		 *
		 * @return mixed|\Money\Money|\Money\Money[]
		 */
		private static function unwrap($value)
		{
			if (is_array($value))
			{
				return array_map(function ($item)
					{
						return static::unwrap($item);
					}, $value);
			};

			if ($value instanceof static)
			{
				return $value->instance;
			}

			return $value;
		}

		/**
		 * @return static
		 */
		public function copy() : Money
		{
			$amount   = $this->getAmount();
			$currency = $this->getCurrency();

			return static::wrap(new \Money\Money($amount, $currency));
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