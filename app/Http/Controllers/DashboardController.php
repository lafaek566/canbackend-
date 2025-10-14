<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            Log::warning('No token received in Authorization header.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('Token received:', ['token' => $token]);

        // Validate the token (this is a placeholder, implement your actual token validation logic)
        if ($token !== 'expected_token_value') {
            Log::warning('Invalid token received.', ['token' => $token]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Stats retrieved successfully',
            'data' => [
                'totalEvents' => 10,
                'activeJudges' => 5,
                'participants' => 50,
            ],
        ]);
    }
}