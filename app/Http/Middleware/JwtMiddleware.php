<?php

namespace App\Http\Middleware;

use App\Utils\JwtAuthUtils;
use Closure;
use Exception;
//use Illuminate\Auth\Middleware\Authenticate as BaseMiddleware;
//use JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    // Override handle method
    public function handle($request, Closure $next, ...$guards)
    {
       
        // error_log("-------------1---".$request->root());
        /* if ($this->authenticate($request, $guards) === 'authentication_failed') {
          //  error_log("----------2------".$request->url());
            
            return response()->json(['error'=>'Unauthorized'], 401);
        }  */ //else { * */

        try {
            //$user = $this->authenticate($request);
            //$user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken();
          

            // $token = JwtAuthUtils::getTokenFromHeader($request);

            //error_log('JwtMiddleware handle 1: token: '.$token);

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                $errorInf = [
                    "message" => "User Not Found",
                    "code" => 465
                ];
                return response()->json(
                    [
                        'data' => null,
                        'status' => false,
                        'error' => $errorInf
                    ],
                    $errorInf['code']
                );
            }
        } catch (UnauthorizedHttpException $e) {
            if ($e->getMessage() == 'Token not provided') {
            } else if ($e->getMessage() == 'User not found') {
            }
            //error_log('JwtMiddleware handle 1:  ' . $e->getMessage());
            $errorInf = [
                "message" => "User Not Found",
                "code" => 465
            ];
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (TokenInvalidException $e) {
          
            $errorInf = JwtAuthUtils::parseExecption($e);
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (TokenExpiredException $e) {
           
            $errorInf = JwtAuthUtils::parseExecption($e);
           
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (TokenBlacklistedException $e) {
           
            $errorInf = JwtAuthUtils::parseExecption($e);
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
        } catch (Exception $e) {
            
            $errorInf = [
                'message' => 'Authorization Token not found',
                'code' => 466
            ];
            return response()->json(
                [
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                ],
                $errorInf['code']
            );
            /* $errorInf = UtilsJwtAuthUtils::parseExecption($e);
                return response()->json([
                    'data' => null,
                    'status' => false,
                    'error' => $errorInf
                    ]
                ); */
            /* 
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response()->json([
                        'data' => null,
                        'status' => false,
                        'err_' => [
                            'message' => 'Token Invalid',
                            'code' => 1
                            ]
                        ]
                    );
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response()->json([
                        'data' => null,
                        'status' => false,
                        'err_' => [
                            'message' => 'Token Expired',
                            'code' =>1
                        ]
                    ]
                );
                }
                else{
                if( $e->getMessage() === 'User Not Found') {
                    return response()->json([
                        "data" => null,
                        "status" => false,
                        "err_" => [
                            "message" => "User Not Found",
                            "code" => 1
                            ]
                        ]
                    ); 
                }
                return response()->json([
                    'data' => null,
                    'status' => false,
                    'err_' => [
                        'message' => 'Authorization Token not found',
                        'code' =>1
                    ]
                    ]
                    );
                } */
            // }

        }
        return $next($request);
    }

    // Override authentication method
    /*  protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        return 'authentication_failed';
    }   */
}
