<?php

namespace Codification\Common\Database\Eloquent\Contracts
{
	use Codification\Common\Enum;

	/**
	 * @property array<string, string|\Codification\Common\Enum\Enum> $enums
	 * @psalm-property array<string, class-string<\Codification\Common\Enum\Enum<array-key>>> $enums
	 * @psalm-seal-properties
	 */
	interface HasEnums
	{
		/**
		 * @return array<string, string|\Codification\Common\Enum\Enum>
		 * @psalm-return array<string, class-string<\Codification\Common\Enum\Enum<array-key>>>
		 */
		public function getEnums() : array;

		/**
		 * @template    V of array-key
		 * @param int|string                            $value
		 * @psalm-param V                                $value
		 * @param string|\Codification\Common\Enum\Enum $enum
		 * @psalm-param class-string<\Codification\Common\Enum\Enum<V>> $enum
		 *
		 * @return \Codification\Common\Enum\Enum
		 * @psalm-return \Codification\Common\Enum\Enum<V>
		 */
		public function asEnum($value, string $enum) : Enum\Enum;

		/**
		 * @template     V of array-key
		 * @param \Codification\Common\Enum\Enum $enum
		 * @psalm-param \Codification\Common\Enum\Enum<V> $enum
		 *
		 * @return int|string
		 * @psalm-return V
		 */
		public function fromEnum(Enum\Enum $enum);
	}
}