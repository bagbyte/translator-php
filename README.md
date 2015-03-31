# Translator-PHP
Translator-PHP is a simple PHP Class useful to handle translations in your website with yml files.

#### Requirements
It requires Spyc.php, which is available in the package. For the latest source code please check: https://github.com/mustangostang/spyc 

#### Folder structure
In this guide we will refer to the current folder structure:
 
 ```
project
¦   index.php
¦   Translator.php
¦   Spyc.php 
¦
+---translations
    ¦
    +---LOCALE
        ¦   home.yml
        ¦   menu.yml
        ¦   about.yml
        ¦   service.yml
 ```
 
Where `LOCALE` is a folder with 2 digits rappresenting the locale (i.e.: en, it, es, de, etc..).

### YML File
An example of yml file is the following:
```
# /translations/en/home.yml
title: This is my title
description: This is my description
header:
  sections:
    line1: This is the content of line 1
```

### How to use it
Load the classes:
```
<?php
  require_once('Spyc.php');
  require_once('Translator.php');
?> 
```

Create the `$trans` object:
```
<?php
  $trans = new Translator();
?>
```

Create the `$trans` object and load the contexts:
```
<?php
  $trans = new Translator(array('home', 'menu'));
?>
```

Load the contexts:
```
<?php
  $trans->load(array('about', 'service'));
?>
```

Get the translation for key `title` in `home` context:
```
<?php
  // This will put the translated string into a variable
  $title = $trans->getTranslation('home', 'title');
  $description = $trans->getTranslation('home', 'description');
  $line1 = $trans->getTranslation('home', 'header.sections.line1');
  
  // This will print out the translated string
  $trans->translate('service', 'team.name');
?>
```

### Using the init.php
The init.php file loads the `Translator` class, creates the `$trans` object and define the `_tr($context, $key)` function. The `_tr()` function is an alias of `$trans->getTranslation()` method, which makes the code more easy to read.

### Putting all together
```
<?php
  // load the init.php
  require_once('init.php');
 
  // load the contexts used in the page
  $trans->load(array('home', 'header', 'menu', 'service'));
?>
<!DOCTYPE html>
<html>
  <header>
    <title>
      <?php 
        // get the translation for title key in context home and assign it to $title variable
        $title = $trans->getTranslation('home', 'title');
        
        // print the value of $title
        echo $title;
        
        // or print out directly the translated value
        _tr('home', 'title`);
      ?>
    </title>
  </header>
  ...
```

### API

##### Object creation

```
// Creates the object and set the language
$trans = new Translator();

// Creates the object, set the language and load the file `context.yml` for the proper language
$trans = new Translator(array('context'));
```
