<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;

        $query = $merchant
            ? Member::where('merchant_id', $merchant->id)
            : Member::whereNull('id'); // no merchant yet → empty result

        if ($search = $request->input('search_name')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($search = $request->input('search_phone')) {
            $query->where('phone', 'like', '%' . $search . '%');
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        if (!in_array($sort, ['name', 'birthday'])) {
            $sort = 'name';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $members = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('members.index', compact('members', 'sort', 'direction'));
    }
}
