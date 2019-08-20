<?php

namespace Codification\Common\Contracts\Enums
{
	use Codification\Common\Enums\Enum;

	/**
	 * @property string[]|\Codification\Common\Enums\Enum[] $enums
	 */
	interface HasEnums
	{
		/**
		 * @return string[]|\Codification\Common\Enums\Enum[]
		 */
		public function getEnums() : array;

		/**
		 * @param int|string                             $value
		 * @param string|\Codification\Common\Enums\Enum $enum
		 *
		 * @return \Codification\Common\Enums\Enum
		 */
		public function asEnum($value, string $enum) : Enum;

		/**
		 * @param \Codification\Common\Enums\Enum
		 *
		 * @return int|string
		 */
		public function fromEnum(Enum $enum);
	}
}