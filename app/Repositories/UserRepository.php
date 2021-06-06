<?php


namespace App\Repositories;

use App\Models\Token;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * @var JWT
     */
    private $jwt;


    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;


    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws \Exception
     */
    public function getUser(string $email, string $password)
    {
        $user = User::firstWhere('email', $email);

        if (!$user) {
            throw new \Exception('user not found');
        }

        if (!\Hash::check($password, $user->password)) {
            throw new \Exception('incorrect password');
        }

        return $user;
    }

    public function createUser(string $name, string $email, string $password)
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    public function createToken(string $userId)
    {
        $key = env('JWT_SECRET');
        $payload = array(
            "iss" => env('APP_URL'),
            "aud" => env('APP_URL'),
            "iat" => time(),
            "user_id" => $userId
        );

        return Token::create([
            'user_id' => $userId,
            'token' => $this->jwt::encode($payload, $key)
        ]);

    }

    public function updateToken(string $userId)
    {
        $key = env('JWT_SECRET');
        $payload = array(
            "iss" => env('APP_URL'),
            "aud" => env('APP_URL'),
            "iat" => time(),
            "user_id" => $userId
        );

        $token = Token::firstWhere('user_id',$userId);
        $token->token = $this->jwt::encode($payload, $key);


        return $token;
    }

    public function updateUser($id, $name, $email, $password){
        $user = User::firstWhere('_id',$id);
        if (!$user){
            throw new \Exception('user not found');
        }

        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);

        $user->update();

        return $user;
    }

    public function uploadPic($id,$imageLink)
    {
        $user = User::firstWhere('_id',$id);
            $user->imageLink = $imageLink;
            $user->update();
    }

    public function authProfile(string $userId){
        $user = User::firstWhere('_id', $userId);
        $posts = $user->posts()->get();
        $posts->transform(function ($post, $key) use ($userId) {
            $post->likes_count = $post->likes()->count();
            $post->is_liked= \DB::table('likes')
                ->where('user_id', $userId)
                ->where('post_id', (string) $post->_id)
                ->exists();
            return $post;
        });
        $user->posts=$posts;

        return $user;
    }
}
//        if($request->hasFile('imageLink')){
//            $filename = $request->imageLink->getClientOriginalName();
//            $request->imageLink->storeAs('images',$filename,'public');
//            Auth()->user()->update(['imageLink'=>$filename]);
//        }
//        return redirect()->back();
//    }
//        $user = User::find($id);
//
//        if($request->imageLink != '') {
//            $path = request()->file(‘photo’)->store(‘profile’, ‘public’);
//
//            //code for remove old file
//            if ($user->imageLink != '' && $user->imageLink != null) {
//                $image_old = $path . $user->imageLink;
//                unlink($image_old);
//            }
//
//            //upload new file
//            $imageLink = $request->imageLink;
//            $filename = $imageLink->getClientOriginalName();
//            $imageLink->move($path, $filename);
//
//            //for update in table
//            $user->update(['file' => $filename]);
//        }
//    }

