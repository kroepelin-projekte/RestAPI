<?php

namespace KPG\RestAPI\ILIAS\User\classes;

use KPG\RestAPI\API\Exception\UserNotFoundException;
use KPG\RestAPI\API\Exception\AttributesNotFoundException;
use KPG\RestAPI\API\Exception\RoleNotFoundException;

class UserHandler
{
    private $DIC;
    private UserUtilHandler $utilHandler;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->utilHandler = new UserUtilHandler();
    }

    public function getAllUser(): array
    {
        $user_array = [];
        foreach (\ilObjUser::_getAllUserData(['login', 'firstname', 'lastname']) as $user) {
            $user_array[] = [
                'user_id' => $user['usr_id'],
                'login' => $user['login'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname']
            ];
        }
        return $user_array;
    }

    public function getUserInformation(int $user_id): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $user_data = \ilObjUser::_getUserData([$user_id])[0];
        foreach (['passwd', 'passwd_enc_type', 'passwd_salt'] as $key) {
            if (array_key_exists($key, $user_data)) {
                unset($user_data[$key]);
            }
        }
        return $user_data;
    }

    public function updateUser(int $user_id, array $update_data): void
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_user = new \ilObjUser($user_id);
        $update_forwarded = true;
        $failed_methods = [];
        $success_methods = [];
        foreach ($update_data as $method => $value) {
            $method_name = 'set' . ucfirst($method);
            if (method_exists($obj_user, $method_name)) {
                $success_methods[$method_name] = $value;
            } else {
                $failed_methods[] = $method;
                $update_forwarded = false;
            }
        }
        if (!$update_forwarded) {
            throw new AttributesNotFoundException($failed_methods);
        }
        foreach ($success_methods as $method => $value) {
            $obj_user->$method($value);
        }
        $obj_user->update();
    }

    public function getUserCourses(int $user_id): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $courses = [];
        foreach (\ilObject::_getObjectsByType('crs') as $obj_crs) {
            foreach (\ilObjCourse::_getAllReferences($obj_crs['obj_id']) as $ref_id) {
                if (\ilObject::_isInTrash($ref_id)) {
                    continue;
                }
                $obj_course = new  \ilObjCourse($ref_id, true);
                if ($obj_course->getMembersObject()->isAssigned($user_id)) {
                    $course = [
                        "ref_id" => $ref_id,
                        "title" => $obj_course->getTitle(),
                    ];
                    $user_roles = $obj_course->getMembersObject()->getAssignedRoles($user_id);
                    foreach ($user_roles as $role) {
                        if ($tmp_obj = \ilObjectFactory::getInstanceByObjId($role, false)) {
                            $course['user_role'][] = $tmp_obj->untranslatedTitle;
                        }
                    }
                    $courses[] = $course;
                }
            }
        }
        return array_filter($courses);
    }

    public function getUserGroups(int $user_id): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $groups = [];
        foreach (\ilObject::_getObjectsByType('grp') as $obj_grp) {
            foreach (\ilObjCourse::_getAllReferences($obj_grp['obj_id']) as $ref_id) {
                if (\ilObject::_isInTrash($ref_id)) {
                    continue;
                }
                $obj_group = new  \ilObjGroup($ref_id, true);
                if ($obj_group->getMembersObject()->isAssigned($user_id)) {
                    $group = [
                        "ref_id" => $ref_id,
                        "title" => $obj_group->getTitle(),
                    ];
                    $user_roles = $obj_group->getMembersObject()->getAssignedRoles($user_id);
                    foreach ($user_roles as $role) {
                        if ($tmp_obj = \ilObjectFactory::getInstanceByObjId($role, false)) {
                            $group['user_role'][] = $tmp_obj->untranslatedTitle;
                        }
                    }
                    $groups[] = $group;
                }
            }
        }
        return array_filter($groups);
    }

    public function getUserRoles(int $user_id): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $user_roles = [];
        foreach ($this->DIC->rbac()->review()->assignedRoles($user_id) as $role_id) {
            if (\ilObject::_isInTrash($role_id)) {
                continue;
            }
            if ($tmp_obj = \ilObjectFactory::getInstanceByObjId($role_id, false)) {
                $user_roles[$role_id]['id'] = $role_id;
                $user_roles[$role_id]['title'] = $tmp_obj->getTitle();
                if ($this->DIC->rbac()->review()->isGlobalRole($role_id)) {
                    $user_roles[$role_id]['context'] = 'global';
                } else {
                    $user_roles[$role_id]['context'] = 'local';
                    $parent_ref_id = $this->DIC->rbac()->review()->getObjectReferenceOfRole($role_id);
                    $user_roles[$role_id]['parent'] = [
                        'ref_id' => $parent_ref_id,
                        'obj_id' => \ilObject::_lookupObjId($parent_ref_id),
                        'title' => \ilObject::_lookupTitle(\ilObject::_lookupObjId($parent_ref_id)),
                        'type' => \ilObject::_lookupType(\ilObject::_lookupObjId($parent_ref_id))
                    ];
                }
            }
        }
        return array_filter($user_roles);
    }

    public function addUserRole(int $user_id, int $role_id): void
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        if (!$this->utilHandler->roleExists($user_id)) {
            throw new RoleNotFoundException();
        }
        $this->DIC->rbac()->admin()->assignUser($role_id, $user_id);
    }

    public function removeUserRole(int $user_id, int $role_id): void
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        if (!$this->utilHandler->roleExists($user_id)) {
            throw new RoleNotFoundException();
        }
        $this->DIC->rbac()->admin()->deassignUser($role_id, $user_id);
    }

    public function deleteUser(int $user_id): void
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_user = new \ilObjUser($user_id);
        $obj_user->delete();
    }

    public function getUserDefinedValues(int $user_id): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }

        $obj_user = new \ilObjUser($user_id);
        $user_fields = $obj_user->getUserDefinedData();

        $user_custom_fields = [];
        foreach (\ilUserDefinedFields::_getInstance()->getDefinitions() as $definition) {
            if (array_key_exists('f_' . $definition['field_id'], $user_fields)) {
                $user_custom_fields[$definition['field_name']] = $user_fields['f_' . $definition['field_id']];
            } else {
                $user_custom_fields[$definition['field_name']] = '';
            }
        }
        return array_filter($user_custom_fields);
    }

    public function setUserDefinedValues(int $user_id, array $custom_fields): void
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_user = new \ilObjUser($user_id);
        $user_field_definition = \ilUserDefinedFields::_getInstance()->getDefinitions();
        $user_custom_fields = [];
        $user_custom_fields_status = [];
        foreach ($custom_fields as $field_name => $field_value) {
            foreach ($user_field_definition as $definition) {
                if (strtolower($definition['field_name']) == strtolower($field_name)) {
                    $user_custom_fields[$definition['field_id']] = $field_value;
                    $user_custom_fields_status[$field_name] = true;
                    break;
                } else {
                    $user_custom_fields_status[$field_name] = false;
                }
            }
        }
        if(empty($user_custom_fields)) {
            throw new AttributesNotFoundException(['custom_fields']);
        }
        $error_field = [];
        foreach ($user_custom_fields_status as $field_name => $status) {
            if (!$status) {
                $error_field[] = $field_name;
            }
        }
        if (!empty($error_field)) {
            throw new AttributesNotFoundException($error_field);
        }
        $obj_user->setUserDefinedData($user_custom_fields);
        $obj_user->update();
    }

    public function getUserDefinedValuesByName(int $user_id, string $custom_field): array
    {
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_user = new \ilObjUser($user_id);
        $user_fields = $obj_user->getUserDefinedData();
        $field_name = $custom_field;
        $field_value = '';
        $error = true;
        foreach (\ilUserDefinedFields::_getInstance()->getDefinitions() as $definition) {
            if ($definition['field_name'] == ucfirst($field_name)) {
                $field_value = $user_fields['f_' . $definition['field_id']];
                $error = false;
                break;
            }
        }
        if ($error) {
            throw new AttributesNotFoundException(['custom_fields' => $custom_field]);
        }
        return $field_value;
    }

    public function userExists(int $user_id): bool
    {
        return $this->utilHandler->userExists($user_id);
    }

    public function createUser(string $username, string $password): array
    {
        $new_user = new \ilObjUser();
        $new_user->setTimeLimitOwner(USER_FOLDER_ID);
        $new_user->setTitle($username);
        $new_user->setDescription('');
        $new_user->setLogin($username);
        $new_user->setPasswd($password);
        $new_user->setActive(true);
        $new_user->setTimeLimitUnlimited(true);
        $new_user_id = $new_user->create();
        $new_user->saveAsNew();

        return ['user_id' => $new_user_id];
    }
}
