<?php

namespace timesplinter\tsfw\i18n\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
abstract class AbstractTranslator
{
	protected $directory;
	
	public function __construct($directory)
	{
		$this->directory = $directory;
	}
	
	/**
	 * Lookup a message in the current domain
	 *
	 * @param string $message The message being translated.
	 * @param string|null $pluralMessage
	 * @param int $n
	 *
	 * @return string Returns a translated string if one is found in the translation table, or the submitted message if not found.
	 */
	public abstract function getText($message, $pluralMessage = null, $n = 0);

	/**
	 * Lookup a message in the current domain
	 *
	 * @param string $message The message being translated.
	 * @param string|null $pluralMessage
	 * @param int $n
	 *
	 * @return string Returns a translated string if one is found in the translation table, or the submitted message if not found.
	 */
	public final function _($message, $pluralMessage = null, $n = 0)
	{
		return $this->getText($message, $pluralMessage, $n);
	}

	/**
	 * @return string
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * @param string $directory
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
	}
}

/* EOF */