<?php

namespace Codification\Common\Support
{
	final class SecureRandom
	{
		/**
		 * @param int  $length
		 * @param bool $padding
		 *
		 * @return string
		 */
		public static function urlsafe_base64(int $length = 16, bool $padding = false) : string
		{
			try
			{
				return UrlSafe::base64_encode(random_bytes($length), $padding);
			}
			catch (\Exception $e)
			{
				throw new \UnexpectedValueException('Failed to generate random bytes', 0, $e->getPrevious());
			}
		}
	}
}