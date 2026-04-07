<?php

namespace KPG\RestAPI\ILIAS\Config\Permission;

use ilObjRole;
use KPG\RestAPI\ILIAS\Database\Tables\APIPermissionTable;
use DirectoryIterator;
use KPG\RestAPI\ILIAS\Database\Tables\RolesPermissionTable;

class PermissionModel
{
    private $DIC;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
    }

    public function saveAPIPermission($form): bool
    {
        $form = $form->withRequest($this->DIC->http()->request());
        $result = $form->getData();
        if ($result === null) {
            return false;
        }

        foreach ($result['section_roles'] as $key => $value) {
            if ($value === "") {
                $value = 0;
            }
            if ($value != 1) {
                (new RolesPermissionTable())->deletePermissionByRoleID((int) $key);
            }
            if (!(new APIPermissionTable())->insertOrUpdatePermission((int) $key, (int) $value)) {
                return false;
            }
        }
        return true;
    }

    public function getRolePermissionByRoleID(int $role_id)
    {
        $api_permission_table = new APIPermissionTable();
        return $api_permission_table->getPermissionByRoleID($role_id);
    }

    public function getAllComponentInformations()
    {
        $baseDir = __DIR__ . '/../../../Components';
        $components = [];
        foreach (new DirectoryIterator($baseDir) as $folder) {
            if ($folder->isDot() || !$folder->isDir()) {
                continue;
            }
            $folderName = $folder->getFilename();
            foreach (new DirectoryIterator($folder->getPathname()) as $dir) {
                if ($dir->isDot() || !$dir->isDir()) {
                    continue;
                }
                $componentName = $dir->getFilename();
                $structureFolder = "{$dir->getPathname()}/structure";
                if (!is_dir($structureFolder)) {
                    continue;
                }
                $components[$componentName]['namespace'] = $folderName;
            }
        }
        return $components;
    }

    public function getCustomPermissionRoles()
    {
        $api_permission_table = new APIPermissionTable();
        $result_roles = $api_permission_table->getCustomRoles();
        $roles = [];
        foreach ($result_roles as $role) {
            $roles[] = [
                'id' => $role['role_id'],
                'title' => ilObjRole::_lookupTitle($role['role_id'])
            ];
        }
        return $roles;
    }

    public function saveRolePermission(\ILIAS\UI\Component\Input\Container\Form\Standard $form): bool
    {
        $form = $form->withRequest($this->DIC->http()->request());
        $result = $form->getData();

        if ($result == null) {
            return false;
        }

        $roles_permission_table = new RolesPermissionTable();
        foreach ($result as $name => $roles) {
            foreach ($roles as $role_id => $role_data) {
                if ($role_data === null) {
                    $roles_permission_table->deletePermission($name, $role_id);
                    continue;
                }

                $permissions = $role_data ?? [];
                $permission_string = implode(',', $permissions);

                if (!$roles_permission_table->insertOrUpdatePermission($name, $role_id, $permission_string)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getCustomPermissionByComponentNameAndRoleID(string $component_name, int $role_id): array
    {
        $roles_permission_table = new RolesPermissionTable();
        return explode(",", $roles_permission_table->getPermissionByComponentNameAndRoleID($component_name, $role_id));
    }
    public function checkFullAccessByRoleID(int $role_id): bool
    {
        $tbl_permission = new APIPermissionTable();
        $result = $tbl_permission->getPermissionByRoleID($role_id);
        return $result == 2;
    }
}
