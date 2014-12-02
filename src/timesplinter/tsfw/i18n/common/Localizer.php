<?php

namespace timesplinter\tsfw\i18n\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Localizer
{
	protected $localeCategories;
	protected $categories;

	public function __construct()
	{
		$this->initLocaleCategories();
	}

	/**
	 * @param string $acceptedLanguagesString
	 * @param string $default
	 *
	 * @return Localizer
	 */
	public static function fromAcceptedLanguages($acceptedLanguagesString, $default = 'C', $categories = null)
	{
		$acceptedLanguageArray = array();
		$localeArr = array();
		
		foreach(explode(',', $acceptedLanguagesString) as $lang) {
			if(preg_match('/([a-z-]{2,})(?:;q=(.+))?/i', $lang, $matches) === 0)
				continue;
			
			$acceptedLanguageArray[strtr($matches[1], array('-' => '_'))] = isset($matches[2]) ? (float)$matches[2] : 1.0;
		}
		
		uasort($acceptedLanguageArray, function($a, $b) {
			if($a === $b) return 0;
			elseif($a < $b) return 1;
			else return 0;
		});

		$localizer = new Localizer();
		
		foreach($acceptedLanguageArray as $locale => $weight) {
			$localeArr[LC_ALL][] = $locale;
		}
				
		var_dump($localizer->setLocale($localeArr));
		
		return $localizer;
	}
	
	protected function initLocaleCategories()
	{
		$this->localeCategories = array(
			LC_ALL,
			LC_COLLATE,
			LC_CTYPE,
			LC_MONETARY,
			LC_NUMERIC,
			LC_TIME
		);
		
		if(defined('LC_MESSAGES') === true)
			$this->localeCategories[] = LC_MESSAGES;
	}

	/**
	 * @param array $locales A single locale value as string or an array with category and corresponding locale(s)
	 *
	 * @throws \UnexpectedValueException
	 * @return array|bool Returns an array of the specific locale set for the provided categories or false
	 */
	public function setLocale(array $locales)
	{
		$localesSet = array();
				
		foreach($locales as $category => $catLocales) {
			if(in_array($category, $this->localeCategories) === false)
				throw new \UnexpectedValueException('Invalid category: ' . $category);
			
			$setCatLocale = setlocale($category, $catLocales);
			
			if(in_array($category, array(LC_ALL, LC_MESSAGES)) === true)
				putenv('LANG=' . $setCatLocale);
			
			$localesSet[$category] = $setCatLocale;
		}

		return $localesSet;
	}

	/**
	 * Get all categories with its corresponding locale set
	 * 
	 * @return array List of set locales. Category as key and its corresponding locale as value.
	 */
	public function getLocales()
	{
		$currentLocales = array();
		
		foreach($this->localeCategories as $category) {
			$currentLocales[$category] = setlocale($category, '0');
		}
		
		return $currentLocales;
	}

	/**
	 * Get the current locale value set for a specific category
	 *
	 * @param int $category
	 *
	 * @return string The current locale for this category
	 */
	public function getLocale($category)
	{
		return setlocale($category, '0');
	}
}

/* EOF */