<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class Authenticate
{
    private $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }


    /**
     * @param Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ]);
        }
//decode l token eli jey m request ( ahna nab3thou l authorization token f request dnc hedha yjib token m request yaamlou decode

        $token = $this->jwt::decode($token, env('JWT_SECRET'), ['HS256']);
// baad yekhou l user_id eli f token yhotou f request ; request->merge hedhi taaml attribute jdid l request eli hya user_id l key w
// w lvalue mte3ha l user_id el f wost token baad ma3mnlou decode
        $request->merge(['user_id' => $token->user_id]);

        return $next($request);

    }
}
