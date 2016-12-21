<?php
namespace Sunzinet\SzEbook\Command;

use Sunzinet\SzEbook\Domain\Repository\EbookRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Description of the phpfile 'Convert.php'
 *
 * @author Dennis Römmich <dennis@roemmich.eu>
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class ConvertCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

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
	 * Konvertiert PDF#s zu Turnjs
	 *
	 * @return bool
	 */
	public function convertCommand() {
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump('test');
		die();
		$fluid = $this->setTemplate('EXT:sz_ebook/Resources/Private/Templates/');
		/** @var $pdf Tx_SzEbook_Domain_Model_Ebook */
		$pdf = $this->ebookRepository->findTask();

		if($pdf) {
			$fileinfo = $this->getFileinfo($pdf->getPdf());

			$uploadPath = PATH_site . 'uploads/tx_szebook';
			$file = $uploadPath.'/'.$pdf->getPdf();
			$path = PATH_site.'uploads/tx_szebook/'.$fileinfo['filename'];

			if(!file_exists($path)) {

				mkdir($path, 0775, true);
				mkdir($path . '/pages', 0775, true);
				chmod($path . '/index.html', 0775);
				shell_exec('cp -P -R ' . $fluid->getLayoutRootPath() . '/extras ' . $path . '/extras/');

				$img = new \Imagick($file);

				$imagegeometry = $img->getimagegeometry();
				$output = $fluid->assignMultiple(array('header' => $pdf->getHeader(), 'pages' => $img->getnumberimages(), 'width' => ($imagegeometry['width']*2), 'height' => $imagegeometry['height']));
				file_put_contents($path . '/index.html', $output->render());

				$this->saveImg($file, $path, $img->getnumberimages());

				$pdf->setTurnjs(1);
				return true;
			} else {

				$pdf->setTurnjs(1);
				return false;
			}
		} else {

			return true;
		}
	}

	/**
	 * Liefert die Informationen über die soeben gespeicherte Datei
	 *
	 * @param string $file Name der Datei
	 * @return mixed
	 */
	protected function getFileinfo($file) {
		$path = PATH_site . 'uploads/tx_szebook';
		$file = $path.'/'.$file;
		$fileinfo = pathinfo($file);

		return $fileinfo;
	}

	/**
	 * Setzt den Pfad zum Turnjs Template
	 *
	 * @param $path
	 * @return StandaloneView
	 */
	protected function setTemplate($path) {
		/** @var $fluid StandaloneView */
		$fluid = GeneralUtility::makeInstance(StandaloneView::class);
		$templateRootPath = GeneralUtility::getFileAbsFileName($path);
		$fluid->setTemplatePathAndFilename($templateRootPath.'turnjs/index.html');

		return $fluid;
	}

	/**
	 * @param $file
	 * @param $path
	 * @param int $i
	 * @return bool
	 */
	protected function saveImg($file, $path, $i = 1) {

		// for each page in the pdf
		for($it = 0; $it < $i; $it++) {
			$tempImage = PATH_site . 'typo3temp/pics/tx_szebook-' . ($it+1) . '.jpg';
			$tempImageThumb = PATH_site . 'typo3temp/pics/tx_szebook-' . ($it+1) . '-thumb.jpg';
			$tempImageLarge = PATH_site . 'typo3temp/pics/tx_szebook-' . ($it+1) . '-large.jpg';

			// convert pdf to image (3 times: normal, thumb and large)
			// resize this image (divided by 4 for normal and thumb image and 2 for large image)
			// move image to destination path
			exec('convert -density 400 -colorspace RGB ' . $file.'['.$it.'] ' . $tempImage);

			$currentImageSize = getimagesize($tempImage);
			$resizedImage = new \Imagick($tempImage);
			$resizedImage->resizeImage($currentImageSize[0]/4,$currentImageSize[1]/4, \Imagick::FILTER_LANCZOS,1);
			$resizedImage->writeImage($tempImage);

			rename($tempImage, $path . '/pages/' . ($it+1) . '.jpg');

			exec('convert -density 400 -colorspace RGB ' . $file.'['.$it.'] ' . $tempImageThumb);

			$currentImageThumbSize = getimagesize($tempImageThumb);
			$resizedImageThumb = new \Imagick($tempImageThumb);
			$resizedImageThumb->resizeImage($currentImageThumbSize[0]/4,$currentImageThumbSize[1]/4, \Imagick::FILTER_LANCZOS,1);
			$resizedImageThumb->writeImage($tempImageThumb);

			rename($tempImageThumb, $path . '/pages/' . ($it+1) . '-thumb.jpg');

			exec('convert -density 400 -colorspace RGB ' . $file.'['.$it.'] ' . $tempImageLarge);

			$currentImageLargeSize = getimagesize($tempImageLarge);
			$resizedImageLarge = new \Imagick($tempImageLarge);
			$resizedImageLarge->resizeImage($currentImageLargeSize[0]/2,$currentImageLargeSize[1]/2, \Imagick::FILTER_LANCZOS,1);
			$resizedImageLarge->writeImage($tempImageLarge);

			rename($tempImageLarge, $path . '/pages/' . ($it+1) . '-large.jpg');

			file_put_contents($path . '/pages/' . ($it+1) . '-regions.json', '[]');
		}
	}
}
