<?php

declare(strict_types=1);

namespace Jidaikobo\A11yc\Tests;

use Jidaikobo\A11yc\Analyzer;
use Jidaikobo\A11yc\CheckRegistry;
use PHPUnit\Framework\TestCase;

final class AnalyzerTest extends TestCase
{
    public function testAnalyzeResultSetReturnsStructuredSummary(): void
    {
        $analyzer = new Analyzer();

        $result = $analyzer->analyzeResultSet(
            'https://example.com/',
            array(
                'errors' => array(
                    'errors' => array(),
                    'notices' => array(),
                ),
                'errs_cnts' => array(
                    'a' => 0,
                    'aa' => 0,
                    'aaa' => 0,
                ),
            ),
            array(
                'url' => 'https://example.com/',
                'user_agent' => 'using',
            )
        );

        self::assertSame('https://example.com/', $result['meta']['url']);
        self::assertIsArray($result['issues']);
        self::assertSame(0, $result['summary']['error_count']);
        self::assertSame(0, $result['summary']['notice_count']);
        self::assertSame(array('a' => 0, 'aa' => 0, 'aaa' => 0), $result['summary']['counts_by_level']);
    }

    public function testExtractImagesReturnsImageSummary(): void
    {
        $analyzer = new Analyzer();

        $images = $analyzer->extractImages(
            '<img src="/a.png" alt="sample">',
            array(
                'url' => 'https://example.com/',
            )
        );

        self::assertCount(1, $images);
        self::assertSame('img', $images[0]['element']);
        self::assertStringContainsString('sample', (string) $images[0]['alt']);
        self::assertStringContainsString('example.com', (string) $images[0]['src']);
        self::assertStringContainsString('/a.png', (string) $images[0]['src']);
    }

    public function testAnalyzeHtmlReturnsIssuesForRealHtml(): void
    {
        $analyzer = new Analyzer();

        try {
            ob_start();
            $result = $analyzer->analyzeHtml(
                '<!doctype html><html lang="ja"><head><title>Test</title></head><body><img src="/a.png"></body></html>',
                array(
                    'url' => 'https://example.com/',
                )
            );
        } finally {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        self::assertSame('https://example.com/', $result['meta']['url']);
        self::assertGreaterThanOrEqual(1, $result['summary']['error_count']);
        self::assertNotEmpty($result['issues']);
        self::assertContains(
            'alt_attr_of_img',
            array_column($result['issues'], 'id')
        );
    }

    public function testCheckRegistryScansAvailableChecks(): void
    {
        $available = CheckRegistry::availableChecks();

        self::assertContains('AltAttrOfImg', $available);
        self::assertContains('LinkCheck', $available);
        self::assertSame(array_values(array_unique($available)), $available);
    }

    public function testCheckRegistryCanRegisterExternalChecks(): void
    {
        CheckRegistry::clearExtensions();

        try {
            CheckRegistry::register(
                'ExternalCheck',
                '\\Jidaikobo\\A11yc\\Validate\\Check\\Titleless',
                'check'
            );

            self::assertContains('ExternalCheck', CheckRegistry::availableChecks());
            self::assertSame(
                array('\\Jidaikobo\\A11yc\\Validate\\Check\\Titleless', 'check'),
                CheckRegistry::resolve('ExternalCheck')
            );
        } finally {
            CheckRegistry::clearExtensions();
        }
    }
}
