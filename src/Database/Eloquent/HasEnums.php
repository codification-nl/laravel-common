<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Enum;

	/**
	 * @mixin \Codification\Common\Database\Eloquent\Contracts\HasEnums
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasGlobalScopes
	 */
	trait HasEnums
	{
		/**
		 * @return void
		 * @throws \InvalidArgumentException
		 */
		public static function bootHasEnums() : void
		{
			static::addGlobalScope(new EnumScope());
		}

		/**
		 * @template  T
		 * @param string                               $key
		 * @param mixed|\Codification\Common\Enum\Enum $out
		 * @param-out mixed|\Codification\Common\Enum\Enum<T> $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function getHasEnumsValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isEnumAttribute($key))
			{
				return false;
			}

			$out = $this->asEnum($out, $this->enums[$key]);

			return true;
		}

		/**
		 * @template  T
		 * @param string                               $key
		 * @param mixed|\Codification\Common\Enum\Enum $out
		 * @param-out mixed|\Codification\Common\Enum\Enum<T> $out
		 *
		 * @return bool
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function setHasEnumsValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isEnumAttribute($key))
			{
				return false;
			}

			/**
			 * @var string|\Codification\Common\Enum\Enum $enum
			 * @psalm-var class-string<\Codification\Common\Enum\Enum>
			 */
			$enum = $this->enums[$key];

			$enum::assertType($out);

			$out = $this->fromEnum($out);

			return true;
		}

		/**
		 * @template     T
		 * @return array<string, string|\Codification\Common\Enum\Enum>
		 * @psalm-return array<string, class-string<\Codification\Common\Enum\Enum<T>>>
		 */
		public function getEnums() : array
		{
			return $this->enums;
		}

		/**
		 * @template    T
		 * @param int|string                            $value
		 * @psalm-param T                                $value
		 * @param string|\Codification\Common\Enum\Enum $enum
		 * @psalm-param class-string<\Codification\Common\Enum\Enum<T>> $enum
		 *
		 * @return \Codification\Common\Enum\Enum
		 * @psalm-return \Codification\Common\Enum\Enum<T>
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function asEnum($value, string $enum) : Enum\Enum
		{
			return $enum::make($value);
		}

		/**
		 * @template     T
		 * @param \Codification\Common\Enum\Enum $enum
		 * @psalm-param \Codification\Common\Enum\Enum<T> $enum
		 *
		 * @return int|string
		 * @psalm-return T
		 */
		public function fromEnum(Enum\Enum $enum)
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