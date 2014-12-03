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
	 * 
	 * @return array
	 */
	public function extract($filePath);
}

/* EOF */ 