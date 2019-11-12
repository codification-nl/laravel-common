<?php

namespace Codification\Common\Support\Exceptions
{
	class ResolutionException extends \RuntimeException
	{
		/**
		 * @param string          $abstract
		 * @param \Throwable|null $e = null
		 */
		public function __construct(string $abstract, \Throwable $e = null)
		{
			parent::__construct("Failed to resolve [$abstract] container", 0, $e);
		}
	}
}