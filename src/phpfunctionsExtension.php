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
	    $units = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);
	    $bytes /= pow(1024, $pow);
	    $size = round($bytes, '2') . ' ' . $units[$pow];

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

}
