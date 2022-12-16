<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\ApiResource\AuthenticResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\EarnPoint;
use App\Models\GenerateUrl;
class AuthController extends Controller
{   
     function __construct(AuthenticResource $authRepository){
        $this->authResource = $authRepository;
    }

    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid! User email or password is invalid.','status'=>401], 401);
        }

        return $this->respondWithToken($token);
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
     
    
   public function register(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            $plainErrorText = "";
            $errorMessage = json_decode($validator->messages(), True);
            foreach ($errorMessage as $value) {
                $plainErrorText .= $value[0] . ". ";
            }
            return [
                'status' => 402,
                'message' => 'Validation failed',
                'errorMsg' => $plainErrorText
            ];
        }


        return $this->authResource->getRegistered($request);
       
        

    }
    
    public function userInfo()
    {
        $totalRegisteredUser = GenerateUrl::where('parent_user_id', auth()->user()->id)->get();
        $getShareLink = GenerateUrl::where('user_id',auth()->user()->id)->first();
        $convertToArr = json_decode(json_encode($totalRegisteredUser),true);
        $totalEarnPoint = EarnPoint::where('user_id',auth()->user()->id)->sum('points');
        return response()->json([
            'status' => 200,
            'message' => 'Data Found',
            'user' => [
                'name' => auth()->user()->name,
                'email' => is_null(auth()->user()->email)?'':auth()->user()->email,
                'total_registered_user_you_own' => $convertToArr,
                'total_earning_point' => $totalEarnPoint,
                'register_code' => $getShareLink->url_code,
                'register_url' => $getShareLink->register_url
            ]
            
        ]);
    }
    public function userList()
    {
        return $this->authResource->getUserPaginate();
    }
    
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Successfully Loggedin',

            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24,
            'user' => [
                'user_name' => auth()->user()->name,
                'user_email' => is_null(auth()->user()->email)?'':auth()->user()->email,
                'user_phone' => auth()->user()->phone_number,
                'user_address' => is_null(auth()->user()->address)?'':auth()->user()->address,
            ]
            
        ]);
    }
}
