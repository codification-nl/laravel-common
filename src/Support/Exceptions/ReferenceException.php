<?php

namespace Codification\Common\Support\Exceptions
{
	class ReferenceException extends \UnexpectedValueException
	{
		/**
		 * @param string $var
		 */
		public function __construct(string $var)
		{
			parent::__construct("$var === null");
		}
	}
}