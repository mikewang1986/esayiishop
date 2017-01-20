<?php

return [   // this is used in contact page
    'admin' => 'superbeta',
    'adminPassword' => '9be4e9c1e40a1952d3ad23cdb9343cedce6814d8e2b031d9d775ba58a02108b0',
    'adminEmail' => 'fainqin@foxmail.com',
//        'contactEmail' => 'contactus@mingyizhudao.com',
    'contactEmail' => '147566179@qq.com',
    'csadminEmail' => 'csadmin@mingyizhudao.com',
    // 'registerBaiduScript' => true,
    'medicalRecordFilePath' => 'upload/mr',
    'bookingFilePath' => 'upload/booking',
    "doctorFilePath" => "upload/doctor/cert",
    "patientMRFilePath" => "upload/patient/mr",
    'connectionString' => '120.26.107.48:27017',
    'rpcEmailUrl' => 'http://121.40.127.64:8848/rpc/server/sendEmail',
    'rpcSmsUrl' =>  'http://121.40.127.64:8848/rpc/server/sendSms',
    'doctorcase'=>require(dirname(__FILE__).'/doctorcase.php'),
    'qiniuyunurl'=>'http://7xq939.com2.z0.glb.qiniucdn.com',
    //'wifi_email'=>'1063408776@qq.com',
    //'wifi_email'=>array('147566179@qq.com','mingtianrjtc@163.com'),
    'wifi_email'=>array('mingtianrjtc@163.com'),
    'sina_email'=>array('mingtianrjtc@163.com'),
    'macity_email'=>'147566179@qq.com',

    'fileUrl'=> array(
        'wap.dev.mingyizd.com' => 'http://file.mingyizhudao.com',
        'm.mingyizhudao.com' => 'http://121.40.127.64:8089',
        '121.41.82.86:8002' => 'http://121.40.127.64:8089',

    ),
    'wapurl'=>
        array(
            'http://m.mingyizhudao.com',
            'http://demowap.mingyizd.com',
            'http://121.40.127.64:8022',
            'http://h5.dev.mingyizd.com',
            'http://m.myzd.com.cn',
        )
];
