<?php

namespace Codification\Common\Enums
{
	/**
	 * @mixin \Codification\Common\Enums\Enum
	 */
	trait EnumFlags
	{
		/**
		 * @param int|string|string[] $value
		 *
		 * @return int
		 */
		public static function initializeEnumFlags($value) : int
		{
			if (is_numeric($value))
			{
				return $value;
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
					return $result | static::parse($string)->value;
				}, 0);

			return $result;
		}

		/**
		 * @param array $values
		 *
		 * @return array
		 */
		public static function toArrayEnumFlags(array $values) : array
		{
			$keys  = array_diff(array_keys($values), static::$hidden);
			$count = 1 << count($keys);

			for ($i = 1; $i < $count; $i++)
			{
				if (in_array($i, $values))
				{
					continue;
				}

				$key = [];

				foreach ($keys as $name)
				{
					if (($i & $values[$name]) !== 0)
					{
						$key[] = $name;
					}
				}

				$key = implode('_', $key);

				$values[$key]     = $i;
				static::$hidden[] = $key;
			}

			return $values;
		}

		/**
		 * @param \Codification\Common\Enums\Enum $enum
		 *
		 * @return bool
		 */
		public function has($enum) : bool
		{
			static::assertType($enum);

			return (($this->value & $enum->value) != 0);
		}

		/**
		 * @param \Codification\Common\Enums\Enum[] ...$enums
		 *
		 * @return $this
		 */
		public function set(...$enums) : self
		{
			foreach ($enums as $enum)
			{
				static::assertType($enum);

				$this->value |= $enum->value;
			}

			return $this;
		}

		/**
		 * @param \Codification\Common\Enums\Enum[] ...$enums
		 *
		 * @return $this
		 */
		public function remove(...$enums) : self
		{
			foreach ($enums as $enum)
			{
				static::assertType($enum);

				$this->value &= ~$enum->value;
			}

			return $this;
		}
	}
}