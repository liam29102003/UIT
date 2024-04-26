<?php

namespace App\Http\Middleware;

use Closure;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XSSProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve input data from the request
        $input = $request->all();

        // Define configuration for HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', ''); // Allow no tags, only attributes
        $purifier = new HTMLPurifier($config);

        // Loop through each input and purify it
        foreach ($input as $key => $value) {
            $input[$key] = $purifier->purify($value);
        }

        // Replace the input data in the request
        $request->replace($input);

        return $next($request);
    }
}
