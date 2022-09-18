<?php

namespace App\Message;

use Symfony\Component\DomCrawler\Crawler;

final class ParseNewsMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     private string $node;

     public function __construct(string $node)
     {
         $this->node = $node;
     }

    public function getNode(): string
    {
        return $this->node;
    }
}
