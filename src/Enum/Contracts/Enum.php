<?php

namespace Codification\Common\Enum\Contracts
{
	/**
	 * @template T of array-key
	 */
	interface Enum
	{
		/**
		 * @return int|string
		 * @psalm-return T
		 */
		function getValue();

		/**
		 * @param int|string $value
		 * @psalm-param T    $value
		 *
		 * @return void
		 */
		function setValue($value) : void;

		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Common\Enum\Enum<T>>
		 */
		function getClass() : string;
	}
}