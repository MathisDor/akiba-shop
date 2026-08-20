<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('catalog.index'), 'priority' => '0.9'],
            ['loc' => route('catalog.gender', 'femme'), 'priority' => '0.8'],
            ['loc' => route('catalog.gender', 'homme'), 'priority' => '0.8'],
        ]);

        Category::where('is_active', true)->get()->each(function (Category $category) use ($urls) {
            $urls->push(['loc' => route('catalog.category', [$category->gender, $category->slug]), 'priority' => '0.7']);
        });

        Product::where('is_active', true)->get()->each(function (Product $product) use ($urls) {
            $urls->push([
                'loc' => route('products.show', $product),
                'priority' => '0.6',
                'lastmod' => $product->updated_at->toAtomString(),
            ]);
        });

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
