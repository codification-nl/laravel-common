<?php

namespace Codification\Common\Country\Exceptions
{
	class CountryCodeException extends \UnexpectedValueException
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