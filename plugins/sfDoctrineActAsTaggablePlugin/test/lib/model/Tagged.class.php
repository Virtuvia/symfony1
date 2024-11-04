<?php

declare(strict_types=1);

final class Tagged extends sfDoctrineRecord
{
    public function setTableDefinition(): void
    {
    }

    public function setUp(): void
    {
        $this->actAs(new Taggable());
    }
}
