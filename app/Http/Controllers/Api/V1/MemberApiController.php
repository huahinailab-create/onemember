<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

/**
 * PLATFORM-002 Part 5 — v1 members endpoint (read-only reference
 * implementation of the API conventions: auth, abilities, pagination,
 * resources, error envelope).
 */
class MemberApiController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->attributes->get('api_merchant');

        $members = Member::where('merchant_id', $merchant->id)
            ->orderBy('id')
            ->paginate(min((int) $request->query('per_page', 25), 100));

        return MemberResource::collection($members);
    }

    public function show(Request $request, int $id)
    {
        $merchant = $request->attributes->get('api_merchant');

        $member = Member::where('merchant_id', $merchant->id)->find($id);

        if (! $member) {
            return response()->json([
                'error' => ['code' => 'not_found', 'message' => 'Member not found.'],
            ], 404);
        }

        return new MemberResource($member);
    }
}
