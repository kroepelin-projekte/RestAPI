<?php

namespace KPG\RestAPI\ILIAS\Repository\classes;

use KPG\RestAPI\API\Exception\ObjectNotFoundException;
use KPG\RestAPI\API\Exception\UserNotFoundException;

class RepositoryLPHandler
{
    public function getLPByRefID(int $ref_id): array
    {
        if (!\ilObject::_exists($ref_id, true)) {
            throw new ObjectNotFoundException();
        }
        $obj_id = \ilObject::_lookupObjId($ref_id);
        $object_lp = \ilObjectLP::getInstance($obj_id);
        $users = $object_lp->getMembers(true);
        $lp_users = [];

        foreach ($users as $user_id) {
            $lp_users[$user_id] = $this->getLPData($obj_id, $user_id);
        }
        return $lp_users;
    }

    private function getLPData(int $obj_id, int $user_id): array
    {
        return [
            'status' => $this->getLPStatus(\ilLPStatus::_lookupStatus($obj_id, $user_id)),
            'timestamp' => $this->getLPStatusChangedTimestamp($obj_id, $user_id)
        ];
    }

    private function getLPStatus(int $lp_status): string
    {
        switch ($lp_status) {
            case 0:
                return 'not_attempted';
            case 1:
                return 'in_progress';
            case 2:
                return 'completed';
            case 3:
                return 'failed';
            default:
                return "not_tracked";
        }
    }
    private function getLPStatusChangedTimestamp(int $obj_id, int $user_id): ?int
    {
        $status_changed = \ilLPStatus::_lookupStatusChanged($obj_id, $user_id);

        if ($status_changed === null || $status_changed === '') {
            return null;
        }

        $timestamp = strtotime($status_changed);

        return $timestamp !== false ? $timestamp : null;
    }

    public function getLPByRefIDAndUserID(int $ref_id, int $user_id): array
    {
        if (!\ilObject::_exists($ref_id, true)) {
            throw new ObjectNotFoundException();
        }
        if (!\ilObjUser::_exists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_id = \ilObject::_lookupObjId($ref_id);

        return $this->getLPData($obj_id, $user_id);
    }
}
