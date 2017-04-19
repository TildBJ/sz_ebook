<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'TildBJ.' . $_EXTKEY,
    'Pi1',
    array(
        'Ebook' => 'show',

    ),
    // non-cacheable actions
    array(
        'Ebook' => '',

    )
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['szEbookFileToFalMigration'] =
    \TildBJ\SzEbook\Migration\FileToFalMigration::class;
