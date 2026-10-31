<?php

namespace Tests\Unit;

use App\Models\Juego;
use App\Support\UniqueSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_sufijo_cuando_el_slug_ya_existe(): void
    {
        Juego::create(['nombre' => 'Fight Games', 'slug' => 'fight-games']);

        $slug = UniqueSlug::for(Juego::class, 'Fight Games');

        $this->assertSame('fight-games-2', $slug);
    }
}
