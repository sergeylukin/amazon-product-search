<?php

use Sergeylukin\AmazonProductSearch as Amazon;
use Mockery as m;

class AmazonProductSearchTest extends \Codeception\TestCase\Test
{

  public function testProductDetailsAreExtractedCorrectly()
  {
    $search_results_fixture = new stdClass();
    $search_results_fixture->body = '
      <div id="atfResults">
        <ul>
          <li>
          <fig>
            <article class="acs-carousel-header"></article>
            </fig>
          </li>
          <li>
            <img class="s-access-image" src="/latte-cup.jpg" />
            <a href="/latte-cup" class="s-access-detail-page">
              <h2>Best latte cup</h2>
            </a>
            <span class="s-price">$12.05</span>
          </li>
        </ul>
      </div>
    ';

    $product_page_fixture = new stdClass();
    $product_page_fixture->body = "
      <div id=\"productDescription\">
        <p>
            <div><img src=\"/some/image.jpg\" /></div>
            <p style=\"margin: 1em\">\t \n </p>
            <p>The best latte cup you will ever drink latte from. Period.</p>
        </p>
      </div>
    ";

    $Http = m::mock('Requests_Session');
    // Mock HTTP request to product page
    $Http->shouldReceive('get')->with('/latte-cup')->once()->andReturn($product_page_fixture);
    // Mock HTTP request to search results
    $Http->shouldReceive('get')->once()->andReturn($search_results_fixture);

    $Amazon = new Amazon($Http);

    $Product = $Amazon->findOneProductByKeyword('latte cup');

    assertThat($Product->title, is('Best latte cup'));
    assertThat($Product->price, is('$12.05'));
    assertThat($Product->image_uri, is('/latte-cup.jpg'));
    assertThat($Product->description, is('<p>The best latte cup you will ever drink latte from. Period.</p>'));
  }

}
