<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DomoscioClient;
use TinCan\Verb;
use TinCan\Agent;
use TinCan\Activity;
use App\Http\Requests\StoreEventRequest;
use App\LRS;

/**
 * @group Domoscio Hub
 *
 * This group of APIs communicates with Domoscio Hub.
 */
class EventController extends Controller
{
    private DomoscioClient $domoscioClient;

    /**
     * Controller constructor.
     *
     * @param DomoscioClient $domoscioClient
     */
    public function __construct(DomoscioClient $domoscioClient)
    {
        $this->domoscioClient = $domoscioClient;
    }

    /**
     * Display a listing of the resource.
     * @hideFromAPIDocumentation
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     * @hideFromAPIDocumentation
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Create an event in Domoscio Hub.
     *
     * Obtain information for completed resource from CLMS and create an EventResult event in Domoscio Hub
     * for the current student.
     * <aside class="notice">This endpoint will automatically create a Domoscio Hub student using the given credentials if one has not already been created.</aside>
     *
     * @group Domoscio Hub
     * @authenticated
     *
     * @bodyParam student_uid string required Student UID in Domoscio Hub
     * @bodyParam student_email string required Student e-mail in CLMS
     * @bodyParam student_firstname string Student first name
     * @bodyParam student_lastname string Student last name
     * @bodyParam activity string required Activity name as stored in the LRS
     * @bodyParam activity_url string required Activity URL as stored in the LRS
     * @bodyParam domoscio_content_uid string required Content UID in Domoscio Hub
     *
     * @response 201 {
     *   "data": {
     *       "id": 677389,
     *       "event_type": "EventResult",
     *       "payload": "payload",
     *       "created_at": "2021-08-05T18:49:32.531Z",
     *       "updated_at": "2021-08-05T18:49:32.531Z",
     *       "student_id": 27425,
     *       "content_id": 20241,
     *       "feedback_error_id": null,
     *       "generated_at": "2021-08-05T18:49:32.531Z",
     *       "return": false,
     *       "standard": null,
     *       "time_spent": null,
     *       "status": 201
     *    }
     * }
     *
     * @response 401 {
     *   "error":
     *   {
     *      "message": "Unauthorized",
     *      "status": 401,
     *   }
     * }
     *
     * @response 422 {
     *   "error":
     *   {
     *      "message": "The given data was invalid",
     *      "status": 422,
     *      "details": {
     *         "form-data-key": "error message"
     *      }
     *   }
     * }
     *
     * @response 400 {
     *   "error":
     *   {
     *      "message": "Bad request",
     *      "details": "details",
     *      "status": 400,
     *   }
     * }
     *
     * @response 404 {
     *   "error":
     *   {
     *      "message": "No statements found for this actor",
     *      "status": 404,
     *   }
     * }
     *
     * @response 503 {
     *   "error":
     *   {
     *      "message": "Service unavailable",
     *      "status": 503,
     *   }
     * }
     *
     * @param StoreEventRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function store(StoreEventRequest $request)
    {
        $student = $this->domoscioClient->getStudent($request->student_uid);

        $lrs = new LRS();

        if (empty($student)) {
            $student = $this->domoscioClient->createStudent($request->only([
                'student_uid',
                'student_email',
                'student_firstname',
                'student_lastname'])
            );
        } else {
            $student = $student[0];
        }

        $mbox = 'mailto:' . $request->student_email;

        $verb = new Verb(
            ['id' => 'http://adlnet.gov/expapi/verbs/completed']
        );

        $activity = new Activity();
        $activity->setId($request->activity_url);
        $activity->setDefinition([
                'name' => [
                    'en-US' => $request->activity,
                ]
            ]
        );

        $agent = new Agent(
            ['mbox' => $mbox]
        );

        $response = $lrs->connection->queryStatements(
            [
                'verb' => $verb,
                'agent' => $agent,
                'activity' => $activity,
            ]
        );

        if ($response->success) {
            $statements = $response->content->getStatements();

            if (!empty($statements)) {
                $payload = $statements[0];

                $eventInfo['student_id'] = $student->id;
                $eventInfo['content_id'] = $request->domoscio_content_id;
                $eventInfo['payload'] = json_encode($payload->asVersion($lrs->lrs_version));

                $domoscioResponse = $this->domoscioClient->createEvent($eventInfo);

                if (!empty($domoscioResponse)) {
                    $domoscioResponse->status = Response::HTTP_CREATED;
                }

                return response()->json([
                    'data' => $domoscioResponse
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'error' => [
                        'message' => "No statements found for this actor",
                        'status' => Response::HTTP_NOT_FOUND
                    ]
                ], Response::HTTP_NOT_FOUND);
            }
        } else {
            return response()->json([
                'error' => [
                    'message' => "Bad request",
                    'details' => $response->content,
                    'status' => Response::HTTP_BAD_REQUEST
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Display the specified resource.
     * @hideFromAPIDocumentation
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @hideFromAPIDocumentation
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @hideFromAPIDocumentation
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @hideFromAPIDocumentation
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
