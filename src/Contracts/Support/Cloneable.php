<?php

namespace Codification\Common\Contracts\Support
{
	/**
	 * @template T
	 */
	interface Cloneable
	{
		/**
		 * @psalm-return T
		 */
		function copy();

		/**
		 * @psalm-return T
		 */
		function clone();

		/**
		 * @psalm-return T
		 */
		function __clone();
	}
}