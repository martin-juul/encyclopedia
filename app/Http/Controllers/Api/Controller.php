<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function badRequest(string $message = 'Bad request'): JsonResponse
    {
        return $this->error($message, Response::HTTP_BAD_REQUEST);
    }

    protected function conflict(string $message = 'Conflict'): JsonResponse
    {
        return $this->error($message, Response::HTTP_CONFLICT);
    }

    protected function internalServerError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function notImplemented(string $message = 'Not implemented')
    {
        return $this->error($message, Response::HTTP_NOT_IMPLEMENTED);
    }

    protected function error(string $message, int $status): JsonResponse
    {
        return $this->json([
            'message' => $message,
        ], $status);
    }

    /**
     * @param string|array|\JsonSerializable|null $data
     * @param int $status
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function json($data = null, int $status = 200, array $headers = []): JsonResponse
    {
        return response()->json($data, $status, $headers, config('app.encoding.json'));
    }
}
