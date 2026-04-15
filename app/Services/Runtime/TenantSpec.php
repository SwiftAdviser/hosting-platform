<?php

declare(strict_types=1);

namespace App\Services\Runtime;

final readonly class TenantSpec
{
    public function __construct(
        public string $agentName,
        public string $personality,
        public string $telegramBotToken,
        public string $llmApiKey,
        public string $imageTag,
        public string $webhookBaseDomain,
        public ?array $allowlist = [],
        public string $llmProvider = 'anthropic',
    ) {
        if (trim($this->agentName) === '') {
            throw new \InvalidArgumentException('agentName must not be empty');
        }
        if (trim($this->personality) === '') {
            throw new \InvalidArgumentException('personality must not be empty');
        }
        if (trim($this->telegramBotToken) === '') {
            throw new \InvalidArgumentException('telegramBotToken must not be empty');
        }
        if (trim($this->llmApiKey) === '') {
            throw new \InvalidArgumentException('llmApiKey must not be empty');
        }
        if (trim($this->imageTag) === '') {
            throw new \InvalidArgumentException('imageTag must not be empty');
        }
        if (trim($this->webhookBaseDomain) === '') {
            throw new \InvalidArgumentException('webhookBaseDomain must not be empty');
        }
    }
}
