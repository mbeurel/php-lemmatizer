<?php


namespace PhpTreeTagger;


use PhpTreeTagger\Tools\ToolsTrait;
use Symfony\Component\Process\Process;
class TreeTagger
{

  use ToolsTrait;

  /**
   * @var string
   */
  protected $treeTaggerBinPath;

  /**
   * @var string
   */
  protected $tokenizePerlCmdPath;

  /**
   * @var string
   */
  protected $abbreviationsPath;

  /**
   * @var string
   */
  protected $parFilePath;

  /**
   * @var string
   */
  protected $language;

  /**
   * @var bool
   */
  protected $debug = false;

  /**
   * @var bool|mixed
   */
  protected $uniqueWord;

  /**
   * @var bool|mixed
   */
  protected $removeAccent;

  /**
   * @var bool|mixed
   */
  protected $nbProcess;

  /**
   * @var array
   */
  protected $cleanTypeWords = array();

  /**
   * TreeTagger constructor.
   *
   * @param string $language
   * @param string $pathLib
   *
   * @throws \Exception
   */
  public function __construct(string $language, array $config = array())
  {
    $this->language = $language;

    $this->debug = (isset($config["debug"]) && $config["debug"]) ? $config["debug"] : false;
    $this->uniqueWord = (isset($config["uniqueWord"]) && $config["uniqueWord"]) ? $config["uniqueWord"] : false;
    $this->removeAccent = (isset($config["removeAccent"]) && $config["removeAccent"]) ? $config["removeAccent"] : false;
    $this->nbProcess = (isset($config["nbProcess"]) && $config["nbProcess"]) ? ($config["nbProcess"] > 0 ? $config["nbProcess"] : 1) : 4;

    $treeTaggerPath = (isset($config["treeTaggerPath"]) && $config["treeTaggerPath"]) ? $config["treeTaggerPath"] : "";
    if(!$treeTaggerPath)
    {
      throw new \Exception(sprintf("The path to the TreeTagger library is not defind"));
    }
    $this->treeTaggerBinPath = sprintf("%s/bin/tree-tagger", $treeTaggerPath);
    $this->tokenizePerlCmdPath = sprintf("%s/cmd/utf8-tokenize.perl", $treeTaggerPath);
    $this->abbreviationsPath = sprintf("%s/lib/%s-abbreviations", $treeTaggerPath, $this->language);
    $this->parFilePath = sprintf("%s/lib/%s.par", $treeTaggerPath, $this->language);
    $this->verifyLibrary();
  }

  /**
   * @throws \Exception
   */
  protected function verifyLibrary()
  {
    if(!file_exists($this->treeTaggerBinPath))
    {
      throw new \Exception(sprintf("The file \"tree-tagger\" not exist in %s", $this->treeTaggerBinPath));
    }
    if(!file_exists($this->tokenizePerlCmdPath))
    {
      throw new \Exception(sprintf("The file \"utf8-tokenize.perl\" not exist in %s", $this->treeTaggerBinPath));
    }
    if(!file_exists($this->abbreviationsPath))
    {
      throw new \Exception(sprintf("The file \"%s-abbreviations\" not exist in %s", $this->language, $this->treeTaggerBinPath));
    }
    if(!file_exists($this->parFilePath))
    {
      throw new \Exception(sprintf("The file \"%s.par\" not exist in %s", $this->language, $this->treeTaggerBinPath));
    }
  }

  /**
   * @param array $cleanTypeWords
   *
   * @return TreeTagger
   */
  public function setCleanTypeWords(array $cleanTypeWords) : self
  {
    $this->cleanTypeWords = $cleanTypeWords;
    return $this;
  }

  /**
   * @param array $cleanTypeWords
   *
   * @return array
   */
  public function getCleanTypeWords() : array
  {
    return $this->cleanTypeWords;
  }

  /**
   * @param string $value
   *
   * @return string
   */
  protected function buildCommand(string $value)
  {
    return sprintf("echo \"%s\" | %s -f -a %s | %s -token -lemma -sgml %s",
      $value,
      $this->tokenizePerlCmdPath,
      $this->abbreviationsPath,
      $this->treeTaggerBinPath,
      $this->parFilePath
    );
  }

  /**
   * @param array|string $data
   *
   * @return array
   * @throws \Exception
   */
  public function lemmatizer($data)
  {
    $data = $this->toArray($data);
    return $this->executeProcess($data);
  }

  protected function executeProcess($data)
  {
    $finaleArray = array();
    $processRun = array();
    $processRunIds = array();
    for($i = 1; $i <= $this->nbProcess; $i++)
    {
      $processRunIds[] = $i;
    }

    $errors = null;
    $stopWhile = false;
    $countSuccessElements = 0;
    $nbElements = count($data);
    do {
      if(count($processRun) < $this->nbProcess && count($data) > 0)
      {
        $numProcess = (int) $this->first($processRunIds);
        $keyArray = array_key_first($data);
        $value = $data[$keyArray];
        unset($data[$keyArray]);
        $commandeTexte = $this->buildCommand($value);
        $process = array(
          "key"               =>  $keyArray,
          "value"             =>  $value,
          "commande"          =>  new Process($commandeTexte),
          "commandeTexte"     =>  $commandeTexte,
          "numProcess"        =>  $numProcess
        );
        $process['commande']->start();
        array_shift($processRunIds);
        $processRun[$numProcess] = $process;
      }

      foreach($processRun as $numProcess => $process)
      {
        if($process['commande']->isTerminated())
        {
          if($process['commande']->isSuccessful())
          {
            $outputString = $process['commande']->getOutput();
            $output = explode("\n", $outputString);
            if($output)
            {
              if($this->debug)
              {
                $countSuccessElements++;
                dump($countSuccessElements." Elements / ".$nbElements);
              }
              $finaleArray[$process['key']] = $this->handlingOutput($output);
              unset($processRun[$numProcess]);
              $processRunIds[] = $numProcess;
            }
            else
            {
              $stopWhile = true;
              $errors = "Not output for this commande : %s".$process["commandeTexte"];
            }
          }
          else
          {
            $stopWhile = true;
            $errors = $process['commande']->getOutput();
          }
          unset($data[$process['key']]);
          if(count($data) <= 0 && count($processRun) <= 0)
          {
            $stopWhile = true;
          }
        }
      }
    } while(!$stopWhile);
    if($errors)
    {
      throw new \Exception($errors);
    }
    return $finaleArray;
  }

  /**
   * @param array $output
   *
   * @return array
   */
  protected function handlingOutput(array $output)
  {
    $finalArray = array();
    $detailArray = array();
    if($this->debug)
    {
      dump($output);
    }
    foreach($output as $key => $elements)
    {
      if($elements)
      {
        list($source, $type, $dest) = explode("\t", $elements);
        if(!in_array($type, $this->getCleanTypeWords()))
        {
          $usedSource = false;
          if($dest == "<unknown>")
          {
            $usedSource = true;
          }
          $value = $usedSource ? strtolower($source) : $dest;
          $value = $this->removeAccent ? $this->removeAccents($value) : $value;
          $finalArray[$this->uniqueWord ? $value : $key] = $value;
          $detailArray[$key] = array(
            "source"    =>  $source,
            "type"      =>  $type,
            "dest"      =>  $dest
          );
        }
      }
    }
    return array(
      "value"   =>  implode(" ", $finalArray),
      "detail"  =>  $detailArray,
    );
  }

}