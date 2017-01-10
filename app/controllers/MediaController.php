<?php
class MediaController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function listAction()
    {

        $data = json_decode(file_get_contents('data.json'));
        // foreach($data->media as $media) {
        //     var_dump($media);
        // }

        $this->view->setVar('data', $data->media);
        $this->view->pick('media/list');
    }

    public function addAction()
    {
        $this->view->setVar('return_to','/media/add');
        $this->view->pick('media/add');
    }

    public function addPostAction()
    {
        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;
        if (empty($return_to)) {
            $return_to = '/media/add';
        }

        $cover_path = "";
        $photo_path = "";
        if ($this->request->hasFiles() == true) {
            $isUploaded = false;
            foreach ($this->request->getUploadedFiles() as $file) {
                if($file->getName() !== "") {
                    $key = $file->getKey();

                    if ($key === "photo") {
                        $path = 'img/'. md5(uniqid(rand(), true)) . '-' .$file->getName();

                        if ($file->moveTo($path)) {
                            $isUploaded = true;
                        }

                        if ($isUploaded == false) {
                            $hasError = true;
                            $this->flashSession->error("請重新上傳展開圖片。");
                        }

                        $photo_path = $this->di->config->site->url . '/'.  $path;
                    }

                    if ($key === "cover") {
                        if(!empty($cover_data)) {
                            $cover_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $cover_data));

                            $path = 'img/'.md5(uniqid(rand(), true)).'.png';
                            file_put_contents($path, $cover_data);
                            $cover_path = $this->di->config->site->url . '/'.  $path;
                        } else {
                            $hasError = true;
                            $this->flashSession->error("請重新上傳封面圖片。");
                        }
                    }

                }
            }
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($date)) {
            $hasError = true;
            $this->flashSession->error("請輸入日期。");
        }

        $sort = (int)$sort;
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }
        if ($type === "" && $photo_path === "") {
            $hasError = true;
            $this->flashSession->error("請上傳展開圖片。");
        }
        if ($type === "link" && $url === "") {
            $hasError = true;
            $this->flashSession->error("請輸入連結網址。");
        }
        if ($type === "youtube") {
            parse_str(parse_url($url,PHP_URL_QUERY),$param_array);
            if (!array_key_exists("v", $param_array)) {
                $hasError = true;
                $this->flashSession->error("請輸入正確的 Youtube 影片網址。");
            } else {
                $url = $param_array["v"];
            }
        }

        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'media',
                'action'        => 'add',
            ));
        }else{
            $data = json_decode(file_get_contents('data.json'));
            $insert = array(
                "title" => $title,
                "summary" => $summary,
                "date" => $date,
                "cover" => $cover_path,
                "photo" => $photo_path,
                "url" => $url,
                "media" => $media,
                "type" => $type,
                "sort" => $sort,
                "create" => date('Y-m-d H:i')
            );
            $data->media[] = $insert;

            file_put_contents('data.json', json_encode($data));


            $this->flashSession->success("新增成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function editAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->media))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/media', true);
        }

        $row = $data->media[$id];

        $this->view->setVar('id', $id);
        $this->view->setVar('data', $row);
        $this->view->setVar('return_to', '/media/edit/' . $id);
        $this->view->pick('media/edit');
    }

    public function editPostAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->media))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/media', true);
        }
        $row = $data->media[$id];

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;

        $cover_path = $row->cover;
        $photo_path = $row->photo;
        if ($this->request->hasFiles() == true) {
            $isUploaded = false;
            foreach ($this->request->getUploadedFiles() as $file) {
                if($file->getName() !== "") {
                    $key = $file->getKey();

                    if ($key === "photo") {
                        $path = 'img/'. md5(uniqid(rand(), true)) . '-' .$file->getName();

                        if ($file->moveTo($path)) {
                            $isUploaded = true;
                        }

                        if ($isUploaded == false) {
                            $hasError = true;
                            $this->flashSession->error("請重新上傳展開圖片。");
                        }

                        $photo_path = $this->di->config->site->url . '/'.  $path;
                    }

                    if ($key === "cover") {
                        if(!empty($cover_data)) {
                            $cover_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $cover_data));

                            $path = 'img/'.md5(uniqid(rand(), true)).'.png';
                            file_put_contents($path, $cover_data);
                            $cover_path = $this->di->config->site->url . '/'.  $path;
                        } else {
                            $hasError = true;
                            $this->flashSession->error("請重新上傳封面圖片。");
                        }
                    }

                }
            }
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($date)) {
            $hasError = true;
            $this->flashSession->error("請輸入日期。");
        }

        $sort = (int)$sort;
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }
        if ($type === "" && $photo_path === "") {
            $hasError = true;
            $this->flashSession->error("請上傳展開圖片。");
        }
        if ($type === "link" && $url === "") {
            $hasError = true;
            $this->flashSession->error("請輸入連結網址。");
        }
        if ($type === "youtube") {
            parse_str(parse_url($url,PHP_URL_QUERY),$param_array);
            if (!array_key_exists("v", $param_array)) {
                $hasError = true;
                $this->flashSession->error("請輸入正確的 Youtube 影片網址。");
            } else {
                $url = $param_array["v"];
            }
        }

        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'media',
                'action'        => 'edit',
            ));
        }else{
            $update = array(
                "title" => $title,
                "summary" => $summary,
                "date" => $date,
                "cover" => $cover_path,
                "photo" => $photo_path,
                "url" => $url,
                "type" => $type,
                "media" => $media,
                "sort" => $sort,
                "create" => $row->create
            );
            $data->media["$id"] = $update;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("編輯成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function deleteAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));

        if (!(array_key_exists($id, $data->media))) {
            $this->flashSession->error("參數錯誤。");
        } else {
            unset($data->media[$id]);
            $data->media = array_values($data->media);

            file_put_contents('data.json', json_encode($data));
            $this->flashSession->success("刪除成功。");
        }

        return $this->response->redirect("/media", true);
    }
}

