<?php

function construct()
{
    load_model('index');
}

function indexsAction()
{
    if (isset($_POST['sm_action'])) {
        if (!empty($_POST['checkItem'])) {
            if ($_POST['actions'] == 'waiting') {
                foreach ($_POST['checkItem'] as $item) {
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'publish' or $post['status'] == 'trash') {
                        $data = array(
                            'status' => 'waiting'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            } elseif ($_POST['actions'] == 'trash') {
                foreach ($_POST['checkItem'] as $item) {
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'publish' or $post['status'] == 'waiting') {
                        $data = array(
                            'status' => 'trash'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            }
        }
    }
    $list_post = get_list_post_by_status();
    $data = array(
        'list_post' => $list_post
    );
    load_view('index', $data);
}
function indexAction(){
    if(isset($_POST['sm_action'])){
        if(!empty($_POST['checkItem'])){
            if($_POST['actions'] == 'waiting'){
                // show_array($_POST['checkItem']);
                foreach($_POST['checkItem'] as $item){
                    // show_array($item);
                    $id=$item;
                    $post=get_info_post_by_post_id($id);
                    if($post['status']=='publish' or $post['status']=='trash'){
                        $data=array(
                            'status'=>'waiting'
                        );
                        db_update("tbl_post",$data,"`post_id`={$id}");
                    }
                }
            }elseif($_POST['actions'] == 'trash'){
                foreach($_POST['checkItem'] as $item){
                    $id=$item;
                    $post=get_info_post_by_post_id($id);
                    if($post['status']=='publish' or $post['status']=='waiting'){
                        $data=array(
                            'status'=>'trash'
                        );
                        db_update("tbl_post",$data,"`post_id`={$id}");
                    }
                }
            }elseif($_POST['actions'] == 'publish'){
                foreach($_POST['checkItem'] as $item){
                    $id=$item;
                    $post=get_info_post_by_post_id($id);
                    if($post['status']=='trash' or $post['status']=='waiting'){
                        $data=array(
                            'status'=>'publish'
                        );
                        db_update("tbl_post",$data,"`post_id`={$id}");
                    }
                }
            }
        }
    }
    //x??? l?? ph??n trang
    //
    $num_rows=db_num_rows("SELECT * FROM `tbl_post`");
    // echo $num_rows;
     //S??? l?????ng b???n ghi tr??n m???i trang
     $num_per_page=3 ;
     //T???ng s??? b???n ghi
     $total_row=$num_rows;
     //=>T???ng s??? trang
     $num_page = ceil($total_row/$num_per_page);
    //  echo "S??? trang:{$num_page}<br>";

     
    $page=isset($_GET['id'])?(int)$_GET['id']:1;
    $start=($page -1)*$num_per_page;
   
    $list_post=get_post_by_pagging($start,$num_per_page,'tbl_post');
    // $list_post=get_list_post_by_status();
    $data_view=array(
        'list_post'=>$list_post,
        'num_page'=>$num_page,
       'page'=>$page,
       'start'=>$start
    );
    load_view('index',$data_view);
}
function addAction()
{
    global $error, $post_title, $post_desc, $post_content, $parent_cat, $slug, $target_file;
    $list_post_cat = get_list_cat();
    if (isset($_POST['btn_add'])) {
        $error = array();
        // show_array($_POST);
         //check post-title
         if (empty($_POST['post_title'])) {
            $error['post_title'] = "<span class='error'>(*) Kh??ng ???????c b??? tr???ng ti??u ????? b??i vi???t</span>";
        } else {
            if (title_post_exists($_POST['post_title'])) {
                $error['post_title'] = "<span class='error'>(*) Ti??u ????? n??y ???? t???n t???i tr?????c ????</span>";
            } else {
                $post_title = $_POST['post_title'];
            }
        }
    
     //check friendly url
     if (empty($_POST['slug'])) {
         $slug = create_slug($_POST['post_title']);
        } else {
            if (slug_url_exists(create_slug($_POST['slug']))) {
                $error['slug'] = "<span class='error'>(*) ???????ng d???n n??y ???? t???n t???i tr?????c ????</span>";
            } else {
                $slug = create_slug($_POST['slug']);
            }
        }

        //m?? t???
        if (empty($_POST['post_desc'])) {
            $error['post_desc'] = "Kh??ng ???????c ????? tr???ng m?? t??? b??i vi???t";
        } else {
            $post_desc = $_POST['post_desc'];
        }
        //n???i dung
        if (empty($_POST['post_content'])) {
            $error['post_content'] = "Kh??ng ???????c ????? tr???ng N???i dung b??i vi???t";
        } else {
            $post_content = $_POST['post_content'];
        }
        //???nh b??i vi???t
        if (empty($_POST['thumbnail_url'])) {
            $error['post_thumb'] = "Kh??ng ???????c ????? tr???ng ???nh b??i vi???t";
        } else {
            $target_file = $_POST['thumbnail_url'];
        }
        //danh m???c b??i vi???t
        if (empty($_POST['parent_cat'])) {
            $error['parent_cat'] = " Vui l??ng ch???n danh m???c b??i vi???t";
        } else {
            $parent_cat = $_POST['parent_cat'];
        }
        //tr???ng th??i
        if (empty($_POST['status'])) {
            $error['status'] = " Vui l??ng ch???n tr???ng th??i b??i vi???t";
        } else {
            if($_POST['status']==1){
                $status="waiting";
            }elseif($_POST['status']==2){
                $status="publish";
            }
        }


        if (empty($error)) {
            $date = date('Y-m-d');
            $data = array(
                'post_title' => $post_title,
                'post_desc' => $post_desc,
                'post_content' => $post_content,
                'post_cat_id' => $parent_cat,
                'post_url' => $slug,
                'post_thumb' => $target_file,
                'creator' => user_login(),
                'created_date' => $date,
                'status'=>$status

            );
            add_post($data);
            $error['success']="Th??m b??i vi???t th??nh c??ng";
           
        }
    }
    $data = array(
        'list_post_cat' => $list_post_cat
    );
    load_view('add', $data);
}
//s???a b??i vi???t
function edit_postAction()
{
    global $error, $post_title, $post_desc, $post_content, $parent_cat, $slug, $target_file;
    $post_id = (int)$_GET['post_id'];

    //validation
    if (isset($_POST['btn_edit'])) {
        $error = array();
        
        if (empty($_POST['post_title'])) {
            $error['post_title'] = "Kh??ng ???????c ????? tr???ng ti??u ?????";
        } else {
            $post_title = $_POST['post_title'];
        }
        //link th??n thi???n
        if (empty($_POST['slug'])) {
            $slug = create_slug($_POST['post_title']);
        } else {
            $slug = create_slug($_POST['slug']);
        }

        //m?? t???
        if (empty($_POST['post_desc'])) {
            $error['post_desc'] = "Kh??ng ???????c ????? tr???ng m?? t??? b??i vi???t";
        } else {
            $post_desc = $_POST['post_desc'];
        }
        //n???i dung
        if (empty($_POST['post_content'])) {
            $error['post_content'] = "Kh??ng ???????c ????? tr???ng N???i dung b??i vi???t";
        } else {
            $post_content = $_POST['post_content'];
        }
        //???nh b??i vi???t
        if (empty($_POST['thumbnail_url'])) {
            $error['post_thumb'] = "Kh??ng ???????c ????? tr???ng ???nh b??i vi???t";
        } else {
            $target_file = $_POST['thumbnail_url'];
        }
        //danh m???c b??i vi???t
        if (empty($_POST['parent_cat'])) {
            $error['parent_cat'] = " Vui l??ng ch???n danh m???c b??i vi???t";
        } else {
            $parent_cat = $_POST['parent_cat'];
        }
        //k???t lu???n
        if (empty($error)) {
            $data = array(
                'post_title' => $post_title,
                'post_desc' => $post_desc,
                'post_content' => $post_content,
                'post_cat_id' => $parent_cat,
                'post_url' => $slug,
                'post_thumb' => $target_file,
            );
            update_post($data, $post_id);
            $error['success'] = " C???p nh???t th??nh c??ng";
        }
    }
    
    $list_post_cat = get_list_cat();
    $info_post_by_post_id = get_info_post_by_post_id($post_id);
    
    $data = array(
        'list_post_cat' => $list_post_cat,
        'info_post_by_post_id' => $info_post_by_post_id
    );
    load_view('edit_post', $data);
}
//xo?? b??i vi???t
function delete_postAction()
{
    $post_id = (int)$_GET['post_id'];
    db_delete("`tbl_post`", "`post_id`={$post_id}");
    redirect_to("?mod=post&action=index");
}


//upload ???nh b??i vi???t ajax
function upload_img_postAction()
{
    if ($_SERVER['REQUEST_METHOD']) {
        if (!isset($_FILES['file'])) {
            $error['file'] = "B???n ch??a ch???n b???c k??? file ???nh n??o";
            $data = array(
                "status" => "error",
                "error" => $error
            );
            echo json_encode($data);
        } else {
            $error = array();
            $target_dir  = "public/uploads/posts/";
            $target_file = $target_dir . basename($_FILES['file']['name']);
            // check type file img valid
            $type_file = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $type_fileAllow = array('png', 'jpg', 'jpeg', 'gif');
            if (!in_array(strtolower($type_file), $type_fileAllow)) {
                $error['file'] = "H??? th???ng kh??ng h??? tr??? file n??y, vui l??ng ch???n m???t file ???nh h???p l???";
            } else {
                $file_size = $_FILES['file']['size'];
                if ($file_size > 5242880) {
                    $error['file'] = "File b???n ch???n kh??ng ???????c v?????c qu?? 5MB";
                } else {
                    if (file_exists($target_file)) {
                        $get_name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
                        $name_new = $get_name . " - Copy.";
                        $path_file_new = $target_dir . $name_new . $type_file;
                        $k = 1;
                        while (file_exists($path_file_new)) {
                            $get_name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
                            $name_new = $get_name . " - Copy({$k}).";
                            $path_file_new = $target_dir . $name_new . $type_file;
                            $k++;
                        }
                        $target_file = $path_file_new;
                    }
                }
            }
            // upload when not error
            if (empty($error)) {
                if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                    $flag = true;
                    $data = array(
                        "status" => "ok",
                        "file_path" => $target_file
                    );
                } else {
                    $error['file'] = "Upload kh??ng th??nh c??ng";
                    $data = array(
                        "status" => "error",
                        "error" => $error
                    );
                }
            } else {
                $data = array(
                    "status" => "error",
                    "error" => $error
                );
            }
            echo json_encode($data);
        }
    }
}
//show_post_publish
function show_post_publishAction()
{
    if (isset($_POST['sm_action'])) {
        if (!empty($_POST['checkItem'])) {
            if ($_POST['actions'] == 'waiting') {
                foreach ($_POST['checkItem'] as $item) {
                    // echo $item;
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'publish' or $post['status'] == 'trash') {
                        $data = array(
                            'status' => 'waiting'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            } elseif ($_POST['actions'] == 'trash') {
                foreach ($_POST['checkItem'] as $item) {
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'publish' or $post['status'] == 'waiting') {
                        $data = array(
                            'status' => 'trash'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            }
        }
    }
//x??? l?? ph??n trang
    //
    $num_rows=db_num_rows("SELECT * FROM `tbl_post` WHERE `status`='publish'");
    // echo $num_rows;
     //S??? l?????ng b???n ghi tr??n m???i trang
     $num_per_page=3 ;
     //T???ng s??? b???n ghi
     $total_row=$num_rows;
     //=>T???ng s??? trang
     $num_page = ceil($total_row/$num_per_page);
    //  echo "S??? trang:{$num_page}<br>";

     
    $page=isset($_GET['id'])?(int)$_GET['id']:1;
    $start=($page -1)*$num_per_page;
   
    $list_post=get_post_by_pagging($start,$num_per_page,'tbl_post',"`status`='publish'");
    // $list_post = get_list_post_by_status("publish");
    $data = array(
        'list_post' => $list_post,
        'num_page'=>$num_page,
       'page'=>$page,
       'start'=>$start
    );
    load_view("show_post_publish", $data);
}
function show_post_waitingAction()
{
    if (isset($_POST['sm_action'])) {
        if (!empty($_POST['checkItem'])) {
            if ($_POST['actions'] == 'publish') {
                foreach ($_POST['checkItem'] as $item) {
                    // echo $item;
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'waiting' or $post['status']=='trash') {
                        $data = array(
                            'status' => 'publish'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            } elseif ($_POST['actions'] == 'trash') {
                foreach ($_POST['checkItem'] as $item) {
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'publish' or $post['status'] == 'waiting') {
                        $data = array(
                            'status' => 'trash'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            }
        }
    }
    //x??? l?? ph??n trang
    //
    $num_rows=db_num_rows("SELECT * FROM `tbl_post`  WHERE `status`='waiting'");
    // echo $num_rows;
     //S??? l?????ng b???n ghi tr??n m???i trang
     $num_per_page=3 ;
     //T???ng s??? b???n ghi
     $total_row=$num_rows;
     //=>T???ng s??? trang
     $num_page = ceil($total_row/$num_per_page);
    //  echo "S??? trang:{$num_page}<br>";

     
    $page=isset($_GET['id'])?(int)$_GET['id']:1;
    $start=($page -1)*$num_per_page;
   
    $list_post=get_post_by_pagging($start,$num_per_page,'tbl_post',"`status`='waiting'");
    // $list_post = get_list_post_by_status("waiting");
    $data = array(
        'list_post' => $list_post,
        'num_page'=>$num_page,
        'page'=>$page,
        'start'=>$start
    );
    load_view("show_post_waiting", $data);
}
function show_post_trashAction()
{
    if (isset($_POST['sm_action'])) {
        if (!empty($_POST['checkItem'])) {
            if ($_POST['actions'] == 'restore') {
                foreach ($_POST['checkItem'] as $item) {
                    // echo $item;
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'trash') {
                        $data = array(
                            'status' => 'waiting'
                        );
                        db_update("tbl_post", $data, "`post_id`={$id}");
                    }
                }
            } elseif ($_POST['actions'] == 'delete') {
                foreach ($_POST['checkItem'] as $item) {
                    $id = $item;
                    $post = get_info_post_by_post_id($id);
                    if ($post['status'] == 'trash') {
                        db_delete("tbl_post", "`post_id`={$id}");
                    }
                }
            }
        }
    }
     //x??? l?? ph??n trang
    //
    $num_rows=db_num_rows("SELECT * FROM `tbl_post`  WHERE `status`='trash'");
    // echo $num_rows;
     //S??? l?????ng b???n ghi tr??n m???i trang
     $num_per_page=3 ;
     //T???ng s??? b???n ghi
     $total_row=$num_rows;
     //=>T???ng s??? trang
     $num_page = ceil($total_row/$num_per_page);
    //  echo "S??? trang:{$num_page}<br>";

     
    $page=isset($_GET['id'])?(int)$_GET['id']:1;
    $start=($page -1)*$num_per_page;
   
    $list_post=get_post_by_pagging($start,$num_per_page,'tbl_post',"`status`='trash'");
    // $list_post = get_list_post_by_status("trash");
    $data = array(
        'list_post' => $list_post,
         'num_page'=>$num_page,
        'page'=>$page,
        'start'=>$start
    );
    load_view("show_post_trash", $data);
}

//=========================POST_CAT===================================================
function add_catAction()
{
    global $error, $cat_title;
    if (isset($_POST['btn_add'])) {
        $error = array();
        if (empty($_POST['cat_title'])) {
            $error['cat_title'] = "T??n danh m???c b??i vi???t kh??ng ????? tr???ng";
        } else {
            if (check_post_cat_title($_POST['cat_title'])) {
                $error['cat_title'] = "T??n danh m???c ???? t???n t???i";
            } else {
                $cat_title = $_POST['cat_title'];
            }
        }
        if (empty($_POST['status'])) {
            $error['status'] = "B???n vui l??ng ch???n tr???ng th??i";
        } else {
            $status = $_POST['status'];
        }
        //k???t lu???n
        if (empty($error)) {
            $date = date('Y-m-d');
            $data = array(
                'post_cat_title' => $cat_title,
                'status' => $status,
                'creator' => user_login(),
                'created_date' => $date

            );
            $error['add_success'] = "Th??m danh m???c th??nh c??ng";
            add_cat($data);
        }
    }
    $list_post_cat = get_list_cat();
    $data = array(
        'list_post_cat' => $list_post_cat
    );

    load_view('add_cat', $data);
}
//danh s??ch danh m???c 
function list_catAction()
{

    $list_cat = get_list_cat();
    $data = array(
        'list_cat' => $list_cat
    );
    load_view('list_cat', $data);
}
//edit
function edit_catAction()
{
    $id = $_GET['id'];
    global $error, $cat_title;
    if (isset($_POST['btn_edit'])) {
        $error = array();
        if (empty($_POST['cat_title'])) {
            $error['cat_title'] = "T??n danh m???c b??i vi???t kh??ng ????? tr???ng";
        } else {
            $cat_title = $_POST['cat_title'];
        }
        if (empty($_POST['status'])) {
            $error['status'] = "B???n vui l??ng ch???n tr???ng th??i";
        } else {
            $status = $_POST['status'];
        }
        //k???t lu???n
        if (empty($error)) {
            $date = date('Y-m-d');
            $data = array(
                'post_cat_title' => $cat_title,
                'status' => $status,
                'creator' => user_login(),
                'created_date' => $date

            );
            $error['add_success'] = "C???p nh???t th??nh c??ng";
            db_update("tbl_post_cat", $data, "`post_cat_id`={$id}");
        }
    }
    $info_cat = get_info_cat_by_id($id);
    $data['info_cat'] = $info_cat;
    load_view("edit_cat", $data);
}
function delete_catAction(){
    $id=$_GET['id'];
    db_delete("tbl_post_cat","`post_cat_id`={$id}");
    redirect_to("?mod=post&action=list_cat");
}
function search_postAction(){
    //Check list:
    //1.T???o giao di???n,t???o form c?? ph????ng th???c GET(th??m 2 tr?????ng <input type="hidden" name="mod" value="post"> 
                            //<input type="hidden" name="action" value="search_post">)
    //2.X??y d???ng h??m x??? l??
    if(isset($_GET['sm_search'])){
        $key=$_GET['key'];
        // $result_search=db_fetch_array("SELECT * FROM  `tbl_post` WHERE `post_title` LIKE '%{$key}%'");
        // $table="tbl_post";
        // $field="post_title";
        $result_search= Search('tbl_post',$key,'post_title');
    }
    $data['result_search']=$result_search;
    load_view('search_post',$data);
}
