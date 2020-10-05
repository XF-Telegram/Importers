<?php

/**
 * This file is a part of [Telegram] Importers.
 * All rights reserved.
 *
 * Developed by SourceModders.
 */

namespace SModders\TelegramImporters\Import\Importer;


use XF\Import\StepState;

class TelegramTfa extends AbstractImporter
{
    /**
     * @return array
     */
    public static function getListInfo()
    {
        return [
            'target'    => '[Telegram] Two Factor Authentication 2.x',
            'source'    => '[Telegram] Two Factor 1.x',
            'beta'      => true,
        ];
    }

    public function getSteps()
    {
        return [
            'telegramTwoFactors' => [
                'title' => \XF::phrase('smodders_tgimporter.connected_tfa_providers')
            ],
        ];
    }
    
    protected function migrateProvider($from, $to)
    {
        $queryParams = [
            $to,    // new provider_id
            '',     // new provider_data
            $from   // old provider_id
        ];
        
        return $this->rowsAffected('
            UPDATE
                xf_user_tfa
            SET
                provider_id = ?,
                provider_data = ?
            WHERE
                provider_id = ?
        ', $queryParams);
    }
    
    // ############### STEPS FOR IMPORTING DATA FROM OLD ADD-ON ###############
    public function stepTelegramTwoFactors(StepState $state)
    {
        $buttonMembers = $this->migrateProvider('telegram_buttons', 'smtgtfa_click');
        $codeMembers = $this->migrateProvider('telegram_code', 'smtgtfa_code');
        $state->imported = $buttonMembers + $codeMembers;
        
        return $state->complete();
    }
}