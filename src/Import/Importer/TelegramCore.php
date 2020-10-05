<?php

/**
 * This file is a part of [Telegram] Importers.
 * All rights reserved.
 *
 * Developed by SourceModders.
 */

namespace SModders\TelegramImporters\Import\Importer;


use XF\Db\AbstractAdapter;
use XF\Import\StepState;

class TelegramCore extends AbstractImporter
{
    /**
     * @return array
     */
    public static function getListInfo()
    {
        return [
            'target'    => '[Telegram] Core 2.x',
            'source'    => '[Telegram] Core 1.0.7',
            'beta'      => true,
        ];
    }

    public function validateBaseConfig(array &$baseConfig, array &$errors)
    {
        if (!$this->validateVersion($versionError))
        {
            $errors[] = $versionError;
            return false;
        }

        return true;
    }

    public function getSteps()
    {
        return [
            'telegramUsers' => [
                'title' => \XF::phrase('smodders_tgimporter.accounts')
            ],

            'connectedAccounts' => [
                'title' => \XF::phrase('connected_accounts'),
                'depends' => ['telegramUsers'] // idk what i do to work this, lol
            ],
        ];
    }

    public function getFinalizeJobs(array $stepsRun)
    {
        $jobsToRun = [];
        if (in_array('connectedAccounts', $stepsRun))
        {
            $jobsToRun[] = 'SModders\TelegramImporter:RebuildConnectedAccountCache';
        }

        return $jobsToRun;
    }

    // ############### STEPS FOR IMPORTING DATA FROM OLD ADD-ON ###############
    public function stepTelegramUsers(StepState $state)
    {
        $versionId = $this->resolveCoreVersion();
        $sourceTable = ($versionId >= 1010034 ? 'xf_' : '') . 'tg_user';

        $state->imported = $this->rowsAffected("
            INSERT IGNORE INTO xf_smodders_tgcore_user
            SELECT id, first_name, last_name, username, updated
            FROM $sourceTable
        ");
        return $state->complete();
    }

    public function stepConnectedAccounts(StepState $state)
    {
        $state->imported = \XF::db()->query("
            UPDATE xf_user_connected_account
            SET provider = 'smodders_telegram'
            WHERE provider = 'telegram'
        ")->rowsAffected();

        return $state->complete();
    }

    protected function resolveCoreVersion($isLegacy = true)
    {
        return $this->resolveAddOnVersion($isLegacy ? 'Kruzya\Telegram' : 'SModders\TelegramCore');
    }

    /**
     * Validates an old Add-On version and prepares error message.
     *
     * @param AbstractAdapter $db
     * @param string|null $versionError
     * @return boolean
     */
    protected function validateVersion(&$versionError = null)
    {
        $versionId = $this->resolveCoreVersion();
        if ($versionId === null)
        {
            $versionError = \XF::phrase('smodders_tgimporter.previous_installation_not_found');
            return false;
        }

        if ($versionId < 1007010)
        {
            $versionError = \XF::phrase('smodders_tgimporter.previous_installation_too_old', ['required' => '1.0.7']);
            return false;
        }

        return true;
    }
}