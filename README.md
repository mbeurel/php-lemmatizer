# php-lemmatizer

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)
[![Latest Stable Version](https://img.shields.io/packagist/v/mbeurel/php-lemmatizer.svg)](https://packagist.org/packages/mbeurel/php-lemmatizer)
[![Total Downloads](https://poser.pugx.org/mbeurel/php-lemmatizer/downloads.svg)](https://packagist.org/packages/mbeurel/php-lemmatizer)
[![License](https://poser.pugx.org/mbeurel/php-lemmatizer/license.svg)](https://packagist.org/packages/mbeurel/php-lemmatizer)

A simple lemmatizer tool based on [TreeTagger](https://www.cis.uni-muenchen.de/~schmid/tools/TreeTagger/) for PHP.

## Installation TreeTagger library

View TreeTagger [WebSite](https://www.cis.uni-muenchen.de/~schmid/tools/TreeTagger/)

## Install php-lemmatizer

You can install it with Composer:

```
composer require mbeurel/php-lemmatizer
```

## Examples

Example scripts are available ina separate repository [php-lemmatizer/example](https://github.com/mbeurel/php-lemmatizer/tree/master/exemple).

## Sample Code
```php
include "vendor/autoload.php";
use PhpTreeTagger\TreeTagger;
$treeTaggerPath = __DIR__."/treeTagger"; // Library TreeTagger path

try {

  // Init library
  $treeTagger = new TreeTagger("french", array(
      "treeTaggerPath"        =>  $treeTaggerPath,      // Path to TreeTagger Library
      "debug"                 =>  false,                // View Debug
      "wordUnique"            =>  true,                 // Keep only one occurrence of the word
      "wordRemoveAccent"      =>  true,                 // Remove all accent in word
      "nbProcess"             =>  $nbProcess            // Number of processes executed at the same time
    )
  );
  
  // Remove type in words
  $treeTagger->setCleanTypeWords(
    array(
      "PRO:PER",
      "DET:ART",
      "DET:POS",
      "SENT",
      "PRP"
    )
  );
  
  // Lemmatizer String or Array parameters, to array => ["La lemmatisation désigne un traitement lexical", "apporté à un texte en vue de son analyse"]
  $result = $treeTagger->lemmatizer("La lemmatisation désigne un traitement lexical apporté à un texte en vue de son analyse.");
  
  // View result : 
  var_dump($result);
  
  //  $result = array(
  //    0  =>  array(
  //      "value"     =>  "lemmatisation designer traitement lexical apporter texte vue analyse",
  //      "detail"    =>  array(
  //        1           =>  array(
  //          "source"    =>  "lemmatisation",
  //          "type"      =>  "NOM",
  //          "dest"      =>  "lemmatisation"
  //        ),
  //        2           =>  array(
  //          "source"    =>  "désigne",
  //          "type"      =>  "VER:pres",
  //          "dest"      =>  "désigner"
  //        ),
  //        4           =>  array(
  //          "source"    =>  "traitement",
  //          "type"      =>  "NOM",
  //          "dest"      =>  "traitement"
  //        ),
  //        6           =>  array(
  //          "source"    =>  "apporté",
  //          "type"      =>  "VER:pper",
  //          "dest"      =>  "apporter"
  //        ),
  //        7           =>  array(
  //          "source"    =>  "à",
  //          "type"      =>  "PRP",
  //          "dest"      =>  "à"
  //        ),
  //        9           =>  array(
  //          "source"    =>  "texte",
  //          "type"      =>  "NOM",
  //          "dest"      =>  "texte"
  //        ),
  //        10          =>  array(
  //          "source"    =>  "en",
  //          "type"      =>  "PRP",
  //          "dest"      =>  "en"
  //        ),
  //        11          =>  array(
  //          "source"    =>  "vue",
  //          "type"      =>  "NOM",
  //          "dest"      =>  "vue"
  //        ),
  //        12          =>  array(
  //          "source"    =>  "de",
  //          "type"      =>  "PRP",
  //          "dest"      =>  "de"
  //        ),
  //        13          =>  array(
  //          "source"    =>  "son",
  //          "type"      =>  "DET:POS",
  //          "dest"      =>  "son"
  //        ),
  //        14          =>  array(
  //          "source"    =>  "analyse",
  //          "type"      =>  "NOM",
  //          "dest"      =>  "analyse"
  //        ),
  //        15          =>  array(
  //          "source"    =>  ".",
  //          "type"      =>  "SENT",
  //          "dest"      =>  "."
  //        )
  //      }
  //    }
  //  }
} catch(\Exception $e) {
  echo $e;
}
```

## Credits

Created by [Matthieu Beurel](https://www.mbeurel.com). Sponsored by [Yipikai](https://yipikai.studio).