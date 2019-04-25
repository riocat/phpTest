<?php


class excelUtil
{

    /**
     * Created by PhpStorm.
     * function: data_import
     * Description:导入数据
     * User: Xiaoxie
     * @param $filename
     * @param string $exts
     * @param $or
     *
     */
    public function data_import($filename, $exts = 'xls')
    {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        require_once '../PHPExcel/PHPExcel.php';
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if ($exts == 'xls') {
            require_once '../PHPExcel/PHPExcel/Reader/Excel5.php';
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            require_once '../PHPExcel/PHPExcel/Reader/Excel2007.php';
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }


        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$data中
                $cell = $currentSheet->getCell($address)->getValue();

                if ($cell instanceof PHPExcel_RichText) {
                    $cell = $cell->__toString();
                }
                $data[$currentRow - 1][$currentColumn] = $cell;
                //  print_r($cell);
            }

        }

        // 写入数据库操作
        $this->my_insert($data);
    }


    /**
     * Created by PhpStorm.
     * function: insert_data
     * Description:写入数据库操作
     * User: Xiaoxie
     * @param $data
     *
     */
    public function insert_data($data)
    {
        $created_time = date('Y-m-d H:i:s');
        $apinfo = A('apinfo');
        foreach ($data as $k => $v) {

            if ($k != 0) {
                //shop信息
                $info['shop_name'] = $v['C'];
                $info['address'] = $v['D'];
                $info['contact_name'] = $v['I'];
                $info['contact_phone'] = $v['J'];
                $info['lng'] = $v['G'];
                $info['lat'] = $v['H'];

                $info['shop_code'] = time() . $k;
                $type_explain = $v['K'];
                $where['type_explain'] = array('like', "%$type_explain%");

                $info['type_code'] = 5;

                $info['wa_area'] = $v['L'];

                $id = M('shop')->add($info);//shop_id
                $info['insert_time'] = date('Y-m-d H:i:s');

                //开始添加device信息


                $infos['dev_no'] = $info['shop_code'];
                $infos['dev_code'] = $v['B'];
                $infos['dev_mac'] = strtolower(str_replace('-', '', $v['B']));
                $infos['device_name'] = $v['C'];
                $infos['device_ip'] = $v['F'];
                $infos['location_id'] = '3397';
                $infos['area_code'] = $v['L'];
                $infos['address'] = $v['D'];
                $infos['device_address'] = $v['D'];

                $infos['agent_id'] = 1;
                $infos['customer_id'] = 1;
                $infos['shop_id'] = $id;
                $infos['lng'] = $v['G'];
                $infos['lat'] = $v['H'];
                $infos['pss'] = $v['M'];
                $infos['site_code'] = $apinfo->setWanganCode($v['L'], 3, $info['type_code'], $id);

                $result = M('device')->add($infos);
                $apinfo->insertdevice($info, $infos, $id);
                $apinfo->apinfo_defaultoption($infos['dev_mac']);
            }

        }
    }

    public function my_insert($data){

        echo print_r($data);

        $con = mysqli_connect("localhost", "root", "abcd1234");
        if (!$con) {
            die('Could not connect: ' . mysqli_error($con));
        } else {
            echo "success";
        }

        mysqli_select_db($con, "lottery");

        foreach($data as $x=>$x_value)
        {
            $sqlstr = "INSERT INTO `importlot` (`id`, `no`, `ball1`, `ball2`, `ball3`, `ball4`, `ball5`, `ball6`, `ball7`, `result_string`)".
                " VALUES (".$x_value['A'].", ".$x_value['B'].", ".$x_value['C'].", ".$x_value['D'].", ".$x_value['E'].", ".$x_value['F'].", ".$x_value['G'].", ".$x_value['H'].", ".$x_value['I'].", '".$x_value['J']."');";

            if (mysqli_query($con, $sqlstr)) {
                echo "插入成功";
            } else {
                echo "插入失败";
            }
        }

        mysqli_close($con);
    }

    /**
     * Created by PhpStorm.
     * function: imports
     * Description:导入excell
     * User: Xiaoxie
     *
     */
    public function imports()
    {
        header("Content-Type:text/html;charset = utf-8");

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('xls', 'xlsx');// 设置附件上传类
        $upload->rootPath = './public/Uploads/'; // 设置附件上传目录
        // 上传文件
        $info = $upload->uploadOne($_FILES['excelData']);
        $filename = $upload->rootPath . $info['savepath'] . $info['savename'];
        $exts = $info['ext'];
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            $this->data_import($filename, $exts, 3);
        }

    }
}