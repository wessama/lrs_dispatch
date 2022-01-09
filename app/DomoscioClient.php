<?php


namespace App;

use Illuminate\Support\Facades\Http;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Response;


class DomoscioClient
{
    private int $client_id = 1;
    private string $baseUrl;
    private string $domoscioClientId;
    private string $domoscioClientSecret;
    private string $accessToken;
    private string $refreshToken;

    /**
     * Service constructor.
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function __construct()
    {
        $this->baseUrl = env('DOMOSCIO_HUB_BASE_URL');
        $this->domoscioClientId = env('DOMOSCIO_HUB_CLIENT_ID');
        $this->domoscioClientSecret = env('DOMOSCIO_HUB_SECRET');

        $token = Token::valid($this->client_id)->get();

        if ($token->isEmpty()) {
            $response = Http::withToken($this->domoscioClientSecret)
                            ->get($this->baseUrl . '/instances/' . $this->domoscioClientId . '/students');

            $response->throw();

            $this->accessToken = $response->header('Accesstoken');
            $this->refreshToken = $response->header('Refreshtoken');

            Token::updateOrCreate([
                    'client_id' => $this->client_id
                ], [
                    'refresh_token' => $this->refreshToken,
                    'access_token' => $this->accessToken,
                    'expires_at' => Carbon::now()->addSeconds(3600)->format('Y-m-d H:i:s')
                ]);
        } else {
            $token = $token->first();

            $this->accessToken = $token->access_token;
            $this->refreshToken = $token->refresh_token;
        }
    }

    /**
     * Fetch student info
     *
     * @param $uid
     * @return object
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getStudent($uid): object
    {
        $response = Http::withHeaders([
            'Refreshtoken' => $this->refreshToken,
            'Accesstoken' => $this->accessToken
        ])->asForm()
          ->get($this->baseUrl . '/instances/' . $this->domoscioClientId . '/students/' . $uid, [
              'key_type' => 'uid'
          ]);

        if ($response->status() == Response::HTTP_NOT_FOUND) {
            return [];
        }

        $response->throw();

        return $response->object();
    }

    /**
     *
     * @param array $info
     * @return \stdClass
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createStudent(array $info): \stdClass
    {
        $response = Http::withHeaders([
            'Refreshtoken' => $this->refreshToken,
            'Accesstoken' => $this->accessToken
        ])->asForm()
          ->post($this->baseUrl . '/instances/' . $this->domoscioClientId . '/students', [
              'uid' => $info['student_uid'],
              'active' => true,
              'is_test_student' => false,
              'civil_profile_attributes' => [
                  'student_infos' => [
                      'firstname' => $info['student_firstname'],
                      'lastname'  => $info['student_lastname'],
                      'email'     => $info['student_email'],
                      'created_at' => Carbon::now()->format('Y-m-d\TH:i:s\Z')
                  ]
              ]
          ]);

        $response->throw();

        if ($response->status() == Response::HTTP_UNPROCESSABLE_ENTITY) {
            return [];
        }

        return $response->object();
    }

    /**
     *
     * @param array $eventInfo
     * @return \stdClass
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createEvent(array $eventInfo): \stdClass
    {
        $response = Http::withHeaders([
            'Refreshtoken' => $this->refreshToken,
            'Accesstoken' => $this->accessToken
        ])->asForm()
          ->post($this->baseUrl . '/instances/' . $this->domoscioClientId . '/events', [
              'student_id' => $eventInfo['student_id'],
              'content_id' => $eventInfo['content_id'],
              'payload' => $eventInfo['payload'],
              'event_type' => "EventResult"
          ]);

        $response->throw();

        if ($response->status() == Response::HTTP_UNPROCESSABLE_ENTITY) {
            return [];
        }

        return $response->object();
    }
}
