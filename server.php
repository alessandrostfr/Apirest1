<?php

    //En este ejemplo vamos a imaginar que nuestra bd es de libros, una libreria

                                                                                                

    //VALIDACION POR HASH

    // //Validamos que los datos existen
    // if( !array_key_exists('HTTP_X_HASH', $_SERVER) || 
    //     !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) || 
    //     !array_key_exists('HTTP_X_UID', $_SERVER)){
    //         die;
    // }

    // //Metemos los datos en 3 variables
    // list($hash, $uid, $timestamp) = [
    //     $_SERVER['HTTP_X_HASH'],
    //     $_SERVER['HTTP_X_UID'],
    //     $_SERVER['HTTP_X_TIMESTAMP'],
    // ];

    // //Creamos la clave secreta
    // $secret = 'Sh!! No se lo cuentes a nadie!';

    // //Creamos el hash con los
    // $newHash = sha1($uid.$timestamp.$secret);

    // if($newHash !== $hash){
    //     die;
    // }

                                                                                                

    //VALIDACION POR TOKEN(LA MEJOR)

    if( !array_key_exists('HTTP_X_TOKEN', $_SERVER)){
        die;
    }
    $url = 'http://localhost:8001';

    $ch = curl_init($url);

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
        ]
    );

    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true

    );

    $ret = curl_exec($ch);

    if($ret !== 'true'){
        die;
    }

    //Definimos los recursos disponibles
    $recursosAdmitidos = [
        'books',
        'authors',
        'genders'
    ];

    //Validamos que el recurso este disponible
    $tipoRecurso = $_GET['tipoRecurso'];

    if(!in_array($tipoRecurso, $recursosAdmitidos)){
        echo $error = http_response_code( 400 );
        die;
    }



    //Defino los recursos
    $books = [
        1 =>[
            'titulo' => 'Lo que el viento se llevo',
            'id_autor' => 2,
            'id_genero' => 2,
        ],
        2 =>[
            'titulo' => 'La Odisea',
            'id_autor' => 3,
            'id_genero' => 3,
        ],
        3 =>[
            'titulo' => 'La Iliada',
            'id_autor' => 1,
            'id_genero' => 1,
        ],
    ];



    //Definimos un header para comunicarle al cliente el tipo de dato que devuelve la api
    header('Content-Type: application/json');

    //Levantamos el id del recurso buscado
    $resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : ''; //if inmediato

    //Generamos la respuesta
    switch(strtoupper($_SERVER['REQUEST_METHOD']))  {


                                                                                                

        //METODO PARA QUE EL USUARIO RECIBA DATOS DE MI APLICACION
        case 'GET':
            if(empty($resourceId)){
            echo json_encode($books);
            }else{
                if(array_key_exists($resourceId, $books)){
                    echo json_encode($books[$resourceId]);
                }else{
                    http_response_code( 404 );
                }
            }
        break;

                                                                                                

        //METODO PARA QUE EL USUARIO INSERTE DATOS DE MI APLICACION
        case 'POST':
            //Tomamos la entrada cruda
            $json = file_get_contents('php://input');

            //Transformamos el json recibido a un nuevo elemento del array
            $books[] = json_decode($json, true);

            // echo array_keys($books)[count($books) - 1];
            echo json_encode( $books );
        break;

                                                                                                

        //METODO PARA QUE EL USUARIO ACTUALICE DATOS DE MI APLICACION
        case 'PUT':
            //Validamos que el recurso buscado exista
            if( !empty($resourceId) && array_key_exists($resourceId, $books)){
                //Tomamos  la entrada cruda
                $json = file_get_contents('php://input');

                //Transformamos el json recibido a un nuevo elemento del array
                $books[$resourceId] = json_decode($json, true);

                //Retornamos la coleccion modificada en formato json
                echo json_encode($books);
            }
        break;

                                                                                                

        //METODO PARA QUE EL USUARIO ELIMINE DATOS DE MI APLICACION
        case 'DELETE':
            //Validamos que el recurso exista
            if( !empty($resourceId) && array_key_exists($resourceId, $books)){
                //Eliminamos el recurso
                unset($books[$resourceId]);
            }
            echo json_encode($books);
        break;

    }

?>