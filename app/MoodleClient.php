<?php

namespace App;

use Illuminate\Support\Facades\Http;

class MoodleClient
{
    private $wstoken;
    private $url;
    private $enrollment_wstoken;

    public function __construct()
    {
        $this->wstoken = env('MOODLE_WEB_SERVICE_TOKEN');
        $this->url = env('MOODLE_REST_BASE_URL');
        $this->enrollment_wstoken = env('MOODLE_ENROLLMENT_WEB_SERVICE_TOKEN');
    }

    public function enrollUsers($users)
    {
        $response = Http::get($this->url, [
            'wstoken'            => $this->enrollment_wstoken,
            'wsfunction'         => 'local_msdynamics_enroll_user',
            'moodlewsrestformat' => 'json',
            'users'              => $users
        ]);

        return $response;
    }

    public function unEnrollUsers($users)
    {
        $response = Http::get($this->url, [
            'wstoken'            => $this->enrollment_wstoken,
            'wsfunction'         => 'local_msdynamics_unenroll_user',
            'moodlewsrestformat' => 'json',
            'users'              => $users
        ]);

        return $response;
    }

    public function getCourseByEnrollmentId($enrollmentId)
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->wstoken,
            'wsfunction' => 'local_atos_get_course_by_enrolment_id',
            'moodlewsrestformat' => 'json',
            'enrol_id' => $enrollmentId
        ]);

        return $response;
    }

    public function getCourseCatalogTree($queryString)
    {
        $params = [
            'wstoken' => $this->enrollment_wstoken,
            'wsfunction' => 'local_msdynamics_get_catalog_tree',
            'moodlewsrestformat' => 'json'
        ];

        $url_params = http_build_query($params)."&".ltrim($queryString, $queryString[0]);

        $response = Http::get($this->url."?".$url_params);

        return $response;
    }

    public function updateActivityProgress($enrollment_id, $activity_id, $oid)
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->wstoken,
            'wsfunction' => 'local_atos_update_activity_progress',
            'moodlewsrestformat' => 'json',
            'enrollment_id' => $enrollment_id,
            'activity_id' => $activity_id,
            'oid' => $oid
        ]);

        return $response;
    }

}
