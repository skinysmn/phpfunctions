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
	    'imagesize' => 'twigImagesize',
	    'calendar' => 'calendar',
	    'server'=> 'TwigServerarray'
	];
    }

    protected function registerTwigFilters() {
	return [
	    'shuffle' => 'shuffleFilter',
	    'url_decode' => 'urldecodeFunction',
	];
    }
public function TwigServerarray($param) {

	$server = $_SERVER[$param];
	    return new \Twig_Markup($server, 'UTF-8');

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
	$thumb_path = $app['resources']->getPath('webpath') . '/thumbs/pdfpre/';
	if (!is_dir($thumb_path)) {
	    mkdir($thumb_path, 0777);
	}

	$path_parts = pathinfo($app['resources']->getPath('filespath') . $file);
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

    public function twigImagesize($img) {
	if (trim($img) <> '') {
	    $width = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/files/" . $img);
	    if ($width[0] > 250) {
		$html .= '<a class="newsimg showpopup" href="/files/' . $img . '"><img src="/files/' . $img . '" alt=""/></a>';
	    } else {
		$html .= '<a class="newsimg"><img src="/files/' . $img . '" alt=""/></a>';
	    }
	} else {
	    $html = " ";
	}
	return new \Twig_Markup($html, 'UTF-8');
    }

    public function calendar($month, $year, $day) {

	function draw_calendar($month, $year,$day, $action = 'none') {
	    $calendar = '<table cellpadding="0" cellspacing="0" class="b-calendar__tb">';

	    // вывод дней недели
	    $headings = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
	    $calendar.= '<tr class="b-calendar__row">';
	    for ($head_day = 0; $head_day <= 6; $head_day++) {
		$calendar.= '<th class="b-calendar__head';
		// выделяем выходные дни
		if ($head_day != 0) {
		    if (($head_day % 5 == 0) || ($head_day % 6 == 0)) {
			$calendar .= ' b-calendar__weekend';
		    }
		}
		$calendar .= '">';
		$calendar.= '<div class="b-calendar__number">' . $headings[$head_day] . '</div>';
		$calendar.= '</th>';
	    }
	    $calendar.= '</tr>';

	    // выставляем начало недели на понедельник
	    $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
	    $running_day = $running_day - 1;
	    if ($running_day == -1) {
		$running_day = 6;
	    }

	    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
	    $day_counter = 0;
	    $days_in_this_week = 1;
	    $dates_array = array();

	    // первая строка календаря
	    $calendar.= '<tr class="b-calendar__row">';

	    // вывод пустых ячеек
	    for ($x = 0; $x < $running_day; $x++) {
		$calendar.= '<td class="b-calendar__np"></td>';
		$days_in_this_week++;
	    }

	    // дошли до чисел, будем их писать в первую строку
	    for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
		$calendar.= '<td class="b-calendar__day';

		// выделяем выходные дни
		if ($running_day != 0) {
		    if (($running_day % 5 == 0) || ($running_day % 6 == 0)) {
			$calendar .= ' b-calendar__weekend';
		    }
		}
		$calendar .= '">';

		// пишем номер в ячейку
		if ($list_day <> $day){
		$calendar.= '<div class="b-calendar__number">' . $list_day . '</div>';
		}else{
		 $calendar.= '<div class="b-calendar__number" style="background: lightgreen">' . $list_day . '</div>';   
		}
		$calendar.= '</td>';

		// дошли до последнего дня недели
		if ($running_day == 6) {
		    // закрываем строку
		    $calendar.= '</tr>';
		    // если день не последний в месяце, начинаем следующую строку
		    if (($day_counter + 1) != $days_in_month) {
			$calendar.= '<tr class="b-calendar__row">';
		    }
		    // сбрасываем счетчики 
		    $running_day = -1;
		    $days_in_this_week = 0;
		}

		$days_in_this_week++;
		$running_day++;
		$day_counter++;
	    }

	    // выводим пустые ячейки в конце последней недели
	    if ($days_in_this_week < 8) {
		for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
		    $calendar.= '<td class="b-calendar__np"> </td>';
		}
	    }
	    $calendar.= '</tr>';
	    $calendar.= '</table>';
return $calendar;
	}
	$html = draw_calendar($month, $year, $day);
return new \Twig_Markup($html, 'UTF-8');
    }

}
