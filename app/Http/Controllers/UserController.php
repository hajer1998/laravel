<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;

    }

    /**
     * Registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = $this->repository->createUser(
            $request->get('name'),
            $request->get('email'),
            $request->get('password')
        );

        $token = $this->repository->createToken((string) $user->_id);
        return response()->json(['data' => $token]);
    }


    /**
     * Login
     */
    public function login(Request $request)
    {

        // Validations
        $rules = [
            'email'=>'required|email',
            'password'=>'required|min:8'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Validation failed
            throw new \Exception('Validation failed');
        } else {
            // Fetch User
            $user = User::where('email',$request->email)->first();
            if($user) {
                // Verify the password
                if( password_verify($request->password, $user->password) ) {
                    $token = $user->token();
                    if(!empty($token)){
                        $token = $this->repository->updateToken((string) $user->_id);
                        return response()->json([
                            'data' => $token->toArray()
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'User is not registered',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Invalid Password',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'User not found',
                ]);
            }
        }
    }




    /**
     * getUser
     */

    public function get($id)
    {
        $user = User::firstWhere('_id', $id);

        if (!$user){
            exit('User not found');
        }

        return response()->json($user->toArray());
    }


}

