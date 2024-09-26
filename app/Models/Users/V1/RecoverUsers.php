<?php
declare(strict_types=1);

namespace App\Models\Users\V1;

use App\Libraries\EmailsLibraries;
use App\Models\PlatformModel;
use App\Models\UsersModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

class RecoverUsers extends UsersModel
{
    /**
     * Recupera o usuário com base no email.
     *
     * @param string $email Email do usuário a ser recuperado.
     * @return bool
     * @throws PageNotFoundException Caso o usuário não seja encontrado.
     */
    public function recover(string $email): array
    {
        // Busca o usuário pelo email
        $data = $this->where('email', $email)->first();

        // Se o usuário não for encontrado, lança uma exceção do CodeIgniter
        if (!$data) {
            log_message('info', "User with email '{$email}' not found.");
            throw new PageNotFoundException("User with email '{$email}' not found.");
        }

        // Exemplo de chamada para enviar um email com as informações necessárias
        $result = $this->sendEmail($data['name'], $data['email'], $data['token'], $data['magic_link']);

        return $result;
    }

    private function sendEmail($name, $email, $token, $magicLink)
    {
        $platForm          = $this->platForm();
        $data['name']      = $name;
        $data['email']     = $email;
        $data['token']     = $token;
        $data['magicLink'] = $magicLink;
        $data['baseUrl']   = $platForm['urlBase'];
        $data['company']   = $platForm['company'];

        $liEmail = new EmailsLibraries;

        $html = view('emails/recover', $data);
        
        $email = $liEmail->send($email, 'Recuperação de senha', $html);

        return $data;
    }



    private function platForm(): array{
        $modelPlatform = new PlatformModel();
        $data = $modelPlatform->first();

        if(!$data){
            throw new Exception("Error fetching platform data");
        }

        return $data ;
    }
}
