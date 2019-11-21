<?php
include "../vendor/autoload.php";
use PhpTreeTagger\TreeTagger;
$treeTaggerPath = __DIR__."/treeTagger";
$wordLemmatizer = "";
$debug = false;
$uniqueWord = false;
$removeAccent = false;
$nbProcess = 6;
$help = false;
foreach($argv as $key => $value)
{
  if(strpos($value, "--help") !== false)
  {
    $help = true;
  }
  elseif(strpos($value, "--debug") !== false)
  {
    $debug = true;
  }
  elseif(strpos($value, "--uniqueWord") !== false)
  {
    $uniqueWord = true;
  }
  elseif(strpos($value, "--removeAccent") !== false)
  {
    $removeAccent = true;
  }
  elseif(strpos($value, "--nbProcess") !== false)
  {
    $nbProcess = (int) str_replace("--nbProcess=", "", $value);
  }
  elseif(strpos($value, "--word") !== false)
  {
    $wordLemmatizer = str_replace("--word=", "", $value);
    if(strpos($wordLemmatizer, "|") !== false)
    {
      $wordLemmatizer = explode("|", $wordLemmatizer);
    }
  }
  elseif($key > 0)
  {
    throw new \Exception("Error : The parameters $value is not defined");
  }
}
try {
  if(!$wordLemmatizer)
  {
    throw new \Exception("Error : You have not to filled in lemmatizer-word");
  }
  $treeTagger = new TreeTagger("french", array(
      "treeTaggerPath"        =>  $treeTaggerPath,
      "debug"                 =>  $debug,
      "uniqueWord"            =>  $uniqueWord,
      "removeAccent"          =>  $removeAccent,
      "nbProcess"             =>  $nbProcess
    )
  );
  // Remove type in words
  $treeTagger->setCleanTypeWords(array(
      "PRO:PER",
      "DET:ART",
      "DET:POS",
      "SENT",
      "PRP"
    )
  );
  var_dump($treeTagger->lemmatizer($wordLemmatizer));
} catch(\Exception $e) {
  echo $e;
}


