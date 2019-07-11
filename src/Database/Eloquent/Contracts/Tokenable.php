<?php

namespace Codification\Common\Database\Eloquent\Contracts
{
	/**
	 * @property string $tokenKey
	 */
	interface Tokenable
	{
		/**
		 * @return int
		 */
		public function getTokenLength() : int;

		/**
		 * @return string
		 */
		public function getTokenKey() : string;

		/**
		 * @param int $length
		 *
		 * @return string
		 */
		public function getToken(int $length) : string;
	}
}