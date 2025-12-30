<?php

namespace App\Http\Controllers\Setup;
use App\Services\ShopSetup\IShopSetupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Shop;
use Illuminate\Validation\Rule;

class ShopSetupController extends Controller
{
    public function __construct(IShopSetupService $shopSetupService)
    {
        abort(404);
        // $this->middleware('auth');
        $this->shopSetupService = $shopSetupService;
    }

    public function index()
    {
        $POSID = auth()->user()->POSID;
        $shops = Shop::where('POSID',$POSID)->get();
        return view('setup/shop/index',['shops' => $shops]);
    }

    public function show($id)
    {
        $POSID = auth()->user()->POSID;
        $shop = Shop::where('POSID', '=', $POSID)
                    ->where('id', '=', $id)
                    ->first();
        
        return response()->json(['shop' => $shop,'status' => 'success']);
    } // end show

    public function edit($id)
    {
        $POSID = auth()->user()->POSID;
        $shop = Shop::where('POSID', '=', $POSID)
                    ->where('id', '=', $id)
                    ->first();
        
        return response()->json(['shop' => $shop,'status' => 'success']);
    } // end edit

    public function store(Request $request){
        try{
            $POSID = auth()->user()->POSID;

            $request->validate([           
                'name'              => 'required|string|min:3|max:100',
                'primaryPhone'      => 'required|min:11|max:20',
                'secondaryPhone'    => 'nullable|min:11|max:20',
                'division'          => 'required|string|min:3|max:100',
                'district'          => 'required|string|min:3|max:100',
                'thana'             => 'required|string|min:3|max:100',
                'address'           => 'required|string|min:3|max:100',
                'about'             => 'nullable|string|min:3|max:200',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('shops')->where('POSID', $POSID)
                ]
            ]);

            $shop = $this->shopSetupService->store($request);
            
            return response()->json([
                'status'    => 'success',
                'message'   => 'Shop Created Successfully!',
                'shop'      => $shop
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'status'    => 'error', 
                'message'   => '', 
                'errors'    => $exception->validator->errors()
            ]);
        }catch(\Exception $exception){
            return response()->json([
                'status'    => 'error', 
                'message'   => 'Something went wrong, please try later.',                     
            ]);
        }
    } // end store

    public function update(Request $request, $id){

        $POSID = auth()->user()->POSID;
        
        $request->validate([           
            'name'              => 'required|string|min:3|max:100',
            'primaryPhone'      => 'required|min:11|max:20',
            'secondaryPhone'    => 'nullable|min:11|max:20',
            'division'          => 'required|string|min:3|max:100',
            'district'          => 'required|string|min:3|max:100',
            'thana'             => 'required|string|min:3|max:100',
            'address'           => 'required|string|min:3|max:100',
            'about'             => 'nullable|string|min:3|max:200',
            'email' => [
                'required',
                'email',
                Rule::unique('shops')->ignore($id)->where('POSID', $POSID)
            ],
        ]);

        $shop = $this->shopSetupService->update($request, $id);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Shop Created Successfully!',
            'shop'      => $shop
        ]);
    } // end update

    public function destroy(Request $request, $id){
        $POSID = auth()->user()->POSID;
        $shop = Shop::where('POSID', $POSID)
            ->where('id', $id)
            ->first();
        
        $shop->delete();
        return response()->json([
            'status'    => 'success',
            'message'   => 'Shop Deleted Successfully!'
        ]);
    }
}