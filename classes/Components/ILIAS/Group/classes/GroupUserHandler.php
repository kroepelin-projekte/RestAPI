<?php

namespace KPG\RestAPI\ILIAS\Group\classes;

use KPG\RestAPI\API\Exception\GroupNotFoundException;
use KPG\RestAPI\API\Exception\UserNotFoundException;
use KPG\RestAPI\API\Exception\DefaultRoleNotFoundException;

class GroupUserHandler
{
    private $DIC;
    private GroupUtilHandler $utilHandler;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->utilHandler = new GroupUtilHandler();
    }
    public function addUser(int $user_id, int $group_ref_id, string $group_default_role): void
    {
        if (!$this->utilHandler->groupExists($group_ref_id)) {
            throw new GroupNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_group = new \ilObjGroup($group_ref_id, true);
        switch ($group_default_role) {
            case 'admin':
                $role_id = $obj_group->getDefaultAdminRole();
                $participant_role = \ilParticipants::IL_GRP_ADMIN;
                break;
            case 'member':
                $role_id = $obj_group->getDefaultMemberRole();
                $participant_role = \ilParticipants::IL_GRP_MEMBER;
                break;
            default:
                throw new DefaultRoleNotFoundException();
        }
        $this->addParticipant($obj_group, $user_id, $participant_role, $role_id);
    }

    private function addParticipant(\ilObjGroup $obj_group, int $user_id, int $participant_role, int $role_id): void
    {
        $participants = $obj_group->getMembersObject();

        if ($participants->isAssigned($user_id)) {
            $this->DIC->rbac()->admin()->assignUser($role_id, $user_id);
            return;
        }

        $participants->add($user_id, $participant_role);
        $participants->sendNotification(
            \ilGroupMembershipMailNotification::TYPE_ADMISSION_MEMBER,
            $user_id
        );
    }

    public function deleteUser(int $user_id, int $group_ref_id): void
    {
        if (!$this->utilHandler->groupExists($group_ref_id)) {
            throw new GroupNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        (new \ilObjGroup($group_ref_id, true))->getMembersObject()->delete($user_id);
    }

    public function getAllUsers(int $group_ref_id, string $default_role = null): array
    {
        if (!$this->utilHandler->groupExists($group_ref_id)) {
            throw new GroupNotFoundException();
        }
        $users = [];
        if ($default_role == null) {
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjGroup($group_ref_id, true), 'member');
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjGroup($group_ref_id, true), 'admin');
        } else {
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjGroup($group_ref_id, true), $default_role);
        }

        return array_filter($users);
    }
}
