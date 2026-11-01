<?php
namespace App\Support;
final class HomeVideoSource
{
    public static function local(string $value): bool {
        return ! str_contains($value,'..') && preg_match('~^/vids/(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_.-]+\.mp4$~D',$value) === 1;
    }
    public static function aparat(string $value): bool {
        $url=parse_url($value);
        return is_array($url) && ($url['scheme']??'')==='https' && in_array($url['host']??'',['aparat.com','www.aparat.com'],true)
            && (!isset($url['port']) || $url['port']===443) && !isset($url['user']) && !isset($url['pass'])
            && preg_match('~^/(?:(?:_/)?v/[A-Za-z0-9]+|v/[A-Za-z0-9]+|video/video/embed/(?:videohash/[A-Za-z0-9]+/vt/frame|vt/frame/showvideo/yes/videohash/[A-Za-z0-9]+))/?$~D',$url['path']??'')===1;
    }
}
