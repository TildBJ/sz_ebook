<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Dennis Römmich <dennis.roemmich@sunzinet.com>, sunzinet AG
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class Tx_SzEbook_Domain_Model_Ebook.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage E Book
 *
 * @author Dennis Römmich <dennis.roemmich@sunzinet.com>
 */
class Tx_SzEbook_Domain_Model_EbookTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_SzEbook_Domain_Model_Ebook
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_SzEbook_Domain_Model_Ebook();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getEReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setPdfForStringSetsPdf() {
		$this->fixture->setPdf('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getPdf()
		);
	}

}
?>