<?php
namespace App\Services\ShopSetup;
use App\Repositories\ShopSetup\IShopSetupRepository;
use Illuminate\Http\Request;
use App\Models\Shop;
use \stdClass;

class ShopSetupService implements IShopSetupService{
    public function __construct(IShopSetupRepository $shopSetupRepository){
        $this->shopSetupRepository = $shopSetupRepository;
    }

    public function store(Request $request){
        $metaData = new stdClass();
        $metaData->POSID        = auth()->user()->posid;
        $metaData->createdBy    = auth()->user()->id;

        $shopInfo = [
            'name'              => $request->name,
            'email'             => $request->email,
            'primaryPhone'      => $request->primaryPhone,
            'secondaryPhone'    => $request->secondaryPhone,
            'division'          => $request->division,
            'district'          => $request->district,
            'thana'             => $request->thana,
            'address'           => $request->address,
            'about'             => $request->about
        ];

        return $this->shopSetupRepository->store($shopInfo,$metaData);
    }
    
    public function update(Request $request, $id){
        $metaData = new stdClass();
        $metaData->POSID        = auth()->user()->posid;
        $metaData->updatedBy    = auth()->user()->id;
        $metaData->shopID       = $id;

        $shopInfo = [
            'name'              => $request->name,
            'email'             => $request->email,
            'primaryPhone'      => $request->primaryPhone,
            'secondaryPhone'    => $request->secondaryPhone,
            'division'          => $request->division,
            'district'          => $request->district,
            'thana'             => $request->thana,
            'address'           => $request->address,
            'about'             => $request->about
        ];
        return $this->shopSetupRepository->update($shopInfo,$metaData);
    }
}