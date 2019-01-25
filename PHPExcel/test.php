<?php
/**
 * Created by PhpStorm.
 * User: weijianhua
 * Date: 18/6/16
 * Time: 上午1:01
 */

require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

// 扫描的目录
$dir = __DIR__ . '/inFile';

$files = scandir($dir);

if (empty($files)) {
    echo '目录为空' . PHP_EOL;
    exit;
}

foreach ($files as $file) {
    if ($file == '.' || $file == '..') {
        continue;
    }
    // 生成 excel
    generateExcel($file);
}


function generateExcel($file)
{
    $inputFileName = __DIR__ . '/inFile/' . $file;

    if (!file_exists($inputFileName)) {
        echo '文件名 : ' . $inputFileName . '不存在' . PHP_EOL;
        return false;
    }

    try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
        $sheet = $objPHPExcel->getSheet(0);

        $highestRow = $sheet->getHighestRow();//几行
//        var_dump($highestRow);
        $highestColumn = $sheet->getHighestColumn();//几列
//        var_dump($highestColumn);
        $arr = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z');

        $data = $sheet->toArray();

        //导出数据
        unset($objPHPExcel);

        $objPHPExcel = new PHPExcel();
// 设置文档信息，这个文档信息windows系统可以右键文件属性查看
        $objPHPExcel->getProperties()->setCreator("作者wjh")
            ->setLastModifiedBy("最后更改者")
            ->setTitle("文档标题")
            ->setSubject("文档主题")
            ->setDescription("文档的描述信息")
            ->setKeywords("设置文档关键词")
            ->setCategory("设置文档的分类");

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '手机号码')
            ->setCellValue('B1', '金额');

        $i = 2;
        foreach ($data as $key=>$value) {
            if ($key == 0) continue;
//        $value[0]; // 手机号码
//        $value[1]; // 重复次数
            $repeatCount = $value[1]/100;

            for($k = 1; $k <= $repeatCount; $k++) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $value[0])
                    ->setCellValue('B'.$i, 100);
                $i++;
            }
        }

        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $outFile = __DIR__ . '/outFile/out_' . $file;

        // 输出文件名
        $objWriter->save($outFile);

    } catch(Exception $e) {
        echo '生成 excel 错误 ' . $e->getMessage() . PHP_EOL;
        return false;
    }
}




