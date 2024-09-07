<?php

namespace App\Database\Seeds;

use App\Models\PlansModel;
use App\Models\PlatformModel;
use App\Models\SubscriptionsModel;
use App\Models\UsersModel;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        //
        //
        //$encrypter = service('encrypter');
        $modelPlatform = new PlatformModel();
        $idPlatform = $modelPlatform->insert([
            'company' => 'Plataforma',
            'senderEmail' => 'multidesk.io@gmail.com',
            'senderName' => 'Terapeuta Teste',
            'smtpHost' => 'smtp.gmail.com',
            'smtpUser' => 'multidesk.io@gmail.com',
            'smtpPort' => '587'
        ]);

        $modePlan = new PlansModel();
        $dataPlan[1] = [
            'platformId' => $idPlatform,
            'namePlan'   => 'TP',
            'idPlan'     => 'sz2x8n53rpuherq6hx3ow3fcsc57dd',
            'permissionUser' => 1,
            'timeSubscription' => 0
        ];
        $dataPlan[2] = [
            'platformId' => $idPlatform,
            'namePlan'   => 'TS',
            'idPlan'     => 'daoq4ookxf98xz2y6yq6vnj083yfs6',
            'permissionUser' => 2,
            'timeSubscription' => 0
        ];
        $idPlan = $modePlan->insertBatch($dataPlan);

        $modelUser = new UsersModel();

        $dataUser[0] = [
            'platformId' => $idPlatform,
            'name' => 'ADMIN',
            'email' => 'adm@conect.app',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'phone' => '+55 (62) 9 8115-4120',
            'checked' => 1,
            'admin' => true,
            //'photo' => null,
            'token' => time()
        ];

        $dataUser[1] = [
            'platformId' => $idPlatform,
            'name' => 'Demo TP',
            'email' => 'tp@conect.app',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'phone' => '+55 (62) 9 8115-4120',
            'checked' => 1,
            'admin' => false,
            //'photo' => '/assets/images/users/user-dummy-img.jpg',
            'token' => time()
        ];

        $dataUser[2] = [
            'platformId' => $idPlatform,
            'name' => 'Demo TS',
            'email' => 'ts@conect.app',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'phone' => '+55 (62) 9 8115-4120',
            'checked' => 1,
            'admin' => false,
            //'photo' => '/assets/images/users/user-dummy-img.jpg',
            'token' => time()*10
        ];

        $idUser = $modelUser->insertBatch($dataUser);
        
        $modelSubscription = new SubscriptionsModel();
        $dataSub[1] = [
            'idPlan' => 1,
            'idUser' => 1
        ];

        $dataSub[2] = [
            'idPlan' => 2,
            'idUser' => 2
        ];

        echo "\n\nProcesso de inserção no banco de dados concluído... \n\n";

        $idSubscription = $modelSubscription->insertBatch($dataSub);
    }
}
