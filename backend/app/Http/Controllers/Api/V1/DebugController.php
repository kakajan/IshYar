<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class DebugController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Debug works', 'success' => true]);
    }
}
