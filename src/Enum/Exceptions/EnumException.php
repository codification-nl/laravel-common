<?php

namespace Codification\Common\Enum\Exceptions
{
	class EnumException extends \UnexpectedValueException
	{
		/**
		 * @param string          $message
		 * @param \Throwable|null $e = null
		 */
		public function __construct(string $message, \Throwable $e = null)
		{
			parent::__construct($message, 0, $e);
		}
	}
}