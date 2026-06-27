<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantProfileRequest;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerchantProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $merchant = $request->user()->merchant;

        return view('merchant.profile.edit', compact('merchant'));
    }

    public function update(MerchantProfileRequest $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $data = $request->validated();

        if ($merchant) {
            $merchant->update($data);
        } else {
            $data['user_id'] = $request->user()->id;
            $data['slug']    = \Illuminate\Support\Str::slug($data['name']);
            Merchant::create($data);
        }

        return redirect()->route('merchant.profile.edit')
            ->with('success', 'Merchant profile saved successfully.');
    }
}
