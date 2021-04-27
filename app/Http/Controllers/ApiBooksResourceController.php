<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use SimpleXMLElement;

class ApiBooksResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books=Book::select('id','isbn','title_of_the_book','authors','cover')->get();
        $result_books = json_encode($books);

        return $result_books;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($isbn)
    {
        $isbn_captured = $isbn;

        $search_book=Book::select('id','isbn','title_of_the_book','authors','cover')->where('isbn','=',$isbn_captured)->first();
        if ($search_book != null) {
            $search_book=Book::select('id','isbn','title_of_the_book','authors','cover')->where('isbn','=',$isbn_captured)->first()->toArray();

            $info = json_encode($search_book);
            $xml = $this->crearXML2($search_book);
            // dd($xml);
            return $xml;
        }else{
            $xml = $this->crearXML_Error($search_book);

            return $xml;
        }
        
    }

    public function crearXML($array, $xml = false)
    {
        if($xml === false){
            $xml = new SimpleXMLElement('<libro/>');
        }
    
        foreach($array as $key => $value){
            if(is_array($value)){
                array2xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }
    
        return $xml->asXML();
    }

    public function crearXML2($array, $xml = false)
    {
        $xmlstr = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <Libros>
            <Libro>
                <Titulo>'.$array['title_of_the_book'].'</Titulo>
                <Isbn>'.$array['isbn'].'</Isbn>
                <Autores>
                    <Autor>'.$array['authors'].'</Autor>
                </Autor>
                <Caratula>'.$array['cover'].'</Caratula>
            </Libro>
            </Libros>';
            
        return $xmlstr;
    }

    public function crearXML_Error()
    {
        $xmlstr = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
        <Libros>
          Error Libro no encontrado
        </Libros>';

        return $xmlstr;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
