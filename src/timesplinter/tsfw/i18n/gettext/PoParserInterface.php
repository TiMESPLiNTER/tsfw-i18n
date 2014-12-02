<?php

namespace timesplinter\tsfw\i18n\gettext;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
interface PoParserInterface
{
	/**
	 * @param string $filePath
	 */
	public function extract($filePath);

	/**
	 * @param string $msgId
	 * @param string $msgIdPlural
	 * @param int $n
	 *
	 * @return array
	 */
	public function getText($msgId, $msgIdPlural, $n);
}

/* EOF */ 