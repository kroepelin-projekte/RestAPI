<?php

namespace KPG\RestAPI\ILIAS\Course\classes;

use KPG\RestAPI\API\Exception\UserNotFoundException;
use KPG\RestAPI\API\Exception\DefaultRoleNotFoundException;
use KPG\RestAPI\API\Exception\CourseNotFoundException;

class CourseUserHandler
{
    private $DIC;
    private CourseUtilHandler $utilHandler;

    public function __construct()
    {
        global $DIC;
        $this->DIC = $DIC;
        $this->utilHandler = new CourseUtilHandler();
    }

    public function addUser(int $user_id, int $course_ref_id, string $course_default_role): void
    {
        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_course = new \ilObjCourse($course_ref_id, true);
        switch ($course_default_role) {
            case 'admin':
                $role_id = $obj_course->getDefaultAdminRole();
                break;
            case 'tutor':
                $role_id = $obj_course->getDefaultTutorRole();
                break;
            case 'member':
                $role_id = $obj_course->getDefaultMemberRole();
                break;
            default:
                throw new DefaultRoleNotFoundException();
        }
        $this->DIC->rbac()->admin()->assignUser($role_id, $user_id);
    }

    public function addMember(int $user_id, int $course_ref_id): void
    {
        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_course = new \ilObjCourse($course_ref_id, true);
        $role_id = $obj_course->getDefaultMemberRole();
        $this->DIC->rbac()->admin()->assignUser($role_id, $user_id);
    }

    public function getAllUsers(int $course_ref_id, $default_role = null)
    {
        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        $users = [];
        if ($default_role == null) {
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjCourse($course_ref_id, true), 'member');
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjCourse($course_ref_id, true), 'tutor');
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjCourse($course_ref_id, true), 'admin');
        } else {
            $users[] = $this->utilHandler->getUserByDefaultRole(new \ilObjCourse($course_ref_id, true), $default_role);
        }

        return array_filter($users);
    }

    public function deleteUser(int $user_id, int $course_ref_id): void
    {
        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        (new \ilObjCourse($course_ref_id, true))->getMemberObject()->delete($user_id);
    }
}
