<?php

namespace Codification\Common\Contracts\Support
{
	interface Stringable extends \JsonSerializable
	{
		/**
		 * @return string
		 */
		function toString() : string;

		/**
		 * @return string
		 */
		function __toString() : string;
	}
}