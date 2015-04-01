<?php
/**
 * Author: Sabino Papagna
 * Date: 30/03/2015
 * 
 * Requires: Spyc.php (https://github.com/mustangostang/spyc)
 *
 */
class Translator {
  const KEY_DELIMITER               = '.';
  const DEFAULT_LANGUAGE            = 'en';
  const LOCALE_PATH                 = './translations';
  const FOLDER_SEPARATOR            = '/';
  const TRANSLATION_FILE_EXTENSION  = '.yml';
  const SESSION_LANGUAGE_PREFERENCE = 'translator_language_preference';
  const LOCALE_GET_PARAMETER        = 'locale';
  
  private $language;
  private $translations = array();
  private $contexts = array();
  private $supportedLanguages = array('en','it');
  
  public function __construct($contexts = null) {
    if (!isset($_SESSION)) { 
      session_start(); 
    }

    if (isset($_GET[self::LOCALE_GET_PARAMETER]) && !empty($_GET[self::LOCALE_GET_PARAMETER])) {
      $this->language = $_GET[self::LOCALE_GET_PARAMETER];
    }
    elseif (isset($_SESSION[self::SESSION_LANGUAGE_PREFERENCE])) {
      $this->language = $_SESSION[self::SESSION_LANGUAGE_PREFERENCE];
    }
    elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $this->language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }
    else {
      $this->language = self::DEFAULT_LANGUAGE;
    }
    
    $_SESSION[self::SESSION_LANGUAGE_PREFERENCE] = $this->language;
    
    if ($contexts != null) {
      $this->contexts = $contexts;
      $this->load($contexts);
    }
  }

  public function load($contexts = array()) {
    if (!is_array($contexts))
      return;
    
    if (!is_array($this->translations))
      $this->translations = array();
    
    foreach($contexts as $ctx) {
      if (in_array("", $this->contexts))
        continue;
      
      $this->contexts[] = $ctx;
      $localeFile = $this->getFilePath($this->language, $ctx);
      $defaultLocaleFile = $this->getFilePath(self::DEFAULT_LANGUAGE, $ctx);
      
      if (file_exists($localeFile))
        $this->translations[$ctx] = Spyc::YAMLLoad($localeFile);
      elseif (file_exists($defaultLocaleFile))
        $this->translations[$ctx] = Spyc::YAMLLoad($defaultLocaleFile);
    }
  }
  
  private function translation($context, $key, $print = true) {
    if ((strlen($context) < 1) || (strlen($key) < 1) || (count($this->translations) < 1)) {
      if ($print) {
        echo $key;
        return;
      }
      else
        return $key;
    }
      
    if (!in_array($context, $this->contexts) || !array_key_exists($context, $this->translations)) {
      if ($print) {
        echo $key;
        return;
      }
      else
        return $key;
    }
    
    $output = $this->getValueFromKey($this->translations[$context], $key);
    
    if ($print)
      echo (($output == null) ? $key : $output);
    else
      return (($output == null) ? $key : $output);
  }
  
  public function getTranslation($context, $key) {
    return $this->translation($context, $key, false);
  }
  
  public function translate($context, $key) {
    return $this->translation($context, $key, true);
  }
  
  public function getCurrentLanguage() {
    return $_SESSION[self::SESSION_LANGUAGE_PREFERENCE];
  }
  
  public function getAvailableLanguages() {
    return $this->supportedLanguages;
  }
  
  private function getFilePath($locale, $context) {
    return self::LOCALE_PATH . self::FOLDER_SEPARATOR . $locale . self::FOLDER_SEPARATOR . $context . self::TRANSLATION_FILE_EXTENSION;
  }
  
  private function getValueFromKey($array, $key) {
    if (strlen($key) < 1)
      return null;
    
    $keys = explode(self::KEY_DELIMITER, $key);
    if (count($keys) == 1) {
      if (array_key_exists($key, $array))
        return $array[$key];
      
      return null;
    }
    else {
      $currentKey = $keys[0];
      if (!array_key_exists($currentKey, $array))
        return null;

      array_shift($keys);
      return $this->getValueFromKey($array[$currentKey], implode(self::KEY_DELIMITER, $keys));
    }
  }
}
?>
