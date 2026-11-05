<?php

namespace KPG\RestAPI\Components\ILIAS\Course\classes;

use KPG\RestAPI\ILIAS\Course\classes\CourseUtilHandler;
use KPG\RestAPI\API\Exception\CourseNotFoundException;
use KPG\RestAPI\API\Exception\AttributesNotFoundException;

class CourseHandler
{
    private $DIC;
    public CourseUtilHandler $utilHandler;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->utilHandler = new CourseUtilHandler();
    }

    public function getCourse(int $ref_id = null): array
    {
        if ($ref_id == null) {
            $ref_ids = [];
            foreach (\ilObject::_getObjectsByType('crs') as $obj_crs) {
                foreach (\ilObjCourse::_getAllReferences($obj_crs['obj_id']) as $ref_id) {
                    if (\ilObject::_isInTrash($ref_id)) {
                        continue;
                    }
                    $ref_ids[] = $ref_id;
                }
            }
            $courses = [];
            foreach ($ref_ids as $ref_id) {
                $courses[] = [
                    'ref_id' => $ref_id,
                    'obj_id' => \ilObjCourse::_lookupObjId($ref_id),
                    'title' => \ilObjCourse::_lookupTitle(\ilObjCourse::_lookupObjId($ref_id)),
                    'parent_id' => $this->DIC->repositoryTree()->getParentId($ref_id)
                ];
            }
            return $courses;
        } else {
            if (!$this->utilHandler->courseExists($ref_id)) {
                throw new CourseNotFoundException();
            }
            $obj_course = new \ilObjCourse($ref_id);
            $course_information['ref_id'] = $ref_id;
            $course_information['title'] = $obj_course->getTitle();
            $course_information['description'] = $obj_course->getDescription();
            $course_information['long_description'] = $obj_course->getLongDescription();
            $course_information['owner'] = [
                'user_id' => $obj_course->getOwner(),
                'name' => $obj_course->getOwnerName()
            ];
            $course_information['create_date'] = $obj_course->getCreateDate();
            $course_information['last_update'] = $obj_course->getLastUpdateDate();
            $course_information['import_id'] = $obj_course->getImportId();
            $course_information['offline'] = $obj_course->getOfflineStatus();
            $course_information['default_roles'] = [
                'default_member_role' => $obj_course->getDefaultMemberRole(),
                'default_tutor_role' => $obj_course->getDefaultTutorRole(),
                'default_admin_role' => $obj_course->getDefaultAdminRole()
            ];
            $course_information['contact'] = [
                'name' => $obj_course->getContactName(),
                'email' => $obj_course->getContactEmail(),
                'phone' => $obj_course->getContactPhone(),
                'consultation' => $obj_course->getContactConsultation(),
                'responsibility' => $obj_course->getContactResponsibility()
            ];

            $role_information = [];
            foreach ($this->DIC->rbac()->review()->getRolesOfObject($ref_id) as $role) {
                if (!$this->DIC->rbac()->review()->isGlobalRole($role)) {
                    $role_information[$role] = \ilObjectFactory::getInstanceByObjId($role, false)->getTitle();
                }
            }
            $course_information['roles'] = $role_information;

            $course_information['parent']['ref_id'] = $this->DIC->repositoryTree()->getParentId($ref_id);
            $course_information['parent']['obj_id'] = \ilObject::_lookupObjId(
                $this->DIC->repositoryTree()->getParentId($ref_id)
            );
            $course_information['parent']['title'] = \ilObject::_lookupTitle(
                \ilObject::_lookupObjId($this->DIC->repositoryTree()->getParentId($ref_id))
            );
            $course_information['parent']['type'] = \ilObject::_lookupType(
                \ilObject::_lookupObjId($this->DIC->repositoryTree()->getParentId($ref_id))
            );
            $course_information['childs'] = [];
            foreach ($this->DIC->repositoryTree()->getChilds($ref_id) as $child) {
                $course_information['childs'][] = [
                    'ref_id' => (int) $child['ref_id'],
                    'obj_id' => \ilObject::_lookupObjId($child['ref_id']),
                    'title' => $child['title'],
                    'type' => \ilObject::_lookupType(\ilObject::_lookupObjId($child['ref_id']))
                ];
            }
            return $course_information;
        }
    }

    public function updateCourse(int $ref_id, array $request_body): void
    {
        if (!$this->utilHandler->courseExists($ref_id)) {
            throw new CourseNotFoundException();
        }
        $obj_course = new \ilObjCourse($ref_id, true);
        $update_forwarded = true;
        $failed_methods = [];
        $success_methods = [];

        foreach ($request_body as $method => $value) {
            $method_name = 'set' . ucfirst($method);
            if (method_exists($obj_course, $method_name)) {
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
            $obj_course->$method($value);
        }
        $obj_course->update();
    }

    /**
     * Returns the existence, offline and trash status of a course.
     *
     * The offline status is read from the central object properties (object_data.offline),
     * which replaced the legacy crs_settings.activation_type.
     *
     * @return array|null null if no course with the given ref_id exists
     */
    public function getCourseExistsStatus(int $ref_id): ?array
    {
        if (!$this->utilHandler->courseExists($ref_id)) {
            return null;
        }

        $obj_id = \ilObject::_lookupObjId($ref_id);

        return [
            'ref_id' => $ref_id,
            'obj_id' => $obj_id,
            'title' => \ilObject::_lookupTitle($obj_id),
            'offline' => \ilObject::lookupOfflineStatus($obj_id),
            'in_trash' => \ilObject::_isInTrash($ref_id),
        ];
    }

    public function deleteCourse(int $ref_id): array
    {
        if (!$this->utilHandler->courseExists($ref_id)) {
            return [false, 'COURSE_NOT_FOUND', []];
        }
        $obj_course = new \ilObjCourse($ref_id, true);
        $obj_course->delete();
        return [true];
    }

    public function getCourseInformation(int $ref_id, string $property): string
    {
        if (!$this->utilHandler->courseExists($ref_id)) {
            throw new CourseNotFoundException();
        }
        $obj_course = new \ilObjCourse($ref_id, true);
        $obj_methode = "get" . ucfirst($property);
        if (!method_exists($obj_course, $obj_methode)) {
            throw new AttributesNotFoundException([$property]);
        }
        $property_value = $obj_course->$obj_methode();
        return $property_value;
    }

    public function getCourseRoles(int $course_ref_id): array
    {
        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        global $DIC;
        $roles = [];
        foreach ($DIC->rbac()->review()->getRolesOfObject($course_ref_id) as $role) {
            if (!$DIC->rbac()->review()->isGlobalRole($role)) {
                $roles[$role] = \ilObjectFactory::getInstanceByObjId($role, false)->getTitle();
            }
        }
        return $roles;
    }
}
