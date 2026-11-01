<?php
namespace Tests\Unit;
use App\Support\HomeVideoSource;
use Tests\TestCase;
class HomeVideoSourceTest extends TestCase {
 public function test_only_local_video_paths_and_canonical_aparat_urls_are_accepted(): void {
  $this->assertTrue(HomeVideoSource::local('/vids/farsi.mp4'));
  $this->assertTrue(HomeVideoSource::aparat('https://www.aparat.com/v/abc123'));
  $this->assertTrue(HomeVideoSource::aparat('https://www.aparat.com/video/video/embed/videohash/abc123/vt/frame'));
  foreach(['https://evil.test/file.mp4','/vids/../private.mp4'] as $path)$this->assertFalse(HomeVideoSource::local($path));
  foreach(['javascript:alert(1)','https://aparat.com.evil.test/v/abc','https://user@aparat.com/v/abc','<iframe>'] as $url)$this->assertFalse(HomeVideoSource::aparat($url));
 }
}
