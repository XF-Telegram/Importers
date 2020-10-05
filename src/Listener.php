<?php

/**
 * This file is a part of [Telegram] Importers.
 * All rights reserved.
 *
 * Developed by SourceModders.
 */

namespace SModders\TelegramImporters;


class Listener
{
    public static function onTelegramImporterInitialization(\XF\SubContainer\Import $container, \XF\Container $parentContainer, array &$importers)
    {
        $importerPrefix = 'SModders\TelegramImporters:Telegram';

        $importers[] = $importerPrefix . 'Core'; // users, connected accounts
        $importers[] = $importerPrefix . 'Tfa';  // two factor authentication
    }
}