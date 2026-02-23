<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class RedirectShortUrlController extends Controller
{
    /**
     * Public redirect: resolve short code and redirect to original URL.
     */
    public function __invoke(string $shortCode): RedirectResponse
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->firstOrFail();

        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->long_url, 302);
    }
}
