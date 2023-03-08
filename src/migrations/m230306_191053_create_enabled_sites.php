<?php

namespace craft\contentmigrations;

use adigital\cookieconsentbanner\CookieConsentBanner;
use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\db\Table;
use craft\services\Plugins;

/**
 * m230306_191053_create_enabled_sites migration.
 */
class m230306_191053_create_enabled_sites extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): void
    {
        $plugin = CookieConsentBanner::$plugin;
        $settings = CookieConsentBanner::$plugin->getSettings();
        $sites = (new Query())
            ->select(['{{%sites}}.id as id', '{{%elements}}.uid as uid'])
            ->from([Table::SITES])
            ->innerJoin([Table::ELEMENTS], '{{%sites}}.id = {{%elements}}.id')
            ->pairs();

        if (is_array($settings->enabled_sites)) {
            foreach ($settings->enabled_sites as $siteId) {
                if (in_array($siteId, $settings->enabled_sites)) {
                    $settings->enabled_sites[array_search($siteId, $settings->enabled_sites)] = $sites[str_replace("id_", "", $siteId)];
                }
            }
        }

        Craft::$app->getProjectConfig()->set(Plugins::CONFIG_PLUGINS_KEY . '.' . $plugin->handle . '.settings', $settings->toArray());
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230306_191053_create_enabled_sites cannot be reverted.\n";
        return false;
    }
}
