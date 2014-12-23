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
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @package sz_ebook
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_SzEbook_Domain_Model_Ebook extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * pdf
	 *
	 * @var string
	 */
	protected $pdf;

	/**
	 * image
	 *
	 * @var string
	 */
	protected $image;

	/**
	 * header
	 *
	 * @var string
	 */
	protected $header;

	/**
	 * turnjs
	 *
	 * @var bool
	 */
	protected $turnjs;

	/**
	 * Returns the image
	 *
	 * @return string $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param string $image
	 * @return Tx_SzEbook_Domain_Model_Ebook
	 */
	public function setImage($image) {
		$this->image = $image;

		return $this;
	}

	/**
	 * Returns the pdf
	 *
	 * @return string $pdf
	 */
	public function getPdf() {
		return $this->pdf;
	}

	/**
	 * Sets the pdf
	 *
	 * @param string $pdf
	 * @return Tx_SzEbook_Domain_Model_Ebook
	 */
	public function setPdf($pdf) {
		$this->pdf = $pdf;

		return $this;
	}

	/**
	 * Returns the header
	 *
	 * @return string $header
	 */
	public function getHeader() {
		return $this->header;
	}

	/**
	 * Sets the header
	 *
	 * @param string $header
	 * @return Tx_SzEbook_Domain_Model_Ebook
	 */
	public function setHeader($header) {
		$this->header = $header;

		return $this;
	}

	/**
	 * Sets or returns Turnjs
	 *
	 * @param bool $isTurnjs
	 * @return Tx_SzEbook_Domain_Model_Ebook|bool
	 */
	public function isTurnjs($isTurnjs = false) {
		if(!$isTurnjs) {
			return $this->turnjs;
		}

		$this->turnjs = $isTurnjs;

		return $this;
	}

}
?>