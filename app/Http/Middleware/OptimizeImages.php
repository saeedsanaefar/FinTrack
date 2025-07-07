<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeImages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add image optimization headers
        if ($this->isImageRequest($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        return $response;
    }

    private function isImageRequest(Request $request): bool
    {
        $path = $request->path();
        return preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $path);
    }
}
