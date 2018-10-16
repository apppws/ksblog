<?php 
    namespace controllers;
    use Intervention\Image\ImageManagerStatic as Image;
    class IndexController{
        public function index(){

            view('index.index');
        }
        
        // 获取子分类
        public function ajax_get_cat(){

            $id = (int)$_GET['id'];
            // var_dump($id);
            $model = new \models\Category;
            $topCat = $model->getCat($id);
            // 转成json格式
            echo json_encode($topCat);
        }

        // 显示页面
        public function insert(){
            $model = new \models\Category;
            $topCat = $model->getCat();
            // echo "<pre>";
            // var_dump($topCat);
            view('index.insert',[
                'topCat'=>$topCat['data']
            ]);
        }
        // // 处理插入数据
        public function doinsert(){
            $title = $_POST['title'];
            $user = $_POST['user'];
            // var_dump($_FILES['logo']);
            // 打开这个图片    并生成缩略图
            $img  =  Image::make($_FILES['logo']['tmp_name']);
            // 缩放图片
            $img->resize(50,50);
            // 保存图片
            $img->save(ROOT . 'public/uploads/thum/1.png');
            $img->resize(100,100);
            $img->save(ROOT . 'public/uploads/thum/2.png');
            $img->resize(200,200);
            $img->save(ROOT . 'public/uploads/thum/3.png');
            // var_dump($img);
            $uploadfile = \libs\Uploadfile::file();
            $logo = '/uploads/' .$uploadfile->upload('logo','logo');
            // $this->data['logo'] = $logo;
            // 判断这个logo的
            
            $content = $_POST['content'];
            $cat1_id = $_POST['cat1_id'];
            $cat2_id = $_POST['cat2_id'];
            $cat3_id = $_POST['cat3_id'];
            // var_dump($title,$user,$logo,$content,$cat1_id,$cat2_id,$cat3_id);
            $model = new \models\Blog;           
             $model->insert($title,$user,$logo,$content,$cat1_id,$cat2_id,$cat3_id);
            redirect('/index/design');
        }
        public function design(){
            $model = new \models\Blog;
            $data = $model->findAll();
            // var_dump($data);
            $model = new \models\Category;
            $topCat = $model->getCat();
            // var_dump($topCat);
            view('index.design',[
                'data'=>$data['data'],
                'page'=>$data['page'],
                'topCat'=>$topCat['data']
            ]);
        }

        public function edit(){
            $model = new \models\Blog;
            $data=$model->findOne($_GET['id']);
            // var_dump($data);
            view('index.edit', [
                'data' => $data,    
            ]);
        }
        public function doedit(){
            $title = $_POST['title'];
            $user = $_POST['user'];
            $blog_type_id = 1;
            $uploadfile = \libs\Uploadfile::file();
            $logo = '/uploads/' .$uploadfile->upload('logo','brand');
            $content = $_POST['content'];
            $id=$_GET['id'];
            $model = new \models\Blog;
            $model->update($title,$user,$logo,$content,$blog_type_id,$id);
            redirect('/index/design');
        }

        public function delete(){
            $model = new \models\Blog;
            $model->delete($_GET['id']);
            redirect('/index/design');
        }
    }


?>