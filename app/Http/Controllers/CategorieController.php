<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategorieResource;
use App\Http\Requests\CategorieRequestStore;
use App\Http\Resources\CategorieCollection;

class CategorieController extends Controller
{
    //index ==> all categorie + pagination
    public function all(Request $request){
        $query= $request->input('limit')??2;
        return new CategorieCollection(Categorie::paginate($query)); 
  
      }
    //add ==> add categorie + unite
    public function store(CategorieRequestStore $request){
        /* dd($request->validated()); */
        $validated=$request->validated();
        return DB::transaction(function() use($validated){
        $categorie = Categorie::create([
            "libelle"=> $validated["libelle"]
        ]);

        return response()->json([
            "data"=>$categorie,
            "succes"=>true,
            "message"=>"categorie a été ajouté avec succes"
        ]);
      
    });
}
    //delete ==> supprimer categorie
    public function delete(Request $request, int $id){
        Categorie::whereIn("id",$request->categories->delete());
    }
     
    //update ==> add categorie + unite
    public function update(Request $request,Categorie $categorie ){
        //dd($categorie);
        DB::transaction(function() use($categorie,$request){
        $categorie->update([
            "libelle"=> $request->libelle
        ]);
        $unite = Unite::byLibelle(request()->unite)->first();
            if(!$unite){
                $unite= Unite::create([
                    "libelle"=>request()->unite
                ]);
            }
            $categorie->unites()->sync([$unite->id=>["conversion"=>1,"etat"=>1]]);
        });
    }

    public function byLibelle (CategorieRequestStore $request){
        return response()->json([
            "data"=>[],
            "succes"=>true
        ]); 

    } 
}
