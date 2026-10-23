<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('listings.index'), 'priority' => '0.9'],
            ['loc' => route('categories.index'), 'priority' => '0.8'],
        ]);

        ListingCategory::active()->get()->each(function (ListingCategory $category) use ($urls) {
            $urls->push(['loc' => route('categories.show', $category), 'priority' => $category->parent_id ? '0.6' : '0.8']);
        });

        Listing::visible()->latest('published_at')->take(5000)->get(['slug', 'updated_at'])->each(function (Listing $listing) use ($urls) {
            $urls->push([
                'loc' => route('listings.show', $listing),
                'priority' => '0.7',
                'lastmod' => $listing->updated_at->toAtomString(),
            ]);
        });

        foreach (['pages.about', 'pages.contact', 'pages.safety', 'pages.terms', 'pages.privacy'] as $page) {
            $urls->push(['loc' => route($page), 'priority' => '0.3']);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '<url>'."\n";
            $xml .= '<loc>'.e($url['loc']).'</loc>'."\n";
            if (! empty($url['lastmod'])) {
                $xml .= '<lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
            }
            $xml .= '<priority>'.e($url['priority']).'</priority>'."\n";
            $xml .= '</url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
