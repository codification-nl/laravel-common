<?php

namespace Codification\Common\Support
{
	use Codification\Common\Url\UrlSafe;

	final class SecureRandom
	{
		/**
		 * @param int  $length  = 16
		 * @param bool $padding = false
		 *
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function urlsafe_base64(int $length = 16, bool $padding = false) : string
		{
			try
			{
				return UrlSafe::base64_encode(random_bytes($length), $padding);
			}
			catch (\Exception $e)
			{
				throw new Exceptions\ShouldNotHappenException('Failed to generate random bytes', $e);
			}
		}
	}
}