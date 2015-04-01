<?php
  require_once "lib/Spyc.php";
  require_once "Translator.php";
  if (!isset($trans))
    $trans = new Translator();

  if (!function_exists("_tr")) {
    function _tr($context, $key, $print = true) {
      global $trans;
      return $trans->translate($context, $key, $print);
    }
  }
?>
