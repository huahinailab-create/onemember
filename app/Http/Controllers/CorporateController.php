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

    /**
     * WEBSITE-002A polish — XML sitemap for the marketing site.
     * robots.txt has advertised /sitemap.xml since RELEASE-1B; it 404'd
     * until this route existed. Built from the same named routes the site
     * links to, so a page rename can never silently desync the sitemap.
     */
    public function sitemap(): \Illuminate\Http\Response
    {
        $pages = [
            ['corporate.home',       'weekly',  '1.0'],
            ['corporate.features',   'monthly', '0.9'],
            ['corporate.industries', 'monthly', '0.9'],
            ['corporate.pricing',    'monthly', '0.9'],
            ['corporate.faq',        'monthly', '0.8'],
            ['corporate.about',      'monthly', '0.7'],
            ['corporate.contact',    'monthly', '0.7'],
            ['corporate.solutions',  'monthly', '0.6'],
            ['corporate.resources',  'monthly', '0.6'],
            ['corporate.demo',       'monthly', '0.6'],
            ['corporate.partners',   'monthly', '0.5'],
            ['corporate.careers',    'monthly', '0.5'],
            ['corporate.blog',       'monthly', '0.5'],
            ['corporate.security',   'yearly',  '0.4'],
            ['corporate.privacy',    'yearly',  '0.3'],
            ['corporate.terms',      'yearly',  '0.3'],
            ['corporate.pdpa',       'yearly',  '0.3'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as [$name, $freq, $priority]) {
            $xml .= '  <url><loc>' . e(route($name)) . '</loc>'
                . '<changefreq>' . $freq . '</changefreq>'
                . '<priority>' . $priority . '</priority></url>' . "\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
