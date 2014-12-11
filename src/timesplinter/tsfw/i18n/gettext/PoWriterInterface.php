<?php

namespace timesplinter\tsfw\i18n\gettext;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
interface PoWriterInterface
{
	/**
	 * @param string $filePath
	 * @param array $entries
	 *
	 * @return bool
	 */
	public function write($filePath, array $entries);
}

/* EOF */ 