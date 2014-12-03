<?php

namespace timesplinter\tsfw\i18n\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
abstract class AbstractTranslator
{
	protected $directory = null;
	protected $defaultCodeSet = null;
	protected $currentTextDomain = null;

	/**
	 * @param string $directory
	 * @param string $defaultCodeSet
	 */
	public function __construct($directory, $defaultCodeSet)
	{
		$this->directory = $directory;
		$this->defaultCodeSet = $defaultCodeSet;
	}

	/**
	 * Loads/registers a new text domain
	 * 
	 * @param string $textDomain
	 * @param string $codeSet
	 */
	public abstract function bindTextDomain($textDomain, $codeSet);

	/**
	 * Set the current text domain
	 *
	 * @param string $textDomain The text domain to set active
	 *
	 * @return string
	 */
	public abstract function setTextDomain($textDomain);
	
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
	 * Lookup a message in the specified domain
	 * 
	 * @param string $domain
	 * @param string $message
	 * @param string|null $pluralMessage
	 * @param int $n
	 *
	 * @return string Returns a translated string if one is found in the translation table, or the submitted message if not found.
	 */
	public function dGetText($domain, $message, $pluralMessage = null, $n = 0)
	{
		$oldTextDomain = $this->currentTextDomain;
		
		$this->setTextDomain($domain);
		
		$message = $this->getText($message, $pluralMessage, $n);
		
		$this->setTextDomain($oldTextDomain);
		
		return $message;
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
	public final function _($message, $pluralMessage = null, $n = 0)
	{
		return $this->getText($message, $pluralMessage, $n);
	}

	/**
	 * Lookup a message in the specified domain
	 * 
	 * @param string $domain
	 * @param string $message
	 * @param string|null $pluralMessage
	 * @param int $n
	 *
	 * @return string Returns a translated string if one is found in the translation table, or the submitted message if not found.
	 */
	public final function _d($domain, $message, $pluralMessage = null, $n = 0)
	{
		return $this->dGetText($domain, $message, $pluralMessage, $n);
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