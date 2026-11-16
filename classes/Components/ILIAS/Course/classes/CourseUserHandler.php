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
                $participant_role = \ilParticipants::IL_CRS_ADMIN;
                break;
            case 'tutor':
                $role_id = $obj_course->getDefaultTutorRole();
                $participant_role = \ilParticipants::IL_CRS_TUTOR;
                break;
            case 'member':
                $role_id = $obj_course->getDefaultMemberRole();
                $participant_role = \ilParticipants::IL_CRS_MEMBER;
                break;
            default:
                throw new DefaultRoleNotFoundException();
        }
        $this->addParticipant($obj_course, $user_id, $participant_role, $role_id);
    }

    public function addMember(int|string $user_identifier, int $course_ref_id): void
    {
        if (is_numeric($user_identifier)) {
            $user_id = (int) $user_identifier;
        } else {
            $user_id = \ilObjUser::_lookupId((string) $user_identifier);
        }

        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        $obj_course = new \ilObjCourse($course_ref_id, true);
        $this->addParticipant(
            $obj_course,
            $user_id,
            \ilParticipants::IL_CRS_MEMBER,
            $obj_course->getDefaultMemberRole()
        );
    }

    private function addParticipant(\ilObjCourse $obj_course, int $user_id, int $participant_role, int $role_id): void
    {
        $participants = $obj_course->getMembersObject();

        if ($participants->isAssigned($user_id)) {
            $this->DIC->rbac()->admin()->assignUser($role_id, $user_id);
            return;
        }

        $participants->add($user_id, $participant_role);
        $participants->sendNotification(
            \ilCourseMembershipMailNotification::TYPE_ADMISSION_MEMBER,
            $user_id
        );
        $obj_course->checkLPStatusSync($user_id);
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

    public function deleteUser(string|int $user_id_or_username, int $course_ref_id): void
    {
        if (is_numeric($user_id_or_username)) {
            $user_id = (int) $user_id_or_username;
        } else {
            $user_id = \ilObjUser::_lookupId((string) $user_id_or_username);
        }

        if (!$this->utilHandler->courseExists($course_ref_id)) {
            throw new CourseNotFoundException();
        }
        if (!$this->utilHandler->userExists($user_id)) {
            throw new UserNotFoundException();
        }
        (new \ilObjCourse($course_ref_id, true))->getMemberObject()->delete($user_id);
    }
}
