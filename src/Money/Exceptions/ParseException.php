<?php

namespace Codification\Common\Money\Exceptions
{
	class ParseException extends \RuntimeException
	{
		/**
		 * @param mixed           $value
		 * @param \Throwable|null $previous = null
		 */
		public function __construct($value, \Throwable $previous = null)
		{
			parent::__construct(sprintf('Unable to parse %s', strval($value)), 0, $previous);
		}
	}
}