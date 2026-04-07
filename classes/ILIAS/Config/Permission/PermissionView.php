<?php

namespace KPG\RestAPI\ILIAS\Config\Permission;

use KPG\RestAPI\ILIAS\Util\Language;
use KPG\RestAPI\ILIAS\Config\Constant\LangConstant;
use KPG\RestAPI\ILIAS\Util\Roles;
use KPG\RestAPI\ILIAS\Config\Constant\CMDConstant;

class PermissionView implements LangConstant, CMDConstant
{
    use Language;
    use Roles;

    private $ui;
    private $DIC;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->ui = $DIC->ui()->factory();
    }

    public function initFormAPIPermission()
    {

        $model = new PermissionModel();
        $this->DIC->ui()->mainTemplate()->addCss('Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/css/custom.css');

        $section_elements = [];

        foreach ($this->getAllGlobalRoles() as $role_id => $role_title) {
            $role_radio_buttons = $this->ui->input()->field()->radio($role_title, '')->withAdditionalOnLoadCode(
                fn($id) => <<<JS
                (function() {   
                  const el = document.getElementById('$id');
                    el.querySelectorAll(':scope > *').forEach(child => {
                      child.classList.add('events_radiobuttons');
                    });
                })();
                JS
            )->withOption(0, self::getLang(self::LANG_API_PERMISSION_RADIO_BUTTON_NO_PERMISSION))
                                           ->withOption(1, self::getLang(self::LANG_API_PERMISSION_RADIO_BUTTON_CUSTOM))
                                           ->withOption(
                                               2,
                                               self::getLang(self::LANG_API_PERMISSION_RADIO_BUTTON_FULL_PERMISSION)
                                           )
                                           ->withValue($model->getRolePermissionByRoleID($role_id));

            $section_elements[$role_id] = $role_radio_buttons;
        }
        $role_section = $this->ui->input()->field()->section(
            $section_elements,
            self::getLang(self::LANG_PERMISSION_ROLE_SECTION),
            self::getLang(self::LANG_PERMISSION_ROLE_SECTION_BYLINE)
        );

        $form_action = $this->DIC->ctrl()->getLinkTargetByClass(
            \ilRestAPIConfigGUI::class,
            self::CMD_SAVE_PERMISSION
        );
        return $this->ui->input()->container()->form()->standard(
            $form_action,
            ['section_roles' => $role_section]
        );
    }

    public function initFormRolePermission()
    {
        $model = new PermissionModel();
        $components = $model->getAllComponentInformations();
        $custom_roles = $model->getCustomPermissionRoles();

        $this->DIC->ui()->mainTemplate()->addCss('Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/css/custom.css');
        $sections = [];
        foreach ($components as $name => $component) {
            $elements = [];
            foreach ($custom_roles as $role) {
                $value_permission = $model->getCustomPermissionByComponentNameAndRoleID($name, $role['id']);
                if (array_key_exists(0, $value_permission) and $value_permission[0] == "") {
                    $value_permission = [];
                }
                $options = ['GET' => 'GET', 'POST' => 'POST', 'PUT' => 'PUT', 'PATCH' => 'PATCH', 'DELETE' => 'DELETE'];
                if ($name === 'User') {
                    $options['GET_EXISTS'] = self::getLang(self::LANG_USER_EXISTS_ONLY);
                }
                $multi_select = $this->ui->input()->field()->multiSelect($role['title'], $options)->withAdditionalOnLoadCode(
                    fn($id) => <<<JS
                (function() {
                    const el = document.getElementById('$id');
                    el.classList.add('events_multiselect');
                })();
                JS
                )->withValue($value_permission);

                $elements[$role['id']] = $multi_select;
            }

            $section = $this->ui->input()->field()->section(
                $elements,
                $name
            );
            $sections[$name] = $section;
        }
        $form_action = $this->DIC->ctrl()->getLinkTargetByClass(
            \ilRestAPIConfigGUI::class,
            self::CMD_SAVE_ROLE_PERMISSION
        );
        return $this->ui->input()->container()->form()->standard(
            $form_action,
            $sections
        );
    }
}
