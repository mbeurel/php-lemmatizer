<?php


namespace PhpTreeTagger\Tools;


trait ToolsTrait
{

  /**
   * @var array
   */
  private static $accentsReplacements = array(
    "¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
    "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
    "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
    "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
    "Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
    "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
    "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
    "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
    "à" => "a", "á" => "a", "â" => "a", "ã" => "a",
    "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
    "è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
    "ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
    "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
    "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
    "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
    "ý" => "y", "ÿ" => "y");

  /**
   * @param string $texte
   *
   * @return string
   */
  public function removeAccents(string $texte) : string
  {
    return str_replace('!', '', strtr(trim($texte), self::$accentsReplacements));
  }

  /**
   * get first value of an array
   *
   * @param $array
   * @param null $default
   *
   * @return array|mixed|null
   */
  public static function first($array, $default = null)
  {
    if(!is_array($array))
    {
      return $array;
    }
    if(empty($array))
    {
      return $default;
    }
    $a = array_shift($array);
    unset($array);
    return $a;
  }

  /**
   * @param $data
   *
   * @return array
   */
  private function toArray($data): array
  {
    return \is_array($data) ? $data : [$data];
  }

}