<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowStateRequest;
use Illuminate\Http\Request;
use App\Http\Requests\EmitStatementRequest;
use App\Http\Requests\UpsertStateRequest;
use App\LRS;
use Illuminate\Http\Response;

/**
 * @group DreamCask
 *
 * This group of APIs communicates with the learning record store.
 */
class StatementController extends Controller
{
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
     * Emit a cmi5 statement to the LRS.
     *
     * Forward a statement to the LRS.
     * <aside class="notice">The statement needs to follow the cmi5 specification.</aside>
     *
     * @group DreamCask
     * @authenticated
     *
     * @bodyParam payload string required A cmi5 statement
     *
     * @response 200 {
     *   "message": "Successfully forwarded to LRS",
     *   "status": 200
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
     * @response 503 {
     *   "error":
     *   {
     *      "message": "Service unavailable",
     *      "status": 503,
     *   }
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(EmitStatementRequest $request, LRS $lrs)
    {
        $response = $lrs->generateStatement($request->payload);

        if ($response->success) {
            return response()->json([
                'message' => "Successfully forwarded to LRS",
                'status' => Response::HTTP_CREATED
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => "Bad request",
                'details' => json_decode($response->content),
                'status' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
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
     * Get an existing activity state.
     *
     * Obtain an activity state from the LRS.
     *
     * @group DreamCask
     * @authenticated
     *
     * @urlParam state_id string State ID in the LRS
     * @urlParam activity_id string required Activity ID (typically a URL)
     * @urlParam activity_name string required Activity name
     * @urlParam email string required User email
     *
     * @response 200 {
     *   "message": "Success",
     *   "payload": "payload",
     *   "status": 200
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
     * @response 404 {
     *   "error":
     *   {
     *      "message": "Resource not found",
     *      "status": 404,
     *   }
     * }
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowStateRequest $request, LRS $lrs)
    {
        $response = $lrs->retrieveState($request->only([
                'state_id',
                'activity_id',
                'activity_name',
                'email'
            ]));

        if ($response->success) {
            if ($response->httpResponse['status'] == Response::HTTP_NOT_FOUND) {
                return response()->json([
                    'message' => 'State not found',
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => "Successfully retrieved from LRS",
                'details' => json_decode($response->content->getContent()),
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => "Bad request",
                'details' => json_decode($response->content),
                'status' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @hideFromAPIDocumentation
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update an existing activity state, or create a new one.
     *
     * Save an activity state in the LRS.
     *
     * @group DreamCask
     * @authenticated
     *
     * @bodyParam state_id string required State ID in the LRS
     * @bodyParam activity_name string required Activity name
     * @bodyParam activity_id string required Activity ID (typically a URL)
     * @bodyParam email string required User email
     * @bodyParam registration string required Registration ID
     * @bodyParam payload string The state body
     *
     * @response 200 {
     *   "message": "Successfully forwarded to LRS",
     *   "status": 200
     * }
     *
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
    public function update(UpsertStateRequest $request, LRS $lrs)
    {
        $request = json_decode($request->getContent());

        $response = $lrs->saveState($request);

        if ($response->success) {
            return response()->json([
                'message' => "Successfully forwarded to LRS",
                'status' => Response::HTTP_CREATED
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message' => "Bad request",
                'details' => json_decode($response->content),
                'status' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @hideFromAPIDocumentation
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
