<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;
	use Illuminate\Support\Str;

	/**
	 * @template T
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasToken
	{
		/**
		 * @return void
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
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
			return defined('static::TOKEN_LENGTH') ? (int)static::TOKEN_LENGTH : 60;
		}

		/**
		 * @return string
		 */
		public function getTokenKey() : string
		{
			return isset($this->tokenKey) ? (string)$this->tokenKey : 'token';
		}

		/**
		 * @return mixed
		 * @psalm-return T
		 */
		public function getToken()
		{
			return $this->{$this->getTokenKey()};
		}

		/**
		 * @param int $length
		 *
		 * @return mixed
		 * @psalm-return T
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function generateToken(int $length)
		{
			try
			{
				return Str::random($length);
			}
			catch (\Exception $e)
			{
				throw new ShouldNotHappenException('Failed to generate token', $e);
			}
		}
	}
}