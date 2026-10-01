<?php

namespace App\Services;

use InvalidArgumentException;

class StyleProfileCompiler
{
    public const SCHEMA_VERSION = 1;

    /** @var array<string, string> */
    public const TOKENS = [
        'green' => '--brand-green',
        'blue' => '--brand-blue',
        'mint' => '--brand-mint',
        'white' => '--brand-white',
        'gold' => '--brand-gold',
    ];

    /** @var list<string> */
    public const LOCALES = ['fa', 'ar', 'en', 'ku'];

    /**
     * @param  array<string, mixed>  $styles
     * @param  array<string, mixed>  $texts
     * @return array{styles: array<string, array<string, mixed>>, texts: array<string, array<string, string>>, css: string, checksum: string}
     */
    public function prepare(array $styles, array $texts): array
    {
        $normalizedStyles = $this->normalizeStyles($styles);
        $normalizedTexts = $this->normalizeTexts($texts);
        $css = $this->compile($normalizedStyles);

        $checksum = hash('sha256', json_encode([
            'schema' => self::SCHEMA_VERSION,
            'styles' => $normalizedStyles,
            'texts' => $normalizedTexts,
            'css' => $css,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return [
            'styles' => $normalizedStyles,
            'texts' => $normalizedTexts,
            'css' => $css,
            'checksum' => $checksum,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $styles
     */
    public function compile(array $styles): string
    {
        $blocks = [];

        foreach ($styles as $id => $style) {
            $declarations = [];

            if (isset($style['textColor'])) {
                $declarations[] = 'color:'.$this->tokenVar($style['textColor']).'!important';
            }

            if (isset($style['backgroundColor'])) {
                $declarations[] = 'background:'.$this->tokenVar($style['backgroundColor']).'!important';
            }

            if (isset($style['borderColor'])) {
                $declarations[] = 'border-color:'.$this->tokenVar($style['borderColor']).'!important';
            }

            if (($style['hidden'] ?? false) === true) {
                $declarations[] = 'display:none!important';
            }

            if ($declarations !== []) {
                $blocks[] = sprintf(
                    '[data-style-id="%s"]{%s}',
                    $id,
                    implode(';', $declarations),
                );
            }
        }

        return implode("\n", $blocks);
    }

    /**
     * @param  array<string, mixed>  $styles
     * @return array<string, array<string, mixed>>
     */
    private function normalizeStyles(array $styles): array
    {
        ksort($styles, SORT_STRING);
        $normalized = [];

        foreach ($styles as $id => $style) {
            $this->assertStyleId($id);

            if (! is_array($style)) {
                throw new InvalidArgumentException("Style override [{$id}] must be an array.");
            }

            $entry = [];

            foreach (['textColor', 'backgroundColor', 'borderColor'] as $key) {
                if (! array_key_exists($key, $style) || $style[$key] === null) {
                    continue;
                }

                $token = $style[$key];

                if (! is_string($token) || ! array_key_exists($token, self::TOKENS)) {
                    throw new InvalidArgumentException("Unknown style token for [{$id}.{$key}].");
                }

                $entry[$key] = $token;
            }

            if (($style['hidden'] ?? false) === true) {
                $entry['hidden'] = true;
            }

            if ($entry !== []) {
                $normalized[$id] = $entry;
            }
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $texts
     * @return array<string, array<string, string>>
     */
    private function normalizeTexts(array $texts): array
    {
        ksort($texts, SORT_STRING);
        $normalized = [];

        foreach ($texts as $id => $translations) {
            $this->assertStyleId($id);

            if (! is_array($translations)) {
                throw new InvalidArgumentException("Text override [{$id}] must be an array.");
            }

            $entry = [];

            foreach (self::LOCALES as $locale) {
                $value = $translations[$locale] ?? null;

                if (! is_string($value) || $value === '') {
                    continue;
                }

                $entry[$locale] = $value;
            }

            if ($entry !== []) {
                $normalized[$id] = $entry;
            }
        }

        return $normalized;
    }

    private function tokenVar(string $token): string
    {
        return 'var('.self::TOKENS[$token].')';
    }

    private function assertStyleId(string $id): void
    {
        if (! preg_match('/^[A-Za-z0-9._-]+$/', $id)) {
            throw new InvalidArgumentException("Invalid style target id [{$id}].");
        }
    }
}
