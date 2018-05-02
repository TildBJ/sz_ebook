<?php
namespace TildBJ\SzEbook\Migration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Dennis Römmich <dennis@roemmich.eu>, sunzinet AG
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Class FileToFalMigration
 *
 * @package sz_ebook
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileToFalMigration extends \TYPO3\CMS\Install\Updates\AbstractUpdate
{

    /**
     * @var string
     */
    protected $title = 'SzEbook: Change Filehandling to FAL';

    /**
     * @var \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected $queryBuilder;

    protected $table = 'tx_szebook_domain_model_ebook';

    public function __construct()
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->table);
    }

    /**
     * Checks whether updates are required.
     *
     * @param  string &$description The description for the update
     * @return boolean Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        $ebooks = $this->getOldEbooks();
        if (count($ebooks) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Performs the accordant updates.
     *
     * @param  array &$dbQueries      Queries done in this update
     * @param  mixed &$customMessages Custom messages
     * @return boolean Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        $oldEbooks = $this->getOldEbooks();

        foreach ($oldEbooks as $ebook) {
            if (!intval($ebook['image'])) {
                $uid = $this->migrateFileToFal($ebook['image'], $ebook['uid'], 'image');
                $this->queryBuilder
                    ->update($this->table)
                    ->where($this->queryBuilder->expr()->eq('uid',$ebook['uid']))
                    ->set('image', $uid)
                    ->execute();
            }
            if (!intval($ebook['pdf'])) {
                $uid = $this->migrateFileToFal($ebook['pdf'], $ebook['uid'], 'pdf');
                $this->queryBuilder
                    ->update($this->table)
                    ->where($this->queryBuilder->expr()->eq('uid', $ebook['uid']))
                    ->set('pdf', $uid)
                    ->execute();
            }
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getOldEbooks()
    {
        $ebooks = $this->queryBuilder
            ->select('uid', 'image', 'pdf')
            ->from($this->table)
            ->execute()
            ->fetchAll();

        $oldEbooks = array();
        foreach ($ebooks as $ebook) {
            if (!intval($ebook['image']) || !intval($ebook['pdf'])) {
                $oldEbooks[] = $ebook;
            }
        }

        return $oldEbooks;
    }

    /**
     * @param $fileName
     * @param $uidLocal
     * @param $fieldName
     * @return bool|int
     */
    protected function migrateFileToFal($fileName, $uidLocal, $fieldName)
    {
        $storageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
        /** @var $storage \TYPO3\CMS\Core\Resource\ResourceStorage */
        $storage = reset($storageRepository->findAll());

        if (!file_exists(PATH_site . 'uploads/tx_szebook/' . $fileName)) {
            return false;
        }
        $file = $storage->addFile(
            PATH_site . 'uploads/tx_szebook/' . $fileName,
            $storage->createFolder($storage->getDefaultFolder()->getIdentifier() . 'SzEbook'),
            $fileName
        );

        $GLOBALS["TYPO3_DB"]->store_lastBuiltQuery = true;

        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference');
        $databaseConnection->insert(
            'sys_file_reference',
            [
                'uid_local' => $file->getProperty('uid'),
                'uid_foreign' => $uidLocal,
                'tablenames' => $this->table,
                'fieldname' => $fieldName,
                'tstamp' => time(),
                'crdate' => time(),
                'pid' => 1,
                'table_local' => 'sys_file',
            ]
        );
        $lastUid = $databaseConnection->lastInsertId('sys_file_reference');

        return $lastUid;
    }
}
