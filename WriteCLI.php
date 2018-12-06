<?php

class WriteCLI {

  /**
   * [_write description]
   * принимаем строку которую выводим в консоль с расцветкой
   * последовательность #цвет:цвет_фона:стиль$ заменяется на цветовые коды
   * #цвет$
   * #:цвет_фона$
   * #::стиль$
   * #:цвет_фона:стиль$
   * #цвет:цвет_фона$
   * #цвет::стиль$
   * #цвет:цвет_фона:стиль$
   * @param  [type] $msg [строка для вывода]
   * @param  [type] $br  [перенос строки]
   * @return [type]      [description]
   */
  public function write(
    $msg = '',
    $br = PHP_EOL
  ) {
    $esc = "\033[";
    preg_match_all("~(#[\w,\w:\w]+)~", $msg, $out/*, PREG_SET_ORDER*/);
    // preg_match_all("~(#[\w,\w:\w]+\\$)~", $msg, $out/*, PREG_SET_ORDER*/);

    $colors = [];
    foreach ($out[1] as $c) {
      $c = self::normalizeColors($c);
      $o = $c;

      $c = trim($c, '#');

      $c = explode(':', $c);

      $colors[$o] = $esc;
      $color      = $bgcolor      = $style      = '';
      $color      = isset(self::$fcolor[$c[0]]) ? self::$fcolor[$c[0]] : '';
      if (isset($c[1])) {
        $bgcolor = isset(self::$fbgcolor[$c[1]]) ? self::$fbgcolor[$c[1]] : '';
      }
      if (isset($c[2])) {
        $style = isset(self::$fstyle[$c[2]]) ? self::$fstyle[$c[2]] : '';
      }
      $c = [$bgcolor, $color, $style];
      foreach ($c as $k => $v) {
        if (empty($v)) {
          unset($c[$k]);
        }
      }
      $c = implode(';', $c);
      $colors[$o] .= $c.'m';

    }

    $txt = strtr($msg, $colors).$esc.'0m'.$br;
    echo $txt;
  }

  protected static function normalizeColors($color = false)
  {
    // $array_colors = array_keys(self::$fcolor);
    $color = ltrim($color, '#');
    $color = explode(':', $color);
    // $end = end($color);
    $end = array_pop($color);
    // $end =ltrim($end, '#');
    $cf = array_key_exists($end, self::$fcolor);
    $sf = array_key_exists($end, self::$fstyle);

    if ($cf || $sf) {
      // есть такой цвет или стиль
      // собираем строку для замены и возвращаем
      array_push($color, $end);

      return '#'.implode(':', $color);
    } else {
      // совпадений не найденно
      // надо определить
      foreach (self::$fcolor as $key => $value) {
        if (substr($end, 0, strlen($key)) == $key) {
          // нашли совападение
          // надо вернуть строку
          array_push($color, $key);

          return '#'.implode(':', $color);
        }
      }
      foreach (self::$fstyle as $key => $value) {
        if (substr($end, 0, strlen($key)) == $key) {
          // нашли совападение
          // надо вернуть строку
          array_push($color, $key);

          return '#'.implode(':', $color);
        }
      }
    }

    return '#'.implode(':', $color);
  }

  protected static $fcolor = [
    'gray'    => 30,
    'black'   => 30,
    'red'     => 31,
    'green'   => 32,
    'yellow'  => 33,
    'blue'    => 34,
    'magenta' => 35,
    'cyan'    => 36,
    'white'   => 37,
    'default' => 39,
  ];
  protected static $fbgcolor = [
    'gray'    => 40,
    'black'   => 40,
    'red'     => 41,
    'green'   => 42,
    'yellow'  => 43,
    'blue'    => 44,
    'magenta' => 45,
    'cyan'    => 46,
    'white'   => 47,
    'default' => 49,
  ];
  protected static $fstyle = [
    'default'          => '0',
    'bold'             => 1,
    'faint'            => 2,
    'normal'           => 22,
    'italic'           => 3,
    'notitalic'        => 23,
    'underlined'       => 4,
    'doubleunderlined' => 21,
    'notunderlined'    => 24,
    'blink'            => 5,
    'blinkfast'        => 6,
    'noblink'          => 25,
    'negative'         => 7,
    'positive'         => 27,
  ];
}