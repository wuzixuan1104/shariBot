<?php

class HTMLMin {
  public static function minify ($html) {
    return trim (preg_replace (array ('/\>[^\S ]+/su', '/[^\S ]+\</su', '/(\s)+/su'), array ('>', '<', '\\1'), $html));
  }
}