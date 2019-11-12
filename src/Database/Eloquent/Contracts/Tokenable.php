<?php

namespace Codification\Common\Database\Eloquent\Contracts
{
	/**
	 * @template T
	 * @property string $tokenKey
	 * @psalm-seal-properties
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
		 * @return mixed
		 * @psalm-return T
		 */
		public function generateToken(int $length);

		/**
		 * @return mixed
		 * @psalm-return T
		 */
		public function getToken();
	}
}