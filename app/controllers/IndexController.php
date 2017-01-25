<?php

class IndexController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function indexAction()
    {
        parent::indexAction();
        return $this->response->redirect('banner');
    }

    public function outputAction()
    {
    	$http_origin = $_SERVER['HTTP_ORIGIN'];
    	$allow_http_origin = array(
		    'http://beautynose.com.tw',
		    'http://127.0.0.1:24681'
		);

		if (in_array($http_origin, $allow_http_origin)) {
		    header("Access-Control-Allow-Origin: $http_origin");
		}

		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: X-PINGOTHER");
		header("Access-Control-Max-Age: 1728000");

		$data = file_get_contents('data.json');
		echo $data;
    }

    public function inputAction()
    {
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        $allow_http_origin = array(
            'http://beautynose.com.tw',
            'http://127.0.0.1:24681'
        );

        if (in_array($http_origin, $allow_http_origin)) {
            header("Access-Control-Allow-Origin: $http_origin");
        }

        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Access-Control-Max-Age: 1728000");

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);

        if($_FILES['file']['tmp_name'] !== "") {
            $path = 'img/input/' . md5(uniqid(rand(), true)) . '.jpg';
            move_uploaded_file($_FILES['file']['tmp_name'], $path);

            $photo_path = $this->di->config->site->url . '/'.  $path;
        } else {
            $photo_path = "";
        }


        $input = json_decode(file_get_contents('input.json'));
        $insert = array(
            "name" => $Name,
            "facebook_nickname" => $facebook,
            "mobile" => $txtMobile,
            "address" => $address,
            "email" => $Email,
            "message" => $Subject,
            'photo' => $photo_path,
            "create" => date('Y-m-d H:i')
        );
        $input[] = $insert;

        file_put_contents('input.json', json_encode($input));

        return true;
    }

    public function beautyCountAction()
    {
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        $allow_http_origin = array(
            'http://beautynose.com.tw',
            'http://127.0.0.1:24681'
        );

        if (in_array($http_origin, $allow_http_origin)) {
            header("Access-Control-Allow-Origin: $http_origin");
        }

        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: X-PINGOTHER");
        header("Access-Control-Max-Age: 1728000");

        $data = json_decode(file_get_contents('input.json'));
        echo count($data);
    }

    public function beautyAction()
    {
        $data = json_decode(file_get_contents('input.json'));

        $this->view->setVar('data', $data);
        $this->view->pick('beauty');
    }
}