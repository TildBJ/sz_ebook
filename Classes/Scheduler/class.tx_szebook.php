<?php

/**
 * Description of the phpfile 'class.tx_szebook.php'
 *
 * @author Dennis Römmich <dennis@roemmich.eu>
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */


class tx_szebook extends tx_scheduler_Task {

	/**
	 * TypoScript settings
	 *
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * TypoScript persistence
	 *
	 * @var array $persistence
	 */
	protected $persistence;

	/**
	 * TypoScript templatePath
	 *
	 * @var array $templatePath
	 */
	protected $templatePath;

	/**
	 * Main function for the Scheduler
	 *
	 * @return bool
	 */
	public function execute() {
		$this->initTsfe();

		$this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_szebook.']['settings.'];
		$this->persistence = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_szebook.']['persistence.'];
		$this->templatePath = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_szebook.']['view.'];

		$configuration = array(
			'extensionName' => 'szEbook',
			'pluginName' => 'Pi1',
			'controller' => 'EbookTask',
			'action' => 'convert',
			'switchableControllerActions' => array(
				'EbookTask' => array(
					'1' => 'convert',
				),
			),
			'settings' => $this->settings,
			'persistence' => $this->persistence,
			'view' => $this->templatePath
		);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['tx_szebook_cli']['controller'] = 'szEbook';
		$_GET['tx_szebook_cli']['action'] = 'convert';
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['szEbook']['modules']['Pi1']['controllers'] = array(
			'EbookTask' => array(
				'actions' => array(
					'convert'
				)
			)
		);

		$bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
		$output = $bootstrap->run('', $configuration);

		return true;
	}

	/**
	 * init the TypoScript settings
	 */
	static public function initTsfe() {
		$GLOBALS['TT'] = new t3lib_timeTrackNull;
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 2, 0);
		$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$GLOBALS['TSFE']->sys_page->init(TRUE);
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->rootLine = $GLOBALS['TSFE']->sys_page->getRootLine(1, '');
		$GLOBALS['TSFE']->getConfigArray();
	}

}

?>