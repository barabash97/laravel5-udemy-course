<?php

namespace LaraCourse\Http\Controllers;

use function config;
use Illuminate\Http\Request;
use LaraCourse\Models\Album;
use DB;
use LaraCourse\Models\Photo;
use Storage;

class AlbumsController extends Controller
{
    public function index(Request $request)
    {
    
        //DB::table('albums')->  Album::
        $queryBuilder = Album::orderBy('id', 'DESC');

        if ($request->has('id')) {
            $queryBuilder->where('id', '=', $request->input('id'));
        }
        if ($request->has('album_name')) {
            $queryBuilder->where('album_name', 'like', $request->input('album_name') . '%');
        }

        $albums = $queryBuilder->get();
      
        return view('albums.albums', ['albums' => $albums]);


    }

    public function delete(Album $id)
    {

        $res = $id->delete();

        return '' . $res;
    }

    public function edit( $id)
    {
        $album = Album::find($id);
        return view('albums.editalbum')->with('album', $album);
    }

    /**
     * @param $id
     * @param Request $req
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( $id, Request $req)
    {

        $album = Album::find($id);
        $album->album_name = request()->input('name');
        $album->description = request()->input('description');
        $album->user_id = 1;
        if($req->hasFile('album_thumb')){
            
            $file = $req->file('album_thumb');
            if($file->isValid()) {
              
                //$fileName = $file->store(env('ALBUM_THUMB_DIR'));
                $fileName =$id . '.' . $file->extension();
                $file->storeAs(env('ALBUM_THUMB_DIR'), $fileName);
            
                $album->album_thumb =  env('ALBUM_THUMB_DIR').$fileName;
            }
        }
        $res = $album->save();


        $messaggio = $res ? 'Album con id = ' . $album->album_name . ' Aggiornato' : 'Album ' . $album->album_name . ' Non aggiornato';
        session()->flash('message', $messaggio);
        return redirect()->route('albums');
    }

    public function create()
    {
        return view('albums.createalbum');
    }

    public function save()
    {

        $album = new Album();
        $album->album_name = request()->input('name');
        $album->description = request()->input('description');
        $album->user_id = 1;
        $res = $album->save();
        $name = request()->input('name');
        $messaggio = $res ? 'Album   ' . $name . ' Created' : 'Album ' . $name . ' was not crerated';
        session()->flash('message', $messaggio);
        return redirect()->route('albums');
    }
   
}