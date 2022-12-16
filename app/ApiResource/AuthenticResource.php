<?php
namespace App\ApiResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\EarnPoint;
use App\Models\GenerateUrl;
use DB;
use Illuminate\Support\Str;
class AuthenticResource{

	public function getRegistered($requestData){
		try {

            $user = new User;
            $user->name = $requestData->input('name');
            $user->email = $requestData->input('email');
            $plainPassword = $requestData->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->user_role = 'General User';
            $user->save();

            // Generate User unique url;
            // $referCode = $request->url_code;
            $referCode = "qvSfCPtJZmOsV9kX";
            $urlMake = array();
            $findParentUser = GenerateUrl::where('url_code', $referCode)->first();
            if(!empty($findParentUser)){
                $parentId = $findParentUser->user_id;
                // points add to the user;
                $userPoint = EarnPoint::where('user_id', $parentId)->first();
                if(!empty($userPoint)){
                    $update['points'] = $userPoint['points'] + 15;
                    $userPoint->update($update);
                }else{
                    $points['points'] = 15;
                    $points['user_id'] = $parentId;
                    EarnPoint::create($points);
                }
            }else{
                $parentId = NULL;
            }
            $urlMake['user_id'] = $user->id; 
            $urlMake['url_code'] = Str::random(16);
            $urlMake['register_url'] = url().'?ref='.$urlMake['url_code'];
            $urlMake['parent_user_id'] = $parentId;

            $generate = GenerateUrl::create($urlMake);
            //return successful response
            return response()->json([
                'status' => 200,
                'message' => 'User Registration Successfull',
                'user' => [
                    'user_name' => $user->name,
                    'user_email' => is_null($user->email)?'':$user->email,
                    'register_code' => $generate->url_code,
                    'register_shareable_url' => $generate->register_url,
                ] 
            ], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json([
                'status' => 409,
                'message' => 'User Registration Failed!',
                'errorMsg' => $e->getMessage()
            ], 409);
        }
	}

    public function getUserPaginate(){
        $userList = User::orderBy('name','asc')->paginate(3);

        try {
           if(!empty($userList)){
               return response()->json([
                    'status' => 200,
                    'message' => 'User Found',
                    'data' => $userList
                ], 200); 
            }else{
                return response()->json([
                    'status' => 200,
                    'message' => 'No data found!',
                    'data' => ''
                ], 200);
            } 
        } catch (Exception $e) {
            return response()->json([
                'status' => 409,
                'message' => $e->getMessage()
            ], 409);
        }
        
    }
}