<?php

namespace timesplinter\tsfw\i18n\tests;

use timesplinter\tsfw\i18n\Localizer;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class LocalizerTest extends \PHPUnit_Framework_TestCase
{
	public function testSettingSingleLocaleCategory() {
		$localizer = new Localizer();
		
		$defaultLocales = $localizer->getLocales();
		
		$currentLocales = $localizer->getLocales();
		$currentLocales[LC_ALL] = 'en_US';

		$this->assertEquals('en_US', $localizer->setLocale('en_US', LC_ALL), 'Set single locale which exists');
		$this->assertEquals(false, $localizer->setLocale('foobar', LC_ALL), 'Set single locale which does not exist');

		$this->assertEquals($currentLocales, $localizer->getLocales(), 'Compare locale list');
		$this->assertEquals('en_US', $localizer->getLocale(LC_ALL), 'Compare locale category');
		
		$this->assertEquals($defaultLocales, $localizer->setLocale($defaultLocales));
		$this->assertEquals($defaultLocales, $localizer->getLocales(), 'Same as initial locales');
	}

	public function testSettingMultipleLocaleCategories()
	{
		$localizer = new Localizer();

		$defaultLocales = $localizer->getLocales();
		
		$currentLocales = $localizer->getLocales();
		$currentLocales[LC_ALL] = 'de_DE';
		$currentLocales[LC_TIME] = 'de_DE';

		$this->assertEquals(array(LC_ALL => 'de_DE', LC_TIME => 'de_DE'), $localizer->setLocale('de_DE', array(LC_ALL, LC_TIME)), 'Set single locale which exists');
		$this->assertEquals(false, $localizer->setLocale('foobar', array(LC_ALL, LC_TIME)), 'Set single locale which does not exist');

		$this->assertEquals($currentLocales, $localizer->getLocales(), 'Compare locale list');
		$this->assertEquals('de_DE', $localizer->getLocale(LC_ALL), 'Compare locale category LC_ALL');
		$this->assertEquals('de_DE', $localizer->getLocale(LC_TIME), 'Compare locale category LC_TIME');

		$this->assertEquals($defaultLocales, $localizer->setLocale($defaultLocales));
		$this->assertEquals($defaultLocales, $localizer->getLocales(), 'Same as initial locales');
	}
	
	public function testSettingMultipleLocales()
	{
		$localizer = new Localizer();

		$defaultLocales = $localizer->getLocales();
		
		$currentLocales = $localizer->getLocales();
		$currentLocales[LC_ALL] = 'en_US';
		$currentLocales[LC_TIME] = 'en_US';

		$this->assertEquals(array(LC_ALL => 'en_US', LC_TIME => 'en_US'), $localizer->setLocale('en_US', array(LC_ALL, LC_TIME)), 'Set single locale which exists');
		$this->assertEquals(false, $localizer->setLocale('foobar', array(LC_ALL, LC_TIME)), 'Set single locale which does not exist');

		$this->assertEquals($currentLocales, $localizer->getLocales(), 'Compare locale list');
		$this->assertEquals('en_US', $localizer->getLocale(LC_ALL), 'Compare locale category LC_ALL');
		$this->assertEquals('en_US', $localizer->getLocale(LC_TIME), 'Compare locale category LC_TIME');

		$this->assertEquals($defaultLocales, $localizer->setLocale($defaultLocales));
		$this->assertEquals($defaultLocales, $localizer->getLocales(), 'Same as initial locales');
	}
	
	public function testSettingFirstInvalidLocales()
	{
		$localizer = new Localizer();
		
		$currentLocales = array(
			LC_ALL => 'en_US',
			LC_TIME => 'de_DE'
		);
		
		$this->assertEquals('en_US', $localizer->setLocale(array('foo', 'en_US', 'bar'), LC_ALL), 'Illegal locales');

		$localesArr = array(
			LC_ALL => array('foo', 'en_US', 'bar'),
			LC_TIME => array('foo', 'de_DE', 'baz')
		);
		
		$this->assertEquals($currentLocales, $localizer->setLocale($localesArr), 'Illegal locales');
	}
	
	public function testSettingIllegalLocaleOnlyArray()
	{
		$localizer = new Localizer();

		$localesArr = array(
			LC_ALL => array('foo', 'bar'),
			LC_TIME => array('foo', 'baz')
		);
		
		$this->assertEquals(false, $localizer->setLocale($localesArr), 'Illegal locales');
	}
}

/* EOF */