<?php namespace Sergeylukin;

use stdClass as Stub;
use DOMDocument;
use DOMXPath;
use Requests_Session as Http;

class AmazonProductSearch {

  // Define reusable xPath query for selecting first element out
  // of search results page
  const SEARCH_RESULTS_FIRST_ELEMENT_XPATH =
    "//*[@id='atfResults']//li[1 and not(contains(@class, 'acs-carousel-header'))]";

  public function __construct(Http $Http)
  {
    $this->Http = $Http;
  }

  /*
   * Factory pattern
   * 
   * Easy way to avoid Dependency management and get ready-to-use
   * instance of our class
   *
   * If, however, you need to mock the dependencies (Tests?), instantiate
   * via new AmazonProductSearch($Http) where $Http is proper dependency
   * (refer to the constructor for more details)
   *
   */
  public static function factory()
  {
    return new self(new Http());
  }

  public function findOneProductByKeyword($keywords = '')
  {
    $this->load("http://amazon.com/s/?field-keywords={$keywords}");
    $title = $this->getFirstSearchResultTitle();
    $price = $this->getFirstSearchResultPrice();
    $image_uri = $this->getFirstSearchResultImageURI();

    $product_uri = $this->getFirstSearchResultProductURI();
    if ($product_uri) {
      $this->load($product_uri);
      $description = $this->getProductDescription();
    }

    $Product = new Stub();
    $Product->title = $title;
    $Product->price = $price;
    $Product->image_uri = $image_uri;
    $Product->description = ($description ? $description : '');

    return $Product;
  }

  /*
   * Loads remote URI and instantiates XPATH object in $this->xpath
   *
   */
  private function load($uri = '')
  {
    $result = $this->Http->get($uri)->body;
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    @$dom->loadHTML($result);
    $this->xpath = new DOMXPath($dom);
  }

  private function getFirstSearchResultTitle()
  {
    $query = self::SEARCH_RESULTS_FIRST_ELEMENT_XPATH
      . "//*[contains(@class, 's-access-detail-page')]//h2";
    $title_element = $this->xpath->query($query)->item(0);
    if (!$title_element) {
      return '';
    }
    $title = $title_element->nodeValue;
    return $title;
  }

  private function getFirstSearchResultPrice()
  {
    $query = self::SEARCH_RESULTS_FIRST_ELEMENT_XPATH
      . "//*[contains(@class, 's-price')]";
    $price_element = $this->xpath->query($query)->item(0);
    if (!$price_element) {
      return '';
    }
    $price = $price_element->nodeValue;
    return $price;
  }

  private function getFirstSearchResultImageURI()
  {
    $query = self::SEARCH_RESULTS_FIRST_ELEMENT_XPATH
      . "//*[contains(@class, 's-access-image')]";
    $image_element = $this->xpath->query($query)->item(0);
    if (!$image_element) {
      return '';
    }
    $image_uri = $image_element->getAttribute('src');
    return $image_uri;
  }

  private function getFirstSearchResultProductURI()
  {
    $query = self::SEARCH_RESULTS_FIRST_ELEMENT_XPATH
      . "//*[contains(@class, 's-access-detail-page')]";
    $product_link_element = $this->xpath->query($query)->item(0);
    if (!$product_link_element) {
      return '';
    }
    $product_uri = $product_link_element->getAttribute('href');
    return $product_uri;
  }

  private function getProductDescription()
  {
    $query = "//*[@id='productDescription' or @id='mas-product-description']";
    $product_description_element = $this->xpath->query($query)->item(0);

    return $this->DOMNodeToHTML($product_description_element);
  }

  private function DOMNodeToHTML($node, $xpath = null) {
    $dom = new \DOMDocument();

    if (!$node) {
      return '';
    }

    $nodeCopy = $dom->importNode($node, true);
    $html = $dom->saveXML($nodeCopy);

    $config = \HTMLPurifier_Config::createDefault();
    $config->set('AutoFormat.AutoParagraph', true);
    $config->set('HTML.TidyLevel', 'heavy');
    $config->set('HTML.AllowedAttributes', '');
    $config->set('HTML.AllowedElements', array('p', 'strong', 'u', 'i', 'b'));
    $config->set('AutoFormat.RemoveEmpty', true);
    $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
    $config->set('Output.TidyFormat', true);
    $config->set('Core.NormalizeNewlines', true);
    $purifier = new \HTMLPurifier($config);
    $html = $purifier->purify($html);

    $html = preg_replace( "/\r|\n/", "", $html);

    return $html;
  }

}
