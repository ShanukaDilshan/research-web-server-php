<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['base64Image'])) {

        $base64Image = $data['base64Image'];
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $imagePath = 'uploads/' . uniqid() . '.jpg';

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (file_put_contents($imagePath, $imageData)) {

            $data = base64_encode(file_get_contents($imagePath));

            $api_key = "jKTKqK6pyYwvcvx2RvJr";
            $model_endpoint = "fish-quality-grading/2";
            $url = "https://detect.roboflow.com/" . $model_endpoint
                . "?api_key=" . $api_key
                . "&name=YOUR_IMAGE.jpg";

            
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => $data
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            echo $result;
        } else {
            echo json_encode(["message" => "Failed to save the image."]);
        }
    } else {
        echo json_encode(["message" => "No image data received."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method."]);
}
exit();
