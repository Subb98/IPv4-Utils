<?php declare(strict_types=1);

namespace Subb98\IPv4Utils;

use DomainException;
use RuntimeException;

/**
 * Class IPv4
 * Класс для работы с IP-адресами версии 4
 * https://habr.com/ru/post/314484/
 *
 * @method getBinary Возвращает IP-адрес в двоичном формате
 * @method getClass Возвращает класс, к которому принадлежит IP-адрес
 * @method getDefaultSubnetMask Возвращает маску подсети по умолчанию
 * @method getClassfulSubnet Возвращает возможную маску в классовой адресации для указанного количества подсетей и хостов
 *
 * @package Subb98\IPv4Utils
 * @author Vladislav Subbotin <subb98@gmail.com>
 * @license MIT
 */
class IPv4
{
    public function __construct(private string $ip)
    {
    }

    /**
     * Возвращает IP-адрес в двоичном формате.
     * @return string
     */
    public function getBinary(): string
    {
        $octets = explode('.', $this->ip);
        $result = [];

        foreach ($octets as $octet) {
            $result[] = $this->getOctetBinary((int)$octet);
        }

        return implode('.', $result);
    }

    /**
     * Возвращает класс, к которому принадлежит IP-адрес.
     * Речь идёт о классовой адресации IP-сетей, которая сегодня не используется.
     * Эта функция может быть полезна для определения маски подсети.
     * @return string
     * @throws RuntimeException
     */
    public function getClass(): string
    {
        $octets = explode('.', $this->ip);
        $binaryOctet = $this->getOctetBinary((int)$octets[0]);
        $classes = [
            'A' => '0',
            'B' => '10',
            'C' => '110',
            'D' => '1110',
            'E' => '1111',
        ];

        foreach ($classes as $class => $bits) {
            if (str_starts_with($binaryOctet, $bits)) {
                return $class;
            }
        }

        throw new RuntimeException('Unknown class of IP-address');
    }

    /**
     * Возвращает маску подсети по умолчанию.
     * @return string|null
     * @throws RuntimeException
     */
    public function getDefaultSubnetMask(): ?string
    {
        $class = $this->getClass();
        $classes = [
            'A' => '255.0.0.0',
            'B' => '255.255.0.0',
            'C' => '255.255.255.0',
            'D' => null,
            'E' => null,
        ];

        foreach ($classes as $k => $mask) {
            if ($k === $class) {
                return $mask;
            }
        }

        throw new RuntimeException('Unknown class of IP-address');
    }

    /**
     * Возвращает возможную маску в классовой адресации
     * для указанного количества подсетей и хостов.
     * @param int $subnetsCount Кол-во подсетей
     * @param int $hostsCount Кол-во хостов
     * @return array
     * @throws DomainException
     */
    public function getClassfulSubnet(int $subnetsCount, int $hostsCount): array
    {
        // N + S + H = 32
        // N - кол-во битов сети (класс A = 8 бит, B = 16 бит, C = 24 бита)
        // S - кол-во заимствованных битов на подсеть
        // H - кол-во бит, отводимых хостам

        // Вычислить класс адреса, чтобы определить N
        $N = null;
        $S = null;
        $H = null;

        $class = $this->getClass();
        $classes = [
            'A' => 8,
            'B' => 16,
            'C' => 24,
            'D' => null,
            'E' => null,
        ];

        foreach ($classes as $k => $bits) {
            if ($k === $class) {
                $N = $bits; // определили кол-во битов под сеть
                break;
            }
        }

        $H = 32 - $N; // определили кол-во битов под хосты (до выделения битов на подсети)
        $S = ceil(log($subnetsCount, 2)); // определяем кол-во битов на подсети
        $H -= $S; // определили кол-во битов под хосты (после выделения битов на подсети)
        // проверяем, хватает ли оставшихся битов под хосты согласно запросу
        $resultHostsCount = (2 ** $H) - 2; // вычитаем ещё 2 адреса: адрес сети и широковещательный адрес

        if ($resultHostsCount < $hostsCount) {
            throw new DomainException('Impossible condition');
        }

        $maskBits = $N + $S; // 16 + 7 = 23 bits
        $maskBinary = $this->maskBitsToBinary((int)$maskBits);
        $maskDecimal = $this->getBinaryMaskToDecimal($maskBinary);

        return ['class' => $class, 'network_bits' => $N, 'subnets_bits' => (int)$S, 'hosts_bits' => (int)$H, 'subnet_mask_binary' => $maskBinary, 'subnet_mask_decimal' => $maskDecimal, 'subnet_mask_short' => "{$this->ip}/{$maskBits}"];
    }

    /**
     * Возвращает октет в двоичном виде.
     * @param int $octet Октет в десятичном виде
     * @return string
     */
    private function getOctetBinary(int $octet): string
    {
        $result = [];

        while ($octet > 0) {
            $result[] = $octet % 2;
            $octet = floor($octet / 2);
        }

        $fill = 8 - count($result);

        for ($i = 0; $i < $fill; $i++) {
            $result[] = 0;
        }

        $result = array_reverse($result);
        return implode($result);
    }

    /**
     * Преобразует маску подсети в двоичный формат.
     * @param int $bitsCount Кол-во битов для маски подсети
     * @return string
     */
    private function maskBitsToBinary(int $bitsCount): string
    {
        $result = [];

        for ($i = 0, $c = 0; $i < 32; $i++) {
            if ($i < $bitsCount) {
                $result[] = 1;
            } else {
                $result[] = 0;
            }
            if (8 === ++$c && $i < 31) {
                // октет заполнен, нужно добавить разделяющий символ
                $result[] = '.';
                $c = 0;
            }
        }

        return implode($result);
    }

    private function getBinaryMaskToDecimal(string $binaryMask): string
    {
        $octets = explode('.', $binaryMask);
        $result = [];

        foreach ($octets as $octet) {
            $result[] = $this->getBinaryOctet($octet);
        }

        return implode('.', $result);
    }

    private function getBinaryOctet(string $binary): int
    {
        $bits = str_split($binary);
        $bits = array_reverse($bits);
        $result = 0;

        foreach ($bits as $k => $v) {
            if (1 === (int)$v) {
                $result += 2 ** $k;
            }
        }

        return $result;
    }
}
