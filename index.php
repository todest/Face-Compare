<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"><title>人脸对比</title>
    <link rel="icon" href="img/head.ico">
    <link rel="stylesheet" href="/css/style.css">
    <script type="text/javascript" src="/js/function.js"></script>
</head>
<body>
<script src="//cdn.bootcss.com/canvas-nest.js/1.0.1/canvas-nest.js"></script>
<div class="demo-container">
    <div class="demo-face">
        <div class="demo-title">
            <h2><span>人脸对比-功能演示</span></h2>
            <p><span>即刻体验Face++的人脸比对能力。请上传本地图片或提供图片URL。</span></p>
            <p><span>该功能演示是基于Compare API搭建的。如果您对技术能力有特别要求，请联系我们。</span></p>
        </div>
        <div class="content">
            <div id="demo-img">
                <div class="img">
                    <div class="img1"><img src="img/default.png" id="photo1" style="width: 330px;height: 330px;" /></div>
                    <div class="img2"><img src="img/default.png" id="photo2" style="width: 330px;height: 330px;" /></div>
                </div>
                <div class="upload-img">
                    <form method="post" enctype="multipart/form-data">
                        <div class="upload-button">
                            <div class="button1">
                                <input type="file" name="file1" id="file1" onchange="preImg(this.id,'photo1');" />
                            </div>
                            <div class="button2">
                                <input type="file" name="file2" id="file2" onchange="preImg(this.id,'photo2');" />
                            </div>
                        </div>
                        <div class="submit"><input type="submit" name="确认" value="submit"></div>
                    </form>
                </div>
            </div>
            <?php
            if(!empty($_FILES['file1']['tmp_name']) && !empty($_FILES['file2']['tmp_name'])){
                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $temp1 = explode(".", $_FILES["file1"]["name"]);
                $temp2 = explode(".", $_FILES["file2"]["name"]);
                $extension1 = end($temp1);
                $extension2 = end($temp2);    // 获取文件后缀名
                if ((($_FILES["file2"]["type"] == "image/gif")
                        || ($_FILES["file2"]["type"] == "image/jpeg")
                        || ($_FILES["file2"]["type"] == "image/jpg")
                        || ($_FILES["file2"]["type"] == "image/pjpeg")
                        || ($_FILES["file2"]["type"] == "image/x-png")
                        || ($_FILES["file2"]["type"] == "image/png")
                        || ($_FILES["file1"]["type"] == "image/gif")
                        || ($_FILES["file1"]["type"] == "image/jpeg")
                        || ($_FILES["file1"]["type"] == "image/jpg")
                        || ($_FILES["file1"]["type"] == "image/pjpeg")
                        || ($_FILES["file1"]["type"] == "image/x-png")
                        || ($_FILES["file1"]["type"] == "image/png"))
                    && ($_FILES["file2"]["size"] < 204800)  && ($_FILES["file1"]["size"] < 204800)  // 小于 200 kb
                    && in_array($extension1, $allowedExts) && in_array($extension2, $allowedExts))
                {
                    if ($_FILES["file1"]["error"] > 0 && $_FILES["file2"]["error"] > 0)
                    {
                        $error = $_FILES["file1"]["error"];
                        echo "<script>window.alert('错误：'+'$error')</script>";
                    }
                    else
                    {
                        // 判断当期目录下的 upload 目录是否存在该文件
                        // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                        if (file_exists("upload/" . $_FILES["file1"]["name"]) && file_exists("upload/" . $_FILES["file2"]["name"]))
                        {
                            echo "<script>window.alert('文件已存在')</script>";
                        }
                        else
                        {
                            // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                            move_uploaded_file($_FILES["file1"]["tmp_name"], "upload/" . $_FILES["file1"]["name"]);
                            move_uploaded_file($_FILES["file2"]["tmp_name"], "upload/" . $_FILES["file2"]["name"]);
                        }
                    }
                }
                else
                {
                    echo "非法的文件格式";
                }
                $image1 = "upload/" . $_FILES["file1"]["name"];
                $image2 = "upload/" . $_FILES["file2"]["name"];
                $fp1 = fopen($image1, 'rb');
                $content1 = fread($fp1, filesize($image1));
                $fp2 = fopen($image2, 'rb');
                $content2 = fread($fp2, filesize($image2));
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api-cn.faceplusplus.com/facepp/v3/compare",     //输入URL
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array(
                        'image_file1";filename="image1'=>"$content1",
                        'image_file2";filename="image2'=>"$content2",
                        'api_key'=>"f_X9pSle6V5YkXOV9zZz3gcpsznHIqyO",
                        'api_secret'=>"aBXbHr85uNLCjuDdZkZrZrDAwUMOjWD-"
                    ),   //输入api_key和api_secret
                    CURLOPT_HTTPHEADER => array("cache-control: no-cache",),
                ));
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    echo  "<script>window.alert('cURL Error #:'+'$err')</script>";
                } else {
                    $array = json_decode($response,true);
                    $conf = $array['confidence'];
                    echo("<script>window.alert('相似度：'+'$conf'+'%')</script>");
                }
            }
            ?>
            <div id="respond">
                <div class="subtitle">Response JSON</div>
                <div class="json">
                    <?php
                    if(isset($response))
                    {
                        echo $response;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>