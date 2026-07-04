<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CorporateController extends Controller
{
    public function home(): View
    {
        return view('corporate.home');
    }

    public function solutions(): View
    {
        return view('corporate.solutions');
    }

    public function industries(): View
    {
        return view('corporate.industries');
    }

    public function features(): View
    {
        return view('corporate.features');
    }

    public function pricing(): View
    {
        return view('corporate.pricing');
    }

    public function about(): View
    {
        return view('corporate.about');
    }

    public function security(): View
    {
        return view('corporate.security');
    }

    public function contact(): View
    {
        return view('corporate.contact');
    }

    public function faq(): View
    {
        return view('corporate.faq');
    }

    public function resources(): View
    {
        return view('corporate.resources');
    }

    public function blog(): View
    {
        return view('corporate.blog');
    }

    public function careers(): View
    {
        return view('corporate.careers');
    }

    public function partners(): View
    {
        return view('corporate.partners');
    }

    public function demo(): View
    {
        return view('corporate.demo');
    }

    public function privacy(): View
    {
        return view('corporate.privacy');
    }

    public function terms(): View
    {
        return view('corporate.terms');
    }

    public function pdpa(): View
    {
        return view('corporate.pdpa');
    }
}
