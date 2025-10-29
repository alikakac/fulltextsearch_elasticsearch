<?php
declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FullTextSearch_Elasticsearch\Service;


use OCA\FullTextSearch_Elasticsearch\AppInfo\Application;
use OCA\FullTextSearch_Elasticsearch\ConfigLexicon;
use OCA\FullTextSearch_Elasticsearch\Exceptions\ConfigurationException;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IConfig;


/**
 * Class ConfigService
 *
 * @package OCA\FullTextSearch_Elasticsearch\Service
 */
class ConfigService {
	public function __construct(
		private readonly IConfig $config,
		private readonly IAppConfig $appConfig,
	) {
	}

	public function getConfig(): array {
		return [
			ConfigLexicon::FIELDS_LIMIT => $this->appConfig->getAppValueInt(ConfigLexicon::FIELDS_LIMIT),
			ConfigLexicon::ELASTIC_HOST => $this->appConfig->getAppValueString(ConfigLexicon::ELASTIC_HOST),
			ConfigLexicon::ELASTIC_INDEX => $this->appConfig->getAppValueString(ConfigLexicon::ELASTIC_INDEX),
			ConfigLexicon::ELASTIC_LOGGER_ENABLED => $this->appConfig->getAppValueBool(ConfigLexicon::ELASTIC_LOGGER_ENABLED),
			ConfigLexicon::ANALYZER_TOKENIZER => $this->appConfig->getAppValueString(ConfigLexicon::ANALYZER_TOKENIZER),
			ConfigLexicon::ALLOW_SELF_SIGNED_CERT => $this->appConfig->getAppValueBool(ConfigLexicon::ALLOW_SELF_SIGNED_CERT),
			ConfigLexicon::TENANT_ID => $this->appConfig->getAppValueString(ConfigLexicon::TENANT_ID),
		];
	}

	public function setConfig(array $save): void {
		foreach(array_keys($save) as $k) {
			switch($k) {
				case ConfigLexicon::FIELDS_LIMIT:
					$this->appConfig->setAppValueInt($k, $save[$k]);
					break;

				case ConfigLexicon::ELASTIC_HOST:
				case ConfigLexicon::ELASTIC_INDEX:
				case ConfigLexicon::ANALYZER_TOKENIZER:
				case ConfigLexicon::TENANT_ID:
					$this->appConfig->setAppValueString($k, $save[$k]);
					break;

				case ConfigLexicon::ELASTIC_LOGGER_ENABLED:
				case ConfigLexicon::ALLOW_SELF_SIGNED_CERT:
					$this->appConfig->setAppValueBool($k, $save[$k]);
					break;
			}
		}
	}

	public function getElasticIndex(): string {
		$index = $this->appConfig->getAppValueString(ConfigLexicon::ELASTIC_INDEX);
		if ($index === '') {
			throw new ConfigurationException('Your ElasticSearchPlatform is not configured properly');
		}

		return $index;
	}

	public function checkConfig(array $data): bool {
		return true;
	}

	/**
	 * Get the tenant ID for multi-tenant index isolation.
	 * This is mandatory for security to prevent cross-tenant data access.
	 *
	 * @return string
	 * @throws ConfigurationException if tenant_id is not configured
	 */
	public function getTenantId(): string {
		$tenantId = $this->appConfig->getAppValueString(ConfigLexicon::TENANT_ID);
		if ($tenantId === '') {
			throw new ConfigurationException('tenant_id must be configured for multi-tenant support');
		}
		return $tenantId;
	}
}

