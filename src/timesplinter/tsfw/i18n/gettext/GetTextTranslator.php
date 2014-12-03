<?php

namespace timesplinter\tsfw\i18n\gettext;

use PoParser\Parser;
use timesplinter\tsfw\common\StringUtils;
use timesplinter\tsfw\i18n\common\AbstractTranslator;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdeveloper
 */
class GetTextTranslator extends AbstractTranslator
{
	protected $bendTextDomains = array();
	protected $currentTextDomain = null;
	/** @var PoParserInterface|null */
	protected $poParserInterface = null;
	/** @var PoWriterInterface|null */
	protected $poWriterInterface = null;
	
	public function __construct($directory, $defaultCodeSet = 'UTF-8')
	{
		parent::__construct($directory, $defaultCodeSet);
	}
	
	/**
	 * Set the current text domain
	 *
	 * @param string $textDomain The text domain to set active
	 * @param string|null $codeSet Optional charset for this text domain
	 *
	 * @return string
	 */
	public function setTextDomain($textDomain, $codeSet = null)
	{
		if(isset($this->bendTextDomains[$textDomain]) === false)
			$this->bindTextDomain($textDomain, $codeSet);
		
		return $this->currentTextDomain = textdomain($textDomain);
	}

	/**
	 * Register a new text domain
	 *
	 * @param string $textDomain The text domain to set active
	 * @param string|null $codeSet Optional charset for this text domain
	 */
	protected function bindTextDomain($textDomain, $codeSet = null)
	{
		$this->bendTextDomains[$textDomain] = array();
		
		$textDomainDir = bindtextdomain($textDomain, $this->directory);
		
		$translationFileDir = $textDomainDir . DIRECTORY_SEPARATOR . StringUtils::beforeFirst(setlocale(LC_MESSAGES, '0'), '.') . DIRECTORY_SEPARATOR . 'LC_MESSAGES';
		
		$moFilePath = $translationFileDir . DIRECTORY_SEPARATOR . $textDomain . '.mo';
		$poFilePath = $translationFileDir . DIRECTORY_SEPARATOR . $textDomain . '.po';
		
		if(file_exists($moFilePath) === true) {
			$this->bendTextDomains[$textDomain]['file_path'] = $moFilePath;
			$this->bendTextDomains[$textDomain]['type'] = 'mo';
		} elseif($this->poParserInterface instanceof PoParserInterface === true && file_exists($poFilePath) === true) {
			$this->bendTextDomains[$textDomain]['file_path'] = $poFilePath;
			$this->bendTextDomains[$textDomain]['type'] = 'po';
			$this->bendTextDomains[$textDomain]['entries'] = $this->poParserInterface->extract($poFilePath);
			$this->bendTextDomains[$textDomain]['plural_expr'] = false;
			
			if(isset($this->bendTextDomains[$textDomain]['entries']['']) === true) {
				foreach($this->bendTextDomains[$textDomain]['entries']['']['msgstr'] as $meta) {
					if(preg_match('/Plural-Forms:\s+nplurals=(\d+);\s+(plural=[^;]+)/', $meta, $matches) === 0)
						continue;

					$this->bendTextDomains[$textDomain]['plural_expr'] = '$' . $matches[2] . ';';
				}
			}
		}
		
		$textDomainCodeSet = ($codeSet !== null)?$codeSet:$this->defaultCodeSet;
		
		if(bind_textdomain_codeset($textDomain, $textDomainCodeSet) === $textDomainCodeSet)
			$this->bendTextDomains[$textDomain]['code_set'] = $textDomainCodeSet;
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
	public function getText($message, $pluralMessage = null, $n = 0)
	{
		if(isset($this->bendTextDomains[$this->currentTextDomain]['entries']) === true) {
			$msgstrOffset = (int)($n > 0);
			
			if($pluralMessage !== null) {
				if(isset($this->bendTextDomains[$this->currentTextDomain]['plural_expr']) === true) {
					$plural = 0;
					
					eval(strtr($this->bendTextDomains[$this->currentTextDomain]['plural_expr'], array('n' => $n)));

					$msgstrOffset = (int)$plural;
				}
			}
			
			return isset($this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][$msgstrOffset]) ? $this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][$msgstrOffset] : $message;
		}
		
		if($pluralMessage === null)
			return gettext($message);
	
		return ngettext($message, $pluralMessage, $n);
	}

	/**
	 * Get the currently active text domain
	 * 
	 * @return string
	 */
	public function getTextDomain()
	{
		return textdomain(null);
	}

	/**
	 * Enables support for PO files if a parser is set
	 * @param PoParserInterface $parser
	 */
	public function setPoParser(PoParserInterface $parser)
	{
		$this->poParserInterface = $parser;
	}
}

/* EOF */