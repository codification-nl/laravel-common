<?php

namespace Codification\Common\Enum
{
	/**
	 * @mixin \Codification\Common\Enum\Enum<int>
	 */
	trait EnumFlags
	{
		/**
		 * @param int|string|array<string> $value
		 * @psalm-param numeric|string|list<string> $value
		 *
		 * @return int
		 */
		public static function initializeEnumFlags($value) : int
		{
			if (is_numeric($value))
			{
				return intval($value);
			}

			if (empty($value))
			{
				return 0;
			}

			if (!is_array($value))
			{
				$value = explode(',', $value);
			}

			/** @var int $result */
			$result = array_reduce($value, function (int $result, string $string) : int
				{
					/** @psalm-var \Codification\Common\Enum\Enum<int> $enum */
					$enum = static::parse($string);

					return $result | $enum->value;
				}, 0);

			return $result;
		}

		/**
		 * @param array<string, int> $values
		 *
		 * @return array<string, int>
		 */
		public static function toArrayEnumFlags(array $values) : array
		{
			/**
			 * @var string[] $keys
			 * @psalm-var list<string> $keys
			 */

			$keys  = array_diff(array_keys($values), static::$hidden);
			$count = 1 << count($keys);

			for ($value = 1; $value < $count; $value++)
			{
				if (in_array($value, $values))
				{
					continue;
				}

				/**
				 * @var string[] $pieces
				 * @psalm-var list<string> $pieces
				 */
				$pieces = [];

				foreach ($keys as $key)
				{
					if (($value & $values[$key]) !== 0)
					{
						$pieces[] = $key;
					}
				}

				$key = implode('_', $pieces);

				$values[$key]     = $value;
				static::$hidden[] = $key;
			}

			return $values;
		}

		/**
		 * @param \Codification\Common\Enum\Enum $enum
		 * @psalm-param \Codification\Common\Enum\Enum<int> $enum
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function has($enum) : bool
		{
			static::assertType($enum);

			return (($this->value & $enum->value) != 0);
		}

		/**
		 * @param \Codification\Common\Enum\Enum ...$enums
		 * @psalm-param \Codification\Common\Enum\Enum<int> ...$enums
		 *
		 * @return $this
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function set(...$enums) : self
		{
			return $this->op($enums, function (int $lhs, int $rhs) : int
				{
					return $lhs | $rhs;
				});
		}

		/**
		 * @param \Codification\Common\Enum\Enum ...$enums
		 * @psalm-param \Codification\Common\Enum\Enum<int> ...$enums
		 *
		 * @return $this
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function remove(...$enums) : self
		{
			return $this->op($enums, function (int $lhs, int $rhs) : int
				{
					return $lhs & ~$rhs;
				});
		}

		/**
		 * @param \Codification\Common\Enum\Enum ...$enums
		 * @psalm-param \Codification\Common\Enum\Enum<int> ...$enums
		 *
		 * @return $this
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function toggle(...$enums) : self
		{
			return $this->op($enums, function (int $lhs, int $rhs) : int
				{
					return $lhs ^ $rhs;
				});
		}

		/**
		 * @param \Codification\Common\Enum\Enum[] $enums
		 * @psalm-param list<\Codification\Common\Enum\Enum<int>> $enums
		 * @param \Closure                         $op
		 * @psalm-param \Closure(int, int):int     $op
		 * @return $this
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		private function op(array $enums, \Closure $op) : self
		{
			foreach ($enums as $enum)
			{
				static::assertType($enum);

				$this->value = $op($this->value, $enum->value);
			}

			return $this;
		}
	}
}