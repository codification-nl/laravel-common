<?php

namespace Codification\Common\Money
{
	use Codification\Common\Contracts\Support\Cloneable;
	use Codification\Common\Contracts\Support\Stringable;
	use Codification\Common\Support\Contracts;
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;

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
	 *
	 * @template-implements \Codification\Common\Contracts\Support\Cloneable<\Codification\Common\Money\Money>
	 */
	final class Money implements Contracts\Bindable, Stringable, Cloneable
	{
		/** @var \Money\Money|null */
		private $instance = null;

		/**
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale = null
		 *
		 * @return \Codification\Common\Money\Money|null
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 */
		public static function zero($currency, string $locale = null) : ?Money
		{
			return Money::make(0, $currency, $locale);
		}

		/**
		 * @param string|float|int|null  $value
		 * @psalm-param numeric|null     $value
		 * @param string|\Money\Currency $currency
		 * @param string|null            $locale = null
		 *
		 * @return \Codification\Common\Money\Money|null
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
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
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 */
		public function format() : string
		{
			return MoneyUtils::getInstance()->format($this->ensureInstance());
		}

		/**
		 * @param string|null $locale = null
		 *
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public function humanize(string $locale = null) : string
		{
			return MoneyUtils::getInstance()->humanize($this->ensureInstance(), $locale);
		}

		/**
		 * @return int
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 */
		public function getCurrencyCode() : int
		{
			return MoneyUtils::getInstance()->getCurrencyCode($this->ensureInstance());
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function getAmount() : string
		{
			return $this->ensureInstance()->getAmount();
		}

		/**
		 * @return \Money\Currency
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function getCurrency() : \Money\Currency
		{
			return $this->ensureInstance()->getCurrency();
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function __call(string $name, array $parameters)
		{
			return static::call([$this->ensureInstance(), $name], $parameters);
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return mixed
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function __callStatic(string $name, array $parameters)
		{
			return static::call([\Money\Money::class, $name], $parameters);
		}

		/**
		 * @param array $function
		 * @psalm-param  array{0: \Money\Money|class-string<\Money\Money>, 1: string} $function
		 * @param array $parameters
		 *
		 * @return mixed
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private static function call($function, array $parameters)
		{
			/**
			 * @var mixed[] $params
			 * @psalm-var list $params
			 */
			$params = static::unwrap($parameters);

			/** @var mixed $result */
			$result = call_user_func_array($function, $params);

			return static::wrap($result);
		}

		/**
		 * @param mixed|mixed[] $value
		 * @psalm-var    mixed|list $value
		 *
		 * @return mixed|mixed[]
		 * @psalm-return mixed|list
		 */
		private static function wrap($value)
		{
			if (is_array($value))
			{
				/** @psalm-suppress MissingClosureReturnType */
				return array_map(function ($item)
					{
						return static::wrap($item);
					}, $value);
			};

			if ($value instanceof \Money\Money)
			{
				$result = new static();

				$result->instance = clone $value;

				return $result;
			}

			return $value;
		}

		/**
		 * @param mixed|mixed[] $value
		 * @psalm-var    mixed|list $value
		 *
		 * @return mixed|mixed[]
		 * @psalm-return mixed|list
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private static function unwrap($value)
		{
			if (is_array($value))
			{
				/** @psalm-suppress MissingClosureReturnType */
				return array_map(function ($item)
					{
						return static::unwrap($item);
					}, $value);
			};

			if ($value instanceof static)
			{
				return $value->ensureInstance();
			}

			return $value;
		}

		/**
		 * @return \Codification\Common\Money\Money
		 */
		public function copy() : Money
		{
			return clone $this;
		}

		/**
		 * @return \Codification\Common\Money\Money
		 */
		public function clone() : Money
		{
			return clone $this;
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function jsonSerialize()
		{
			return $this->format();
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function __toString() : string
		{
			return $this->toString();
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function toString() : string
		{
			return $this->format();
		}

		/**
		 * @return \Codification\Common\Money\Money
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function __clone()
		{
			$amount   = $this->getAmount();
			$currency = $this->getCurrency();

			try
			{
				/**
				 * @var \Codification\Common\Money\Money $clone
				 * @psalm-suppress MissingThrowsDocblock
				 */
				$clone = static::wrap(new \Money\Money($amount, $currency));
			}
			catch (\InvalidArgumentException $e)
			{
				throw new ShouldNotHappenException('Failed to instantiate [Money]', $e);
			}

			return $clone;
		}

		/**
		 * @return \Money\Money
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private function ensureInstance() : \Money\Money
		{
			if ($this->instance === null)
			{
				throw new ShouldNotHappenException('$this->instance === null');
			}

			return $this->instance;
		}
	}
}