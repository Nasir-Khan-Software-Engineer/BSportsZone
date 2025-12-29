<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Validation\Rule;
class UnitController extends Controller
{
    public function index()
    {
        $posid = auth()->user()->posid;
        $units = Unit::with('creator')->where('posid', '=', $posid)->get();

        foreach ($units as $unit) {

            $unit->formattedDate = formatDate($unit->created_at);
            $unit->formattedTime = formatTime($unit->created_at);

            if ($unit->created_by == null) {
                $unit->createdBy = "CustomData";
            }else{
                $unit->createdBy = $unit->creator->name;
            }
        }

        return view('product.unit.index', ['units' => $units]);
    }

    public function edit($id){
        $posid = auth()->user()->posid;
        $unit = Unit::where('posid', '=', $posid)
                    ->where('id', '=', $id)
                    ->first();
        
        return response()->json(['unit' => $unit,'status' => 'success']);
    }

    public function store(Request $request){
        try{
            $posid = auth()->user()->posid;
            $request->validate([
                'name' => [
                    'required','string','min:3','max:100',
                    Rule::unique('units')->where('posid', $posid)
                ],
                'shortform' => [
                    'required','string','min:1','max:20',
                    Rule::unique('units')->where('posid', $posid)
                ],
                'note' => 'nullable|string|min:3|max:1000'
            ]);

            $unit = new Unit();
            $unit->posid = $posid;
            $unit->name = $request->name;
            $unit->shortform = $request->shortform;
            $unit->note = $request->note;
            $unit->created_by = auth()->user()->id;
            $unit->save();

            $unit->createdBy = auth()->user()->name;
            $unit->formattedDate = formatDate($unit->created_at);
            $unit->formattedTime = formatTime($unit->created_at);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Unit Created Successfully.',
                'unit'  => $unit
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
                'message'   => $exception
            ]);
        }
    } // end store

    public function update(Request $request, $id){
        try{
            $posid = auth()->user()->posid;
            $request->validate([
                'name' => [
                    'required','string','min:3','max:100',
                    Rule::unique('units')->ignore($id)->where('posid', $posid)
                ],
                'shortform' => [
                    'required','string','min:1','max:100',
                    Rule::unique('units')->ignore($id)->where('posid', $posid)
                ],
                'note' => 'nullable|string|min:3|max:1000'
            ]);

            $unit = Unit::with('creator')->where('posid', $posid)
                ->where('id', $id)
                ->first();
            $unit->name = $request->name;
            $unit->shortform = $request->shortform;
            $unit->note = $request->note;
            $unit->updated_by = auth()->user()->id;
            $unit->save();

            $unit->createdBy = $unit->creator->name;
            $unit->formattedDate = formatDate($unit->created_at);
            $unit->formattedTime = formatTime($unit->created_at);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Unit Updated Successfully.',
                'unit'  => $unit
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
    } // end update

    public function destroy($id){
        $posid = auth()->user()->posid;
        $unit = Unit::where('posid', $posid)
            ->where('id', $id)
            ->first();

         if($unit->products()->count() > 0){
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'Dependent' => ['This unit has service items.']
                ],
            ]);
        }else{
            $unit->delete();
            return response()->json([
                'status'    => 'success',
                'message'   => "Unit Deleted Successfully."
            ]);
        }
    }
}