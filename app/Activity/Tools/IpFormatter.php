<?php

namespace BookStack\Activity\Tools;

class IpFormatter
{
    protected string $ip;
    protected int $precision;

    public function __construct(string $ip, int $precision)
    {
        $this->ip = trim($ip);
        $this->precision = max(0, min($precision, 4));
    }

    public function format(): string
    {
        if (empty($this->ip) || $this->precision === 4) {
            return $this->ip;
        }

        return $this->isIpv6() ? $this->maskIpv6() : $this->maskIpv4();
    }

    protected function maskIpv4(): string
    {
        $exploded = $this->explodeAndExpandIp('.', 4);
        $maskGroupCount = min(4 - $this->precision, count($exploded));

        for ($i = 0; $i < $maskGroupCount; $i++) {
            $exploded[3 - $i] = 'x';
        }

        return implode('.', $exploded);
    }

    protected function maskIpv6(): string
    {
        $exploded = $this->explodeAndExpandIp(':', 8);
        $maskGroupCount = min(8 - ($this->precision * 2), count($exploded));

        for ($i = 0; $i < $maskGroupCount; $i++) {
            $exploded[7 - $i] = 'x';
        }

        return implode(':', $exploded);
    }

    protected function isIpv6(): bool
    {
        return strpos($this->ip, ':') !== false;
    }

    protected function explodeAndExpandIp(string $separator, int $targetLength): array
    {
        $exploded = explode($separator, $this->ip);

        while (count($exploded) < $targetLength) {
            $emptyIndex = array_search('', $exploded) ?: count($exploded) - 1;
            array_splice($exploded, $emptyIndex, 0, '0');
        }

        $emptyIndex = array_search('', $exploded);
        if ($emptyIndex !== false) {
            $exploded[$emptyIndex] = '0';
        }

        return $exploded;
    }

    public static function fromCurrentRequest(): self
    {
        $ip = request()->ip() ?? '';

        if (config('app.env') === 'demo') {
            $ip = '127.0.0.1';
        }

        return new self($ip, config('app.ip_address_precision'));
    }
}
