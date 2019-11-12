<?php

namespace Codification\Common\Enum\Exceptions
{
	class ParseException extends \OutOfBoundsException
	{
		/**
		 * @param string $name
		 * @param string $enum
		 * @psalm-param class-string<\Codification\Common\Enum\Enum> $enum
		 */
		public function __construct(string $name, string $enum)
		{
			parent::__construct(sprintf("'%s' does not exist in [%s]", $name, $enum));
		}
	}
}