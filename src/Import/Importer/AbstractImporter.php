<?php

/**
 * This file is a part of [Telegram] Importers.
 * All rights reserved.
 *
 * Developed by SourceModders.
 */

namespace SModders\TelegramImporters\Import\Importer;


use XF\Db\AbstractAdapter;

abstract class AbstractImporter extends \XF\Import\Importer\AbstractImporter
{
    public function validateBaseConfig(array &$baseConfig, array &$errors)
    {
        return true;
    }

    protected function getBaseConfigDefault()
    {
        return [];
    }

    public function renderBaseConfigOptions(array $vars)
    {
    }

    protected function getStepConfigDefault()
    {
        return [];
    }

    public function renderStepConfigOptions(array $vars)
    {
        return '';
    }

    public function validateStepConfig(array $steps, array &$stepConfig, array &$errors)
    {
        return true;
    }

    public function canRetainIds()
    {
        return false;
    }

    public function resetDataForRetainIds()
    {
    }

    protected function doInitializeSource()
    {
    }

    public function getFinalizeJobs(array $stepsRun)
    {
        return [];
    }

    /**
     * Returns the add-on version by database connection.
     *
     * @param string $addOnId
     * @return int|null
     */
    protected function resolveAddOnVersion($addOnId)
    {
        $addOnVersion = $this->db()->fetchOne(
            "SELECT version_id FROM xf_addon WHERE addon_id = ?",
            [$addOnId]
        );

        return (!$addOnVersion) ? null : intval($addOnVersion);
    }

    protected function query($text, array $params = [])
    {
        return $this->db()->query($text, $params);
    }

    protected function rowsAffected($text, array $params = [])
    {
        return $this->query($text, $params)->rowsAffected();
    }
}
