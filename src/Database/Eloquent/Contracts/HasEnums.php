<?php

namespace Codification\Common\Database\Eloquent\Contracts
{
	use Codification\Common\Support\Enum;

	/**
	 * @property string[]|\Codification\Common\Support\Enum[] $enums
	 */
	interface HasEnums
	{
		/**
		 * @param string $key
		 *
		 * @return mixed|\Codification\Common\Support\Enum
		 */
		public function getAttributeValue($key);

		/**
		 * @param string                                  $key
		 * @param mixed|\Codification\Common\Support\Enum $value
		 *
		 * @return mixed
		 */
		public function setAttribute($key, $value);

		/**
		 * @return string[]|\Codification\Common\Support\Enum[]
		 */
		public function getEnums() : array;

		/**
		 * @param int|string                               $value
		 * @param string|\Codification\Common\Support\Enum $enum
		 *
		 * @return \Codification\Common\Support\Enum
		 */
		public function asEnum($value, string $enum) : Enum;

		/**
		 * @param \Codification\Common\Support\Enum
		 *
		 * @return int|string
		 */
		public function fromEnum(Enum $enum);
	}
}