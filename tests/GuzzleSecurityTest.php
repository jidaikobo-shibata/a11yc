<?php

declare(strict_types=1);

namespace Jidaikobo\A11yc\Tests;

use Jidaikobo\A11yc\Guzzle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GuzzleSecurityTest extends TestCase
{
    #[DataProvider('unsafeUrlProvider')]
    public function testUnsafeRemoteUrlsAreRejected(string $url): void
    {
        self::assertFalse(Guzzle::isSafeRemoteUrl($url));
    }

    public static function unsafeUrlProvider(): array
    {
        return array(
            'localhost' => array('http://localhost/report.pdf'),
            'localhost subdomain' => array('https://foo.localhost/report.pdf'),
            'IPv4 loopback' => array('http://127.0.0.1/report.pdf'),
            'IPv4 private' => array('http://10.0.0.1/report.pdf'),
            'IPv4 link local' => array('http://169.254.169.254/latest/meta-data.pdf'),
            'IPv6 loopback' => array('http://[::1]/report.pdf'),
            'IPv6 private' => array('http://[fc00::1]/report.pdf'),
            'file scheme' => array('file:///etc/passwd'),
            'credentials' => array('https://user:pass@93.184.216.34/report.pdf'),
            'single-label host' => array('http://intranet/report.pdf'),
            'relative URL' => array('/report.pdf'),
        );
    }

    public function testPublicRemoteUrlIsAccepted(): void
    {
        self::assertTrue(Guzzle::isSafeRemoteUrl('https://93.184.216.34/report.pdf'));
    }

    public function testUnsafeUrlIsBlockedBeforeARequestIsMade(): void
    {
        $client = Guzzle::forge('http://127.0.0.1/report.pdf');

        self::assertNull($client->is_exists);
        self::assertContains('UnsafeUrl', $client->errors);
    }
}
