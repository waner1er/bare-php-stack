<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function test_hydrate(): void
    {
        $cat = new Category([
            'id' => 1,
            'name' => 'News',
            'slug' => 'news',
            'description' => 'Latest news',
        ]);

        $this->assertSame(1, $cat->getId());
        $this->assertSame('News', $cat->getName());
        $this->assertSame('news', $cat->getSlug());
        $this->assertSame('Latest news', $cat->getDescription());
    }

    public function test_description_is_optional(): void
    {
        $cat = new Category(['id' => 1, 'name' => 'X', 'slug' => 'x']);
        $this->assertNull($cat->getDescription());
    }
}
