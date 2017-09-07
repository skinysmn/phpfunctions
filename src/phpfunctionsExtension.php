<?php

namespace Bolt\Extension\levin\phpfunctions;

use Bolt\Extension\SimpleExtension;
use Bolt\Extension\levin\phpfunctions\VideoThumb;

class phpfunctionsExtension extends SimpleExtension {

    protected function registerTwigFunctions() {
	return [
	    'filesize' => 'TwigFilesize',
	    'array_rand' => 'TwigArrayrand',
	    'str_replace' => 'TwigStrreplace',
	    'videoinfo' => 'twigVideoinfo',
	    'pdfpre' => 'pdfpre',
	    'imagesize' => 'twigImagesize'
	];
    }

    protected function registerTwigFilters() {
	return [
	    'shuffle' => 'shuffleFilter',
	    'url_decode' => 'urldecodeFunction',
	];
    }

    public function TwigFilesize($file) {

	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
	    $bytes = filesize($_SERVER['DOCUMENT_ROOT'] . $file);
	    $units = array('байт', 'Кбайт', 'Мбайт', 'Гбайт', 'Tb');
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);
	    $bytes /= pow(1024, $pow);
	    $size = round($bytes, '1') . ' ' . $units[$pow];

	    return new \Twig_Markup($size, 'UTF-8');
	}
    }

    public function TwigArrayrand($array, $numitems) {

	$result = array_rand($array, $numitems);

	return $result;
    }

    public function TwigStrreplace($search, $replace, $subject) {
	$subject = htmlentities($subject);
	$result = str_replace($search, $replace, $subject);
	$result = html_entity_decode($result);
	return $result;
    }

    public function shuffleFilter($array) {
	if ($array instanceof Traversable) {
	    $array = iterator_to_array($array, false);
	}
	shuffle($array);
	return $array;
    }

    public function urldecodeFunction($str) {
	$str = urldecode($str);
	return $str;
    }

    public function twigVideoinfo($link) {
	$v = new VideoThumb($link);
	$html = $v->getImage;
	return new \Twig_Markup($html, 'UTF-8');
    }

    public function pdfpre($file = "", $width = 100, $height = 0) {
$app = $this->getContainer();
	$thumb_path = $app['resources']->getPath('webpath') . '/thumbs/pdfpre/' ;
	if (!is_dir($thumb_path)) {
	    mkdir($thumb_path, 0777);
	}

	$path_parts = pathinfo($app['resources']->getPath('filespath'). $file);
	$filename = $path_parts['filename'];
	$filepath = $path_parts['dirname'];

	if (!file_exists($thumb_path . $filename . '.jpg')) {
	    exec('convert "' . $app['resources']->getPath('filespath') . '/' . $file . '[0]" -colorspace RGB -density 300 -quality 95 -background white -alpha remove -geometry ' . $width . ' -border 2x2 -bordercolor "#efefef" ' . $thumb_path . $filename . '.jpg');
	}

	$html = <<< EOM
        <img src="%src%" alt="%alt%"> 
EOM;
	$html = str_replace("%src%", '/thumbs/pdfpre/' . $filename . '.jpg', $html);
	$html = str_replace("%alt%", $filename, $html); 
	return new \Twig_Markup($html, 'UTF-8');
    }
	   public function twigImagesize($img)
	{
		if (trim($img) <> ''){
			$width = getimagesize($_SERVER['DOCUMENT_ROOT']."/files/".$img);
			if ($width[0] > 250 ){
				$html .= '<a class="newsimg showpopup" href="/files/'.$img.'"><img src="/files/'.$img.'" alt=""/></a>';
			}else{
				$html .= '<a class="newsimg"><img src="/files/'.$img.'" alt=""/></a>';
			}
		}else{
			$html = " ";
		}
			return new \Twig_Markup($html, 'UTF-8');

	}

}
