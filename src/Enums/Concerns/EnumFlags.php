<?php

namespace Codification\Common\Enums\Concerns
{
	/**
	 * @mixin \Codification\Common\Support\Enum
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
		 * @param \Codification\Common\Support\Enum $enum
		 *
		 * @return bool
		 */
		public function has($enum) : bool
		{
			static::assertType($enum);

			return (($this->value & $enum->value) != 0);
		}

		/**
		 * @param \Codification\Common\Support\Enum[] ...$enums
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
		 * @param \Codification\Common\Support\Enum[] ...$enums
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