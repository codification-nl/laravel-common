<?php

namespace Codification\Common\Database\Eloquent\Concerns
{
	use Codification\Common\Database\Eloquent\Scopes\EnumScope;
	use Codification\Common\Enums\Enum;

	/**
	 * @mixin \Codification\Common\Database\Eloquent\Contracts\HasEnums
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
		 * @param string $key
		 *
		 * @return mixed|\Codification\Common\Enums\Enum
		 */
		public function getAttributeValue($key)
		{
			/** @noinspection PhpUndefinedClassInspection */
			$value = parent::getAttributeValue($key);

			if ($value !== null && $this->isEnumAttribute($key))
			{
				return $this->asEnum($value, $this->enums[$key]);
			}

			return $value;
		}

		/**
		 * @param string                                $key
		 * @param mixed|\Codification\Common\Enums\Enum $value
		 *
		 * @return mixed
		 */
		public function setAttribute($key, $value)
		{
			if ($value !== null && $this->isEnumAttribute($key))
			{
				$this->enums[$key]::assertType($value);

				$value = $this->fromEnum($value);
			}

			/** @noinspection PhpUndefinedClassInspection */
			return parent::setAttribute($key, $value);
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
		protected function isEnumAttribute(string $key) : bool
		{
			return array_key_exists($key, $this->enums);
		}
	}
}