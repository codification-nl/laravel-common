<?php

namespace Codification\Common\Money\Exceptions
{
	class CurrencyCodeException extends \UnexpectedValueException
	{
		/**
		 * @param string|null $code = null
		 */
		public function __construct(string $code = null)
		{
			if ($code === null)
			{
				$code = 'null';
			}

			parent::__construct("[$code] is invalid");
		}
	}
}