<?php

namespace Codification\Common\Enum
{
	use Codification\Common\Contracts\Support\Stringable;
	use Codification\Common\Validation\Rules;
	use Illuminate\Database\Eloquent\Builder;

	/**
	 * @template T of array-key
	 */
	abstract class Enum implements Stringable
	{
		/**
		 * @var int|string
		 * @psalm-var T
		 */
		protected $value;

		/** @var array<string, array<string, array-key>> */
		protected static $cache = [];

		/**
		 * @var string[]
		 * @psalm-var list<string>
		 */
		protected static $hidden = [];

		/**
		 * @return int|string
		 * @psalm-return T
		 */
		public function getValue()
		{
			return $this->value;
		}

		/**
		 * @param int|string $value
		 * @psalm-param T    $value
		 * @return void
		 */
		public function setValue($value) : void
		{
			$this->value = $value;
		}

		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Common\Enum\Enum<T>>
		 */
		public function getClass() : string
		{
			/** @psalm-var class-string<\Codification\Common\Enum\Enum<T>> $class */
			$class = static::class;

			return $class;
		}

		/**
		 * @param int|string $value
		 * @psalm-param T    $value
		 *
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		protected function __construct($value)
		{
			if (!static::isValid($value))
			{
				throw new Exceptions\ValueException($value, get_called_class());
			}

			/** @psalm-var T $value */
			$this->value = $value;
		}

		/**
		 * @param \Codification\Common\Enum\Enum|int|string $enum
		 * @psalm-param \Codification\Common\Enum\Enum<T>|T $enum
		 * @param bool                                      $strict = true
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function equals($enum, bool $strict = true) : bool
		{
			if (!is_object($enum))
			{
				$enum = static::make($enum);
			}

			if ($strict && (get_called_class() !== $enum->getClass()))
			{
				return false;
			}

			/** @psalm-var \Codification\Common\Enum\Enum<T> $enum */
			return ($this->value === $enum->value);
		}

		/**
		 * @param \Codification\Common\Enum\Enum|int|string $enum
		 * @psalm-param \Codification\Common\Enum\Enum<T>|T $enum
		 * @param bool                                      $strict = true
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function eq($enum, bool $strict = true) : bool
		{
			return $this->equals($enum, $strict);
		}

		/**
		 * @template    V of array-key
		 * @param int|string $value
		 * @psalm-param V    $value
		 * @param bool       $strict = true
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function isValid($value, bool $strict = true) : bool
		{
			return in_array($value, static::toArray(), $strict);
		}

		/**
		 * @template     V of array-key
		 * @return int[]|string[]
		 * @psalm-return list<V>
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function values() : array
		{
			/** @psalm-var list<V> $values */
			$values = array_values(static::toArray());

			return $values;
		}

		/**
		 * @return string[]
		 * @psalm-return list<string>
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function keys() : array
		{
			/** @psalm-var list<string> $keys */
			$keys = array_diff(array_keys(static::toArray()), static::$hidden);

			return $keys;
		}

		/**
		 * @template V of array-key
		 * @param \Codification\Common\Enum\Enum $enum
		 * @psalm-param \Codification\Common\Enum\Enum<V> $enum
		 *
		 * @return void
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function assertType(Enum $enum) : void
		{
			/** @var class-string<\Codification\Common\Enum\Enum<array-key>> $type */
			$type = get_called_class();

			/** @var class-string<\Codification\Common\Enum\Enum<V>> $other */
			$other = $enum->getClass();

			if ($other !== $type)
			{
				throw new Exceptions\EnumException("[$other] !== [$type]");
			}
		}

		/**
		 * @template     V of array-key
		 * @return array<string, int|string>
		 * @psalm-return array<string, V>
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function toArray() : array
		{
			$type = get_called_class();

			if (!array_key_exists($type, static::$cache))
			{
				try
				{
					$reflection = new \ReflectionClass($type);
				}
				catch (\ReflectionException $e)
				{
					throw new Exceptions\EnumException("[$type] does not exist", $e->getPrevious());
				}

				/** @psalm-var array<string, V> $values */
				$values = $reflection->getConstants();

				static::$cache[$type] = static::toArrayTraits($values);
			}

			/** @psalm-var array<string, V> $cache */
			$cache = static::$cache[$type];

			return $cache;
		}

		/**
		 * @template    V of array-key
		 * @param mixed|string[] $value
		 * @psalm-param V|list<string> $value
		 *
		 * @return static
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function make($value) : self
		{
			/** @psalm-var V $value */
			$value = static::initializeTraits($value);

			return new static($value);
		}

		/**
		 * @template     V of array-key|array<string, array-key>
		 * @param mixed         $value
		 * @param string        $to
		 * @param \Closure|null $cb = null
		 * @psalm-param \Closure(V, V): V $cb = null
		 *
		 * @return mixed
		 * @psalm-return V
		 */
		private static function forward($value, string $to, \Closure $cb = null)
		{
			$class = static::class;

			/** @var array<trait-string> $traits */
			$traits = class_uses_recursive($class);

			foreach ($traits as $trait)
			{
				$trait_name  = class_basename($trait);
				$method_name = "{$to}{$trait_name}";

				if (method_exists($class, $method_name))
				{
					/** @psalm-var V $trait_value */
					$trait_value = forward_static_call([$class, $method_name], $value);

					if ($cb !== null)
					{
						/** @psalm-var V $value */
						$trait_value = $cb($value, $trait_value);
					}

					$value = $trait_value;
				}
			}

			/** @psalm-var V $value */
			return $value;
		}

		/**
		 * @template     V of array-key
		 * @param mixed $value
		 *
		 * @return int|string
		 * @psalm-return V
		 */
		private static function initializeTraits($value)
		{
			/** @psalm-var V $result */
			$result = static::forward($value, 'initialize');

			return $result;
		}

		/**
		 * @template     V of array-key
		 * @param array<string, mixed> $values
		 * @psalm-param  array<string, V> $values
		 *
		 * @return array<string, int|string>
		 * @psalm-return array<string, V>
		 */
		private static function toArrayTraits(array $values) : array
		{
			/** @psalm-suppress InvalidScalarArgument */
			return static::forward($values, 'toArray', function (array $values, array $trait_values) : array
				{
					/** @psalm-var array<string, V> $result */
					$result = array_merge($trait_values, $values);

					return $result;
				});
		}

		/**
		 * @template V of array-key
		 * @param string $name
		 *
		 * @return static
		 * @throws \Codification\Common\Enum\Exceptions\ParseException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public static function parse(string $name) : self
		{
			/** @psalm-var array<string, V> $values */
			$values = static::toArray();

			if (!array_key_exists($name, $values))
			{
				throw new Exceptions\ParseException($name, get_called_class());
			}

			return new static($values[$name]);
		}

		/**
		 * @template V
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return static
		 * @throws \Codification\Common\Enum\Exceptions\ParseException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public static function __callStatic(string $name, array $parameters) : self
		{
			return static::parse($name);
		}

		/**
		 * @return string
		 */
		public function toString() : string
		{
			return strval($this->getValue());
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->toString();
		}

		/**
		 * @return int|string
		 * @psalm-return T
		 */
		public function jsonSerialize()
		{
			return $this->getValue();
		}

		/**
		 * @template V of array-key
		 * @param bool $strict = true
		 *
		 * @return \Codification\Common\Validation\Rules\Enum
		 * @psalm-return \Codification\Common\Validation\Rules\Enum<V>
		 */
		public static function rule(bool $strict = true) : Rules\Enum
		{
			/** @var class-string<\Codification\Common\Enum\Enum<V>> $enum */
			$enum = get_called_class();

			/** @psalm-var \Codification\Common\Validation\Rules\Enum<V> $rule */
			$rule = Rules\Enum::make($enum, $strict);

			return $rule;
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder $builder
		 * @param string                                $column
		 *
		 * @return \Illuminate\Database\Eloquent\Builder
		 * @throws \InvalidArgumentException
		 * @throws \RuntimeException
		 */
		public static function select(Builder $builder, string $column)
		{
			$query = $builder->getQuery();

			if (count((array)$query->columns) === 0)
			{
				$builder->addSelect(['*']);
			}

			$qualified = $builder->qualifyColumn($column);
			$wrapped   = $query->grammar->wrap($qualified);
			$alias     = $query->grammar->wrap($column);

			$builder->selectRaw("({$wrapped} | 0) as {$alias}");

			return $builder;
		}

		/**
		 * @template V of array-key
		 * @param \Illuminate\Database\Eloquent\Builder     $builder
		 * @param string                                    $column
		 * @param \Codification\Common\Enum\Enum|int|string $value
		 * @psalm-param \Codification\Common\Enum\Enum<V>|V $value
		 * @param string                                    $boolean = 'and'
		 *
		 * @return \Illuminate\Database\Eloquent\Builder
		 * @throws \InvalidArgumentException
		 * @throws \RuntimeException
		 */
		public static function where(Builder $builder, string $column, $value, string $boolean = 'and')
		{
			$query = $builder->getQuery();

			$qualified = $builder->qualifyColumn($column);
			$wrapped   = $query->grammar->wrap($qualified);

			$builder->whereRaw("({$wrapped} & ?) != 0", [$value], $boolean);

			return $builder;
		}
	}
}