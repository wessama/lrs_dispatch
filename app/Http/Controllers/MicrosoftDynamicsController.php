<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollUserRequest;
use App\Http\Requests\ShowCatalogTreeRequest;
use App\Http\Requests\UnenrollUserRequest;
use App\MoodleClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Microsoft Dynamics
 *
 * This group of APIs communicates with Microsoft Dynamics APIs.
 */
class MicrosoftDynamicsController extends Controller
{
    private $moodle_client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->moodle_client = new MoodleClient();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show CLMS catalog tree.
     *
     * Show the catalog hierarchy.
     *
     * @group Microsoft Dynamics
     * @authenticated
     * @header Content-Type application/json
     *
     * @urlParam fields[] array required Array of custom fields to retrieve
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data" : [
     *          {
     *              "id": "id",
     *              "type": "type",
     *              "duration": "duration_in_seconds",
     *              "children": "1",
     *              "custom_fields": [
     *                      "field1",
     *                      "field2"
     *              ],
     *              "container": [
     *                      "id": "id",
     *                      "type": "type",
     *                      "children": "0"
     *              ]
     *          }
     *    ]
     * }
     *
     * @response 400 {
     *   "error":
     *   {
     *      "message": "Bad request",
     *      "status": 400,
     *   }
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowCatalogTreeRequest $request)
    {
        $queryString = str_replace($request->url(), '',$request->fullUrl());
        $moodle_response = $this->moodle_client->getCourseCatalogTree($queryString);

        if ($moodle_response->successful()) {
            return response()->json([
                'message' => "Success",
                'status' => Response::HTTP_OK,
                'data' => $moodle_response->json()
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => "Bad request",
            'status' => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Enroll users to a CLMS course.
     *
     * Enroll existing users to a CLMS course using their OID.
     *
     * @group Microsoft Dynamics
     * @authenticated
     * @header Content-Type application/json
     *
     * @bodyParam users object[] required List of users
     * @bodyParam users[].username string required Username
     * @bodyParam users[].oid string required User OID
     * @bodyParam users[].apn string required User e-mail
     * @bodyParam users[].catalog integer required Catalog ID to enroll the user in
     * @bodyParam users[].access_duration integer required Access duration in days
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data" : [
     *          {
     *              "username": "username",
     *              "oid": "oid",
     *              "catalog": "catalog",
     *              "access_duration": "access_duration",
     *              "status": "status message"
     *          }
     *    ]
     * }
     *
     * @response 400 {
     *   "error":
     *   {
     *      "message": "Bad request",
     *      "status": 400,
     *   }
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
     *      "message": "Unprocessable entity",
     *      "status": 422,
     *   }
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EnrollUserRequest $request)
    {
        $moodle_response = $this->moodle_client->enrollUsers($request->users);

        if ($moodle_response->successful()) {
            return response()->json([
                'message' => "Success",
                'status' => Response::HTTP_OK,
                'data' => $moodle_response->json()
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => "Bad request",
            'status' => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Un-enroll users from a CLMS course.
     *
     * Remove users' enrollment from a CLMS course.
     *
     * @group Microsoft Dynamics
     * @authenticated
     * @header Content-Type application/json
     *
     * @bodyParam users object[] required List of users
     * @bodyParam users[].username string required Username
     * @bodyParam users[].oid string required User OID
     * @bodyParam users[].catalog string required Catalog ID to enroll the user in
     *
     * @response 200 {
     *   "message": "Success",
     *   "status": 200,
     *   "data" : [
     *          {
     *              "username": "username",
     *              "oid": "oid",
     *              "catalog": "catalog",
     *              "status": "status message"
     *          }
     *    ]
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
     *      "message": "Unprocessable entity",
     *      "status": 422,
     *   }
     * }
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UnenrollUserRequest $request)
    {
        $moodle_response = $this->moodle_client->unEnrollUsers($request->users);

        if ($moodle_response->successful()) {
            return response()->json([
                'message' => "Success",
                'status' => Response::HTTP_OK,
                'data' => $moodle_response->json()
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => "Bad request",
            'status' => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }
}
