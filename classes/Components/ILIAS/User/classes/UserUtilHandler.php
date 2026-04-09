<?php
namespace KPG\RestAPI\ILIAS\User\classes;

class UserUtilHandler
{
    public function userExists(int|string $user_identifier): bool
    {
        if (is_numeric($user_identifier)) {
            return \ilObjUser::_exists((int) $user_identifier, false, 'usr');
        } else {
            return (bool) \ilObjUser::_loginExists((string) $user_identifier);
        }
    }

    public function roleExists(int $id): bool
    {
        if (\ilObjRole::_exists($id)) {
            return true;
        } else {
            return false;
        }
    }
}