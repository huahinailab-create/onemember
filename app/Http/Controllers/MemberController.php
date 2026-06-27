<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $merchant = $request->user()->merchant;

        $merchant->members()->create($request->validated());

        return redirect()->route('members')->with('success', 'Member created successfully.');
    }

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
