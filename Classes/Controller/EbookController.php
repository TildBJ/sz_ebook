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

use Sunzinet\SzEbook\Domain\Model\Ebook;
use Sunzinet\SzEbook\Domain\Repository\EbookRepository;
/**
 * Class Tx_SzEbook_Controller_EbookController
 *
 * @package sz_ebook
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EbookController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * ebookRepository
	 *
	 * @var EbookRepository
	 */
	protected $ebookRepository;

	/**
	 * injectEbookRepository
	 *
	 * @param EbookRepository $ebookRepository
	 * @return void
	 */
	public function injectEbookRepository(EbookRepository $ebookRepository) {
		$this->ebookRepository = $ebookRepository;
	}

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction() {
		/** @var $ebook Ebook */
		$ebook = $this->ebookRepository->findByUid($this->settings['pdf']);

		if(is_object($ebook)) {
			$file = PATH_site.$ebook->getPdf();
			$fileinfo = pathinfo($file);

			$this->view->assign('path', $fileinfo['filename']);
			$this->view->assign('ebook', $ebook);
		}
	}
}
