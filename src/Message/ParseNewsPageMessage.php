<?php

namespace App\Message;

final class ParseNewsPageMessage
{
    private $page;

    public function __construct(
        string $page,)
    {
        $this->page = $page;
    }


    public function getPage(): string
    {
        return $this->page;
    }

}
