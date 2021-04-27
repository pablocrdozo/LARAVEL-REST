<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon as Carbon;
use App\Models\Book;

class ServiceApiBooksController extends Controller
{
    public function __construct()
    {
		$this->url='https://openlibrary.org/api/books?bibkeys=ISBN:';
		$this->curl_response=null;

		//Conexión base de datos
		$this->mysql_db = DB::connection('mysql');
    }

    public function create(Request $request,$isbn)
    {
        $isbn_captured = $isbn;

        $method='GET';
		$complement_url = '&jscmd=data&format=json';

        $this->curl=curl_init($this->url.$isbn_captured.$complement_url);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		$this->curl_response= curl_exec($this->curl);
		if (curl_error($this->curl)) {
			$error_msg = curl_error($this->curl);
			curl_close($this->curl);
			return json_encode(array(
				'status' => "Error",
				'response' => array(
					'mensaje' => $error_msg
				)
			));
		}else{
			$result=$this->curl_response;
			curl_close($this->curl);
			if ($result == '{}') {
				return json_encode(array(
					'status' => "Error",
					'response' => array(
						'mensaje' => "ISBN no fue encontrado"
					)
				));
			}else{
				$result_decote = \json_decode($result, true);
				
				//Datos para almacenar en bd
				$title_book = $result_decote["ISBN:".$isbn_captured]['title'];
				$authors = $result_decote["ISBN:".$isbn_captured]['authors'];
				$count_authors = count($authors);
				if ($count_authors == 1) {
					$result_authors = $authors[0]['name'];
				}elseif ($count_authors > 1) {
					$result_authors = [];
					foreach ($authors as $authors) {
						$arreglo = array_push($result_authors,$authors['name']);
					}
					$result_authors = json_encode($result_authors);
				}else{
					$result_authors = 'No tiene autor';	
				}

				if (isset($result_decote["ISBN:".$isbn_captured]['cover']["large"])) {
					$cover = $result_decote["ISBN:".$isbn_captured]['cover']["large"];
				}else{
					$cover = "No tiene carátula";
				}
				
				
				//Verficicación de que no exista
				$verification_isbn = $this->mysql_db->table('books')
					->select('*')
					->where('isbn','=',$isbn_captured)
					->first();
				
				if($verification_isbn != null){
					return json_encode(array(
						'status' => "Error",
						'response' => array(
							'mensaje' => "El libro ya se encuentra en la base de datos"
						)
					));
				}

				$insert_database = $this->mysql_db->table('books')
					->insert(array(
						"isbn" => $isbn_captured,
						"title_of_the_book" => $title_book,
						"authors" => $result_authors,
						"cover" => $cover,
						'created_at' => Carbon::now('America/Bogota'),
                        'updated_at' => Carbon::now('America/Bogota')));
				
				if (isset($insert_database)) {
					return json_encode(array(
						'status' => "Exito",
						'response' => array(
							'mensaje' => "Libro guardado correctamente"
						)
					));
				}else{
					return json_encode(array(
						'status' => "Error",
						'response' => array(
							'mensaje' => "Ocurrio un error al guardar el libro"
						)
					));
				}
			}
		}
    }


	public function Delete(Request $request,$isbn)
	{
		$isbn_captured = $isbn;

		$books_search=Book::select('id','isbn','title_of_the_book','authors','cover')->where('isbn','=',$isbn_captured)->first();
		if ($books_search != null) {
			$dele_book = Book::where('isbn','=',$isbn_captured)->delete();
			if (isset($dele_book)) {
				return json_encode(array(
					'status' => "Exito",
					'response' => array(
						'mensaje' => "El libro fue eliminado correctamente"
					)
				));
			}else{
				return json_encode(array(
					'status' => "Error",
					'response' => array(
						'mensaje' => "Ocurrio un error al eliminar el libro de la base de datos"
					)
				));
			}
		}else{
			return json_encode(array(
				'status' => "Error",
				'response' => array(
					'mensaje' => "El libro enviado no existe en la base de datos"
				)
			));
		}

		dd($isbn_captured);
	}
}
