<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'eBook'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'eBook');

t3lib_extMgm::addLLrefForTCAdescr('tx_szebook_domain_model_ebook', 'EXT:sz_ebook/Resources/Private/Language/locallang_csh_tx_szebook_domain_model_ebook.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_szebook_domain_model_ebook');
$TCA['tx_szebook_domain_model_ebook'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:sz_ebook/Resources/Private/Language/locallang_db.xml:tx_szebook_domain_model_ebook',
		'label' => 'header',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'pdf,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Ebook.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_szebook_domain_model_ebook.gif'
	),
);

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/setup.xml');

?>