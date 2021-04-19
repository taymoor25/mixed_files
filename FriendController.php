<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use Illuminate\Support\Facades\Storage;
use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

          $friends = Friend::all();
           
         return view('index')->with(['friends'=>$friends]);    



     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('createfriend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

     $validated = $request->validate([
        'friendname' => 'required|max:10',
        'avatar' => 'required',
    ]);
     $ext =  $request->avatar->getClientOriginalExtension();
     $filename = $request->avatar->getClientOriginalName();
     $request->avatar->storeAs('Avatars', $filename,'public');
     if ($ext == 'png' || $ext == 'jpg')
     {
        
        Friend::create(['name' =>$request->friendname,'avatar' => $filename
    ]);
    }

return redirect()->back()->with('msg','Friend Add succesfully..');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {


        $obj = Friend::find($id);
        return view ('edit',compact('obj'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
          $validated = $request->validate([
        'friendname' => 'required|max:10',
        'avatar' => 'required',
    ]);
                $obj = Friend::find($id);
                Storage::delete('/public/Avatars/'.$obj->avatar);

     $ext =  $request->avatar->getClientOriginalExtension();
     $filename = $request->avatar->getClientOriginalName();
     $request->avatar->storeAs('Avatars', $filename,'public');

     if ($ext == 'png' || $ext == 'jpg')
     {

        
Friend::where('id', $id)->update(['name' => $request->friendname , 'avatar' => $filename]);    
    }

    else{return redirect()->back()->with('error','File Format is not supoorted use png and jpg only');}


    return redirect()->back()->with('msg','Update succesfully');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $f = Friend::find($id);
        $f->delete();
        return redirect()->back()->with('msg','Delete succesfully');;     
    }

public function csv(){
return (new cvsfile)->download('test.csv');
}


};



class cvsfile implements FromQuery{

// public function collection(){

//         $f = Friend::find(3);
//         return $f;

// }
use Exportable;

    public function query()
    {
        return Friend::query()->where('id',3);
    }


}
