<?php

namespace lib\contracts;

abstract class aPartial implements iPartial
{
    /** @var array List of content pages/tabs */
    protected array $contentPages = [];

    public function __construct(array $contentPages = [])
    {
        $this->contentPages = $contentPages;
    }

    /**
     * @return array
     */
    public function getContentPages(): array
    {
        return $this->contentPages;
    }

    /**
     * @param array $contentPages
     */
    public function setContentPages(array $contentPages): void
    {
        $this->contentPages = $contentPages;
    }
}