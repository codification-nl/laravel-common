<?php

namespace Codification\Common\Url
{
	use Codification\Common\Enum;

	/**
	 * @method static UrlPart SCHEME()
	 * @method static UrlPart HOST()
	 * @method static UrlPart PORT()
	 * @method static UrlPart PATH()
	 * @method static UrlPart QUERY()
	 * @method static UrlPart FRAGMENT()
	 *
	 * @template-extends \Codification\Common\Enum\Enum<string>
	 */
	final class UrlPart extends Enum\Enum
	{
		/** @var string */
		public const SCHEME = 'scheme';

		/** @var string */
		public const HOST = 'host';

		/** @var string */
		public const PORT = 'port';

		/** @var string */
		public const PATH = 'path';

		/** @var string */
		public const QUERY = 'query';

		/** @var string */
		public const FRAGMENT = 'fragment';
	}
}