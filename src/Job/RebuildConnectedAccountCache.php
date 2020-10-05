<?php

/**
 * This file is a part of [Telegram] Importers.
 * All rights reserved.
 *
 * Developed by SourceModders.
 */

namespace SModders\TelegramImporters\Job;


use XF\Job\AbstractRebuildJob;

class RebuildConnectedAccountCache extends AbstractRebuildJob
{
    protected function getNextIds($start, $batch)
    {
        $db = $this->app->db();

        return $db->fetchAllColumn($db->limit("
            SELECT user_id
            FROM xf_user
            WHERE user_id > ?
            ORDER BY user_id
        ", $batch), $start);
    }

    protected function rebuildById($id)
    {
        /** @var \XF\Entity\User $user */
        $user = $this->app->em()->find('XF:User', $id, ['Profile']);
        if (!$user)
        {
            return;
        }

        $this->connectedAccountRepo()->rebuildUserConnectedAccountCache($user);
    }

    protected function getStatusType()
    {
        return \XF::phrase('connected_accounts');
    }

    public function canCancel()
    {
        return false;
    }

    /**
     * @return \XF\Repository\ConnectedAccount
     */
    protected function connectedAccountRepo()
    {
        return $this->app->repository('XF:ConnectedAccount');
    }
}