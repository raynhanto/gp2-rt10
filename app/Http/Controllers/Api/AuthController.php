<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function me(): JsonResponse
    {
        $user = Auth::user()->load('units');
        return response()->json(['success' => true, 'data' => $user]);
    }
}
