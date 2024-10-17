<?php

declare(strict_types=1);

namespace NuonicOverrideAccessTokenTtl\Config;

use Shopware\Core\System\SystemConfig\Exception\InvalidSettingValueException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PluginConfigService
{

    private SystemConfigService $systemConfigService;
    private string $configDomain;

    public function __construct(
        SystemConfigService $systemConfigService,
        string $configDomain
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->configDomain = $configDomain;
    }

    /**
     *
     * @param string $key
     * @param string|null $salesChannelId
     * @return array<mixed>|bool|float|int|string|null
     */
    public function get(string $key, ?string $salesChannelId = null, ?bool $inherit = true)
    {
        $result = $this->systemConfigService->get(
            sprintf('%s.%s', $this->configDomain, $key),
            $salesChannelId
        );

        if (is_null($result) && $inherit && is_string($salesChannelId)) {
            return $this->get($key);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param string|null $salesChannelId
     * @return int
     */
    public function getInt(string $key, ?string $salesChannelId = null, ?bool $inherit = true): int
    {
        try {
            return $this->systemConfigService->getInt(
                sprintf('%s.%s', $this->configDomain, $key),
                $salesChannelId
            );
        } catch (InvalidSettingValueException $e) {
            if ($inherit && is_string($salesChannelId)) {
                return $this->getInt($key);
            }

            throw $e;
        }
    }
}
