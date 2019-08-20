<?php

namespace Codification\Common\Enums
{
	/**
	 * @mixin \Codification\Common\Contracts\Enums\HasEnums
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasGlobalScopes
	 */
	trait HasEnums
	{
		/**
		 * @return void
		 */
		public static function bootHasEnums() : void
		{
			static::addGlobalScope(new EnumScope());
		}

		/**
		 * @param string                                 $key
		 * @param mixed|\Codification\Common\Enums\Enum &$value
		 *
		 * @return bool
		 */
		public function getHasEnumsValue(string $key, &$value) : bool
		{
			if (!$this->isEnumAttribute($key))
			{
				return false;
			}

			$value = $this->asEnum($value, $this->enums[$key]);

			return true;
		}

		/**
		 * @param string                                 $key
		 * @param mixed|\Codification\Common\Enums\Enum &$value
		 *
		 * @return bool
		 */
		public function setHasEnumsValue(string $key, &$value) : bool
		{
			if (!$this->isEnumAttribute($key))
			{
				return false;
			}

			$this->enums[$key]::assertType($value);

			$value = $this->fromEnum($value);

			return true;
		}

		/**
		 * @return string[]|\Codification\Common\Enums\Enum[]
		 */
		public function getEnums() : array
		{
			return $this->enums;
		}

		/**
		 * @param int|string                             $value
		 * @param string|\Codification\Common\Enums\Enum $enum
		 *
		 * @return \Codification\Common\Enums\Enum
		 */
		public function asEnum($value, string $enum) : Enum
		{
			return $enum::make($value);
		}

		/**
		 * @param \Codification\Common\Enums\Enum $enum
		 *
		 * @return int|string
		 */
		public function fromEnum(Enum $enum)
		{
			return $enum->getValue();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isEnumCastable(string $key) : bool
		{
			return array_key_exists($key, $this->enums);
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isEnumAttribute(string $key) : bool
		{
			return $this->isEnumCastable($key);
		}
	}
}