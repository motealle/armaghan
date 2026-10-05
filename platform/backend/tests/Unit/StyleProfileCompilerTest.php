<?php

namespace Tests\Unit;

use App\Services\StyleProfileCompiler;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StyleProfileCompilerTest extends TestCase
{
    public function test_compilation_is_deterministic_and_uses_only_registered_tokens(): void
    {
        $compiler = new StyleProfileCompiler();

        $first = $compiler->prepare([
            'hero.title' => [
                'backgroundColor' => 'white',
                'textColor' => 'blue',
            ],
            'footer.shell' => [
                'hidden' => true,
            ],
        ], [
            'hero.title' => [
                'fa' => 'عنوان',
                'en' => 'Title',
            ],
        ]);

        $second = $compiler->prepare([
            'footer.shell' => [
                'hidden' => true,
            ],
            'hero.title' => [
                'textColor' => 'blue',
                'backgroundColor' => 'white',
            ],
        ], [
            'hero.title' => [
                'en' => 'Title',
                'fa' => 'عنوان',
            ],
        ]);

        $this->assertSame($first['checksum'], $second['checksum']);
        $this->assertSame($first['css'], $second['css']);
        $this->assertStringContainsString('var(--brand-blue)', $first['css']);
        $this->assertStringContainsString('var(--brand-white)', $first['css']);
        $this->assertStringContainsString('display:none!important', $first['css']);
    }

    public function test_media_fit_and_focal_point_compile_to_bounded_css_variables(): void
    {
        $prepared=(new StyleProfileCompiler())->prepare([
            'hero.media'=>['imageFit'=>'cover','imagePositionX'=>25,'imagePositionY'=>70],
        ],[]);
        $this->assertSame('cover',$prepared['styles']['hero.media']['imageFit']);
        $this->assertStringContainsString('--editor-media-fit:cover',$prepared['css']);
        $this->assertStringContainsString('--editor-media-position:25% 70%',$prepared['css']);
    }

    public function test_invalid_target_ids_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StyleProfileCompiler())->prepare([
            'hero"]{color:red}/*' => [
                'textColor' => 'blue',
            ],
        ], []);
    }
}
