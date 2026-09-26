<?php

namespace MoonWalkerz\Contact\Classes\Captcha;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Math challenges and single-use token bookkeeping.
 *
 * A challenge travels in the form as an HMAC-signed payload, so verification
 * does not depend on the cache. The cache is only used to stop a challenge
 * or provider token from being submitted twice.
 */
class ChallengeStore
{
    const USED_PREFIX = 'mm_captcha_used_';
    const TOKEN_PREFIX = 'mm_captcha_tk_';

    /**
     * @return array{id:string,question:string}
     */
    public static function create(int $ttl): array
    {
        $op = ['+', '-', 'x'][random_int(0, 2)];

        if ($op === 'x') {
            $a = random_int(2, 9);
            $b = random_int(2, 9);
        } else {
            $a = random_int(1, 19);
            $b = random_int(1, 19);
        }

        if ($op === '-' && $b > $a) {
            [$a, $b] = [$b, $a];
        }

        $answer = match ($op) {
            '-' => $a - $b,
            'x' => $a * $b,
            default => $a + $b,
        };

        $claims = [
            'j' => Str::random(16),
            'e' => time() + max(60, $ttl),
        ];
        $claims['a'] = self::answerHash($answer, $claims['j']);

        return [
            'id'       => self::sign($claims),
            'question' => sprintf('%d %s %d', $a, $op, $b),
        ];
    }

    /**
     * Checks an answer. Any attempt burns the challenge, so a bot cannot
     * brute-force the same question: the page fetches a fresh one on failure.
     *
     * @return true|string True when valid, otherwise an error code.
     */
    public static function consume($payload, $answer)
    {
        $claims = self::parse($payload);

        if (! $claims) {
            return 'invalid';
        }

        if ($claims['e'] < time()) {
            return 'expired';
        }

        if (! Cache::add(self::USED_PREFIX . $claims['j'], 1, max(60, $claims['e'] - time()))) {
            return 'replay';
        }

        $answer = self::normalise($answer);

        if ($answer === '') {
            return 'empty';
        }

        if (! hash_equals($claims['a'], self::answerHash((int) $answer, $claims['j']))) {
            return 'wrong';
        }

        return true;
    }

    /**
     * Marks a provider token as used.
     *
     * @return bool True when the token had not been seen before.
     */
    public static function claimToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        return Cache::add(self::TOKEN_PREFIX . substr(hash('sha256', $token), 0, 32), 1, 300);
    }

    /**
     * Signed timestamp for the minimum-fill-time trap.
     */
    public static function signTimestamp(): string
    {
        $time = (string) time();

        return $time . '.' . substr(hash_hmac('sha256', 'ts|' . $time, self::key()), 0, 32);
    }

    /**
     * @return int|false Age in seconds, or false when invalid.
     */
    public static function readTimestamp($value)
    {
        if (! is_string($value) || ! str_contains($value, '.')) {
            return false;
        }

        [$time, $sig] = explode('.', $value, 2);

        if (! ctype_digit($time)) {
            return false;
        }

        $expected = substr(hash_hmac('sha256', 'ts|' . $time, self::key()), 0, 32);

        if (! hash_equals($expected, $sig)) {
            return false;
        }

        return max(0, time() - (int) $time);
    }

    /**
     * Stable but unguessable honeypot field name.
     */
    public static function honeypotName(): string
    {
        return 'mm_' . substr(hash_hmac('sha256', 'honeypot', self::key()), 0, 10);
    }

    protected static function normalise($answer): string
    {
        if (! is_scalar($answer)) {
            return '';
        }

        $answer = str_replace([' ', '.', ','], '', trim((string) $answer));

        return preg_match('/^-?\d{1,6}$/', $answer) ? $answer : '';
    }

    protected static function sign(array $claims): string
    {
        $body = self::b64(json_encode($claims));

        return 'v1.' . $body . '.' . self::b64(hash_hmac('sha256', $body, self::key(), true));
    }

    protected static function parse($payload)
    {
        if (! is_string($payload)) {
            return false;
        }

        $parts = explode('.', $payload);

        if (count($parts) !== 3 || $parts[0] !== 'v1') {
            return false;
        }

        $expected = self::b64(hash_hmac('sha256', $parts[1], self::key(), true));

        if (! hash_equals($expected, $parts[2])) {
            return false;
        }

        $claims = json_decode(self::unb64($parts[1]), true);

        if (! is_array($claims) || ! isset($claims['j'], $claims['e'], $claims['a'])) {
            return false;
        }

        return $claims;
    }

    protected static function answerHash(int $answer, string $jti): string
    {
        return hash_hmac('sha256', $answer . '|' . $jti, self::key());
    }

    protected static function key(): string
    {
        return 'mm_captcha|' . config('app.key');
    }

    protected static function b64(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected static function unb64(string $value): string
    {
        return (string) base64_decode(strtr($value, '-_', '+/'), true);
    }
}
