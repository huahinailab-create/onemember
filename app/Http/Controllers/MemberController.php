<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function show(Request $request, Member $member)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);

        return view('members.show', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($member->trashed(), 403);

        $member->update($request->validated());

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member updated successfully.');
    }

    public function archive(Request $request, Member $member)
    {
        abort_unless($member->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($member->trashed(), 409);

        $member->delete();

        return redirect()->route('members')->with('success', 'Member archived successfully.');
    }

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
        $filter   = $request->input('filter', 'active');

        if (!in_array($filter, ['active', 'archived', 'all'])) {
            $filter = 'active';
        }

        if (!$merchant) {
            $query = Member::whereNull('id');
        } elseif ($filter === 'archived') {
            $query = Member::onlyTrashed()->where('merchant_id', $merchant->id);
        } elseif ($filter === 'all') {
            $query = Member::withTrashed()->where('merchant_id', $merchant->id);
        } else {
            $query = Member::where('merchant_id', $merchant->id);
        }

        if ($search = $request->input('search_name')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($search = $request->input('search_phone')) {
            $query->where('phone', 'like', '%' . $search . '%');
        }

        $sort      = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        if (!in_array($sort, ['name', 'birthday'])) {
            $sort = 'name';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $members = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('members.index', compact('members', 'sort', 'direction', 'filter'));
    }
}
