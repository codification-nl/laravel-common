<?php

namespace Codification\Common\Enum\Exceptions
{
	class ValueException extends \UnexpectedValueException
	{
		/**
		 * @param int|string $value
		 * @psalm-param array-key $value
		 * @param string     $enum
		 * @psalm-param class-string<\Codification\Common\Enum\Enum> $enum
		 */
		public function __construct($value, string $enum)
		{
			parent::__construct(sprintf('Unexpected value \'%s\' for [%s]', strval($value), $enum));
		}
	}
}