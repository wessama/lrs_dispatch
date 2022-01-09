<?php

namespace App;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use TinCan\Activity;
use TinCan\Agent;
use TinCan\Context;
use TinCan\ContextActivities;
use TinCan\RemoteLRS;
use TinCan\Statement;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class LRS
{
    public RemoteLRS $connection;
    public $lrs_version;
    protected $lrs_base_url;
    protected $lrs_key;
    protected $lrs_secret;
    private $moodle_client;

    public function __construct()
    {
        $this->lrs_base_url = env('LEARNING_LOCKER_BASE_URL');
        $this->lrs_version = env('LEARNING_LOCKER_VERSION');
        $this->lrs_key = env('LEARNING_LOCKER_KEY');
        $this->lrs_secret = env('LEARNING_LOCKER_SECRET');
        $this->moodle_client = new MoodleClient();

        $this->connection = new RemoteLRS(
            $this->lrs_base_url,
            $this->lrs_version,
            $this->lrs_key,
            $this->lrs_secret
        );
    }

    public function generateStatement($statement): \TinCan\LRSResponse
    {
        $moodle_base_url = env('MOODLE_BASE_URL');

        if (!isset($statement['context']['extensions'][$moodle_base_url]['enrollmentId'])
            || !isset($statement['context']['extensions'][$moodle_base_url]['activityId'])
            || !isset($statement['context']['extensions'][$moodle_base_url]['oid'])) {
            throw ValidationException::withMessages([
                'enrollmentId' => 'Enrollment ID must be provided',
                'activityId' => 'Activity ID must be provided',
                'oid' => 'User OID must be provided'
            ]);
        }

        $enrollmentId = $statement['context']['extensions'][$moodle_base_url]['enrollmentId'];
        $activityId = $statement['context']['extensions'][$moodle_base_url]['activityId'];
        $oid = $statement['context']['extensions'][$moodle_base_url]['oid'];

        $moodle_response = $this->moodle_client->getCourseByEnrollmentId($enrollmentId);

        if ($moodle_response->successful()) {
            $response = $moodle_response->object();

            if (isset($response->exception)) {
                throw new HttpResponseException(response()->json([
                    'message' => $response->message,
                    'status' => Response::HTTP_BAD_REQUEST
                ], Response::HTTP_BAD_REQUEST));
            }

            $contextActivitiesParent = [
                'id' => $response->id,
                'definition' => [
                    'type' => "http://adlnet.gov/expapi/activities/course",
                    'name' => [
                        'en-US' => $response->name
                    ]
                ],
                'objectType' => 'Activity'
            ];

            $context = new Context();
            $context->setExtensions($statement['context']['extensions']);

            $contextActivities = new ContextActivities();
            $contextActivities->setParent($contextActivitiesParent);

            $context->setContextActivities($contextActivities);

            $tinCanStatement = new Statement();

            $tinCanStatement->setObject($statement['object']);
            $tinCanStatement->setActor($statement['actor']);
            $tinCanStatement->setVerb($statement['verb']);
            $tinCanStatement->setTimestamp($statement['timestamp']);
            $tinCanStatement->setContext($context);

            $verbString = $tinCanStatement->getVerb()->getDisplay()->getNegotiatedLanguageString();
            if ($verbString == "completed") {
                $this->moodle_client->updateActivityProgress(
                    $enrollmentId,
                    $activityId,
                    $oid
                );
            }

            return $this->connection->saveStatement($tinCanStatement);
        }

        return $moodle_response->throw()->json();
    }

    public function saveState($data): \TinCan\LRSResponse
    {
        $mbox = 'mailto:' . $data->email;

        $activity = new Activity();
        $activity->setId($data->activity_id);
        $activity->setDefinition([
                'name' => [
                    'en-US' => $data->activity_name,
                ]
            ]
        );

        $agent = new Agent(
            ['mbox' => $mbox]
        );

        return $this->connection->saveState(
            $activity,
            $agent,
            $data->state_id,
            json_encode($data->payload)
        );
    }

    public function retrieveState($data): \TinCan\LRSResponse
    {
        $mbox = 'mailto:' . $data['email'];

        $activity = new Activity();
        $activity->setId($data['activity_id']);
        $activity->setDefinition([
                'name' => [
                    'en-US' => $data['activity_name'],
                ]
            ]
        );

        $agent = new Agent(
            ['mbox' => $mbox]
        );

        return $this->connection->retrieveState(
            $activity,
            $agent,
            $data['state_id']
        );
    }
}
