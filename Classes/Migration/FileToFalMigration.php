<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Dennis Römmich <dennis.roemmich@sunzinet.com>, sunzinet AG
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
 * Class Tx_SzEbook_Migration_FileToFalMigration
 *
 * @package sz_ebook
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_SzEbook_Migration_FileToFalMigration extends Tx_Install_Updates_Base {

	/**
	 * @var string
	 */
	protected $title = 'SzEbook: Change Filehandling to FAL';

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $database;

	protected $table = 'tx_szebook_domain_model_ebook';

	public function __construct()
	{
		$this->database = $this->getDatabaseConnection();
	}

	/**
	 * Checks whether updates are required.
	 *
	 * @param string &$description The description for the update
	 * @return boolean Whether an update is required (TRUE) or not (FALSE)
	 */
	public function checkForUpdate(&$description) {
		$ebooks = $this->getOldEbooks();
		if (count($ebooks > 0)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Performs the accordant updates.
	 *
	 * @param array &$dbQueries Queries done in this update
	 * @param mixed &$customMessages Custom messages
	 * @return boolean Whether everything went smoothly or not
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {
		$oldEbooks = $this->getOldEbooks();

		foreach ($oldEbooks as $ebook) {
			if (is_string($ebook['image'])) {
				$this->migrateFileToFal($ebook['image'], $ebook['uid'], 'image');
			}
			if (is_string($ebook['pdf'])) {
				$this->migrateFileToFal($ebook['pdf'], $ebook['uid'], 'pdf');
			}
		}

		return TRUE;
	}

	/**
	 * @return array
	 */
	protected function getOldEbooks() {
		$ebooks = $this->database->exec_SELECTgetRows('uid, image, pdf', $this->table);
		$oldEbooks = array();
		foreach ($ebooks as $ebook) {
			if (is_string($ebook['image']) || is_string($ebook['pdf'])) {
				$oldEbooks[] = $ebook;
			}
		}

		return $oldEbooks;
	}

	protected function migrateFileToFal($fileName, $uidLocal, $fieldName) {
		$storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
		/** @var $storage \TYPO3\CMS\Core\Resource\ResourceStorage */
		$storage = reset($storageRepository->findAll());

		if (!file_exists(PATH_site . 'uploads/tx_szebook/' . $fileName)) {
			return false;
		}
		$file = $storage->addFile(PATH_site . 'uploads/tx_szebook/' . $fileName, $storage->createFolder($storage->getDefaultFolder()->getIdentifier() . 'SzEbook'), $fileName);

		$data = array();
		$data['sys_file_reference']['NEW1'] = array(
			'uid_local' => $file->getProperty('uid'),
			'uid_foreign' => $uidLocal,
			'tablenames' => $this->table,
			'fieldname' => $fieldName,
			'pid' => 1,
			'table_local' => 'sys_file',
		);
		//$data[$this->table][$uidLocal] = array($fieldName => 'NEW1');

		$beUser = new \TYPO3\CMS\Core\Authentication\BackendUserAuthentication();
		$GLOBALS['BE_USER'] = $beUser;
		$GLOBALS['BE_USER']->user['admin'] = true;

		/** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\DataHandling\DataHandler::class);
		$tce->start($data, array());
		$tce->process_datamap();
		$tce->clear_cacheCmd('pages');

		/** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
		$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
		$fileObjects = $fileRepository->findByRelation($this->table, $fieldName, $uidLocal);
		/** @var \TYPO3\CMS\Core\Resource\FileReference $fileObject */
		$fileObject = reset($fileObjects);

		/** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference */
		$fileReference = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class);
		$fileReference->setOriginalResource($fileObject);

		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($fileReference, __FILE__ . ': ' . __LINE__);

		return $fileReference;
	}
}
