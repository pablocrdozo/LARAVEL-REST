<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceApiBooksController extends Controller
{
    public function __construct()
    {
		$this->url='https://openlibrary.org/api/books?bibkeys=ISBN:1878058517&jscmd=data&format=json';
		$this->curl_response=null;
    }

    public function create(Request $request,$isbn)
    {
        
        $isbn_captured = $isbn;

        $method='GET';

        $this->curl=curl_init($this->url);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
		// curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($json));
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
		$this->curl_response= curl_exec($this->curl);
		if (curl_error($this->curl)) {
			$error_msg = curl_error($this->curl);
			curl_close($this->curl);
			return $error_msg;
		}else{
			$result=$this->curl_response;
			curl_close($this->curl);
			return $result;
		}




    
        // return json_encode(array(
        //     'status' => 200,
        //     'response' => array(
        //         'mensaje' => $response
        //     )
        // ));
    }
}
