<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ]);
       
        if ($validator->fails()){
            return response()->json(['message' => 'Email already taken'], 400);
        } else{
            $user = new User([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $user->save();
            return response()->json(['message' => 'User successfully registered'], 201);
        }
        
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)){
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        //$token = $tokenResult->token;
        //$token->expires_at = Carbon::now()->addWeeks(1);
        //$token->save();

        return response()->json(
           // ['data' => [
           // 'user' => Auth::user(),
            ['access token' => $tokenResult->accessToken]
            //'token_type'  =>'Bearer',
            //'expires_at'  => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
       // ]]
    );
    }

    public function order(Request $request)
    {
        
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $order = new Order([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity
        ]);
        //$Order_product_id = Order::find($request->product_id);
     
        $update = DB::table('orders')->where('product_id',$request->product_id)->update(['quantity'->$request->quantity]);
        $update->save();
        return response()->json(['message' => 'You have successfully ordered this product.'], 201);
    }
}
