<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\MenuItem;
use PHPUnit\Framework\TestCase;

final class MenuItemTest extends TestCase
{
    public function test_is_visible_cast_to_bool(): void
    {
        $item = new MenuItem([
            'id' => 1,
            'label' => 'Home',
            'slug' => 'home',
            'type' => 'static',
            'position' => 1,
            'is_visible' => 1,
        ]);

        $this->assertTrue($item->getIsVisible());
        $this->assertSame(1, $item->getPosition());
    }

    public function test_default_entity_type_is_post(): void
    {
        $item = new MenuItem(['label' => 'X', 'slug' => 'x', 'type' => 'archive', 'position' => 1, 'is_visible' => true]);
        $this->assertSame('Post', $item->getEntityType());
    }
}
