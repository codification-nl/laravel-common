<?php

namespace Codification\Common\Support
{
	use Illuminate\Support\Str;

	/**
	 * @mixin \Codification\Common\Contracts\Support\Tokenable
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasToken
	{
		public function initializeHasToken() : void
		{
			$this->attributes[$this->getTokenKey()] = $this->generateToken($this->getTokenLength());
		}

		/**
		 * @return string
		 */
		public function getRouteKeyName()
		{
			return $this->getTokenKey();
		}

		/**
		 * @return int
		 */
		public function getTokenLength() : int
		{
			/** @noinspection PhpUndefinedClassConstantInspection */
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return defined('static::TOKEN_LENGTH') ? static::TOKEN_LENGTH : 60;
		}

		/**
		 * @return string
		 */
		public function getTokenKey() : string
		{
			return isset($this->tokenKey) ? $this->tokenKey : 'token';
		}

		/**
		 * @return mixed
		 */
		public function getToken()
		{
			return $this->{$this->getTokenKey()};
		}

		/**
		 * @param int $length
		 *
		 * @return string
		 */
		public function generateToken(int $length) : string
		{
			return Str::random($length);
		}
	}
}