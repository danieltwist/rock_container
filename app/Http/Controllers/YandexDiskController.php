<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Yandex\Disk\DiskClient;

use Illuminate\Http\Request;

class YandexDiskController extends Controller
{
    public function check(){

        if(isset($_COOKIE['yaToken'])){
            $diskClient = new DiskClient($_COOKIE['yaToken']);
            $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);

            $root_folder = 'Тест Rock/2021_09_27_1ктк тестовая папка проекта';
            dd($root_folder);
            $subfolders = [
                'Заявки', 'Договоры', 'Договоры/Поставщики', 'Договоры/Клиенты'
            ];
            $files = [
                [
                    'name' => 'Тестовый договор шаблон.xlsx', 'folder' => 'Договоры/Клиенты'
                ],
                [
                    'name' => 'Заявка.xlsx', 'folder' => 'Заявки'
                ]
            ];

            try {
                $dirContent = $diskClient->createDirectory($root_folder);
                if ($dirContent) {
                    echo 'Создана новая директория "' . $root_folder . '"!<br>';

                    foreach ($subfolders as $folder){
                        try {
                            $dirContent = $diskClient->createDirectory($root_folder.'/'.$folder);
                            if ($dirContent) {
                                echo 'Создана новая директория "' . $root_folder.'/'.$folder . '"!<br>';
                            }
                        }
                        catch (\Exception $e) {
                            continue;
                        }

                    }

                    foreach ($files as $file){
                        $fileName = public_path('/storage/excel_containers/containers_update_template.xlsx');
                        $newName = $file['name'];
                        if (file_exists($fileName)){
                            try{
                                $diskClient->uploadFile(
                                    $root_folder.'/'.$file['folder'].'/',
                                    array(
                                        'path' => $fileName,
                                        'size' => filesize($fileName),
                                        'name' => $newName
                                    )
                                );
                                echo 'Создан файл '.$root_folder.'/'.$file['folder'].'/'.$file['name'].'<br>';
                            }
                            catch (\Exception $e) {
                                continue;
                            }
                        }

                    }

                }
            }
            catch (\Exception $e) {
                echo 'Папка данного проекта уже есть на диске';
            }



            /*
            // Получаем список файлов из директории
            $dirContent = $diskClient->directoryContents('/');

            foreach ($dirContent as $dirItem) {
                if ($dirItem['resourceType'] === 'dir') {
                    echo 'Директория "' . $dirItem['displayName'] . '" была создана ' . date(
                            'Y-m-d в H:i:s',
                            strtotime($dirItem['creationDate'])
                        ) . '<br />';
                } else {
                    echo 'Файл "' . $dirItem['displayName'] . '" с размером в ' . $dirItem['contentLength'] . ' байт был создан ' . date(
                            'Y-m-d в H:i:s',
                            strtotime($dirItem['creationDate'])
                        ) . '<br />';
                }
            }*/
        }
        else {
            return 'Сначала авторизируйтесь в Яндекс';
        }


    }
}
