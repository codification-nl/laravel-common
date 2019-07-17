<?php

namespace Codification\Common\Enums
{
	use Codification\Common\Validation\Rules;
	use Illuminate\Database\Eloquent\Builder;

	abstract class Enum implements \JsonSerializable
	{
		/** @var int|string */
		protected $value;

		/** @var array[] */
		protected static $cache = [];

		/** @var string[] */
		protected static $hidden = [];

		/** @return int|string */
		public function getValue()
		{
			return $this->value;
		}

		/**
		 * @param int|string $value
		 *
		 * @throws \Codification\Common\Enums\Exceptions\EnumException
		 */
		protected function __construct($value)
		{
			if (!static::isValid($value))
			{
				throw new Exceptions\EnumException(sprintf('Unexpected value \'%s\' for [%s]', $value, get_called_class()));
			}

			$this->value = $value;
		}

		/**
		 * @param \Codification\Common\Enums\Enum $enum
		 * @param bool                            $strict = true
		 *
		 * @return bool
		 */
		public function equals(Enum $enum, bool $strict = true) : bool
		{
			if (!is_object($enum))
			{
				$enum = static::make($enum);
			}

			if ($strict && get_called_class() !== get_class($enum))
			{
				return false;
			}

			return ($this->value === $enum->value);
		}

		/**
		 * @param \Codification\Common\Enums\Enum $enum
		 * @param bool                            $strict = true
		 *
		 * @return bool
		 */
		public function eq(Enum $enum, bool $strict = true) : bool
		{
			return $this->equals($enum, $strict);
		}

		/**
		 * @param int|string $value
		 * @param bool       $strict = true
		 *
		 * @return bool
		 */
		public static function isValid($value, bool $strict = true) : bool
		{
			return in_array($value, static::toArray(), $strict);
		}

		/**
		 * @return int[]|string[]
		 */
		public static function values() : array
		{
			return array_values(static::toArray());
		}

		/**
		 * @return string[]
		 */
		public static function names() : array
		{
			return array_diff(array_keys(static::toArray()), static::$hidden);
		}

		/**
		 * @param \Codification\Common\Enums\Enum $enum
		 *
		 * @return void
		 * @throws \Codification\Common\Enums\Exceptions\EnumException
		 */
		public static function assertType(Enum $enum) : void
		{
			$type  = get_called_class();
			$other = get_class($enum);

			if ($other !== $type)
			{
				throw new Exceptions\EnumException("[$other] !== [$type]");
			}
		}

		/**
		 * @return int[]|string[]
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
					throw new \RuntimeException("[$type] does not exist", 0, $e->getPrevious());
				}

				static::$cache[$type] = $reflection->getConstants();
			}

			return static::$cache[$type];
		}

		/**
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public static function make($value) : self
		{
			return new static(static::initializeTraits($value));
		}

		/**
		 * @param int|string $value
		 *
		 * @return int|string
		 */
		private static function initializeTraits($value)
		{
			$class = static::class;

			foreach (class_uses_recursive($class) as $trait)
			{
				$name   = class_basename($trait);
				$method = "initialize{$name}";

				if (method_exists($class, $method))
				{
					$value = forward_static_call([$class, $method], $value);
				}
			}

			return $value;
		}

		/**
		 * @param string $name
		 *
		 * @return $this
		 * @throws \Codification\Common\Enums\Exceptions\EnumException
		 */
		public static function parse(string $name) : self
		{
			$values = static::toArray();

			if (!array_key_exists($name, $values))
			{
				throw new Exceptions\EnumException(sprintf("'%s' does not exist in [%s]", $name, get_called_class()));
			}

			return new static($values[$name]);
		}

		/**
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return $this
		 */
		public static function __callStatic(string $name, array $parameters) : self
		{
			return static::parse($name);
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->getValue();
		}

		/**
		 * @return int|string
		 */
		public function jsonSerialize()
		{
			return $this->getValue();
		}

		/**
		 * @param bool $strict = true
		 *
		 * @return \Codification\Common\Validation\Rules\Enum
		 */
		public static function rule(bool $strict = true) : Rules\Enum
		{
			return Rules\Enum::make(get_called_class(), $strict);
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder $builder
		 * @param string                                $column
		 *
		 * @return \Illuminate\Database\Eloquent\Builder
		 */
		public static function select(Builder $builder, string $column) : Builder
		{
			$query = $builder->getQuery();

			if (count((array)$query->columns) === 0)
			{
				$builder->addSelect('*');
			}

			$wrapped = $query->grammar->wrap($builder->qualifyColumn($column));
			$alias   = $query->grammar->wrap($column);

			return $builder->selectRaw("({$wrapped} | 0) as {$alias}");
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder      $builder
		 * @param string                                     $column
		 * @param \Codification\Common\Enums\Enum|int|string $value
		 * @param string                                     $boolean = 'and'
		 *
		 * @return \Illuminate\Database\Eloquent\Builder
		 */
		public static function where(Builder $builder, string $column, $value, string $boolean = 'and') : Builder
		{
			$query = $builder->getQuery();

			$wrapped = $query->grammar->wrap($builder->qualifyColumn($column));

			return $builder->whereRaw("({$wrapped} & ?) != 0", [$value], $boolean);
		}
	}
}