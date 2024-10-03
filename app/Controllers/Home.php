<?php

namespace App\Controllers;

use App\Libraries\EmailsLibraries;
use App\Libraries\ReportsLibraries;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Attributes as OA;

use Dompdf\Dompdf;

class Home extends ResourceController
{
    use ResponseTrait;

    #[OA\Get(
        path: "/",
        tags: ["Status"],
        summary: "Verifica o status do sistema",
        description: "Retorna o status atual do sistema, versão, consumo de memória e tempo de carregamento.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Status atual do sistema",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "development"),
                        new OA\Property(property: "version", type: "string", example: "1.0.0"),
                        new OA\Property(property: "memory", type: "string", example: "13.08 MB"),
                        new OA\Property(property: "load", type: "string", example: "0.0944 seconds"),
                        new OA\Property(property: "docs", type: "string", example: "https://api.multidesk.io/docs"),
                    ]
                )
            )
        ]
    )]

    #[OA\Schema(
        schema: "StatusResponse",
        type: "object",
        properties: [
            new OA\Property(property: "status", type: "string", example: "development"),
            new OA\Property(property: "version", type: "string", example: "1.0.0"),
            new OA\Property(property: "memory", type: "string", example: "13.08 MB"),
            new OA\Property(property: "load", type: "string", example: "0.0944 seconds"),
            new OA\Property(property: "docs", type: "string", example: "https://api.multidesk.io/docs"),
        ]
    )]
    public function index()
    {
        $elapsedTime = microtime(true) - APP_START;
        $memoryUsage = memory_get_usage() / (1024 * 1024);

        return $this->respond([
            'status' => getenv("CI_ENVIRONMENT"),
            "version" => "1.0.0",
            "memory" => number_format($memoryUsage, 2) . ' MB',
            "load"  => number_format($elapsedTime, 4) . ' seconds',
            "docs" => site_url("docs")
        ]);
    }

    public function teste()
{
    // Impede que qualquer saída anterior interfira
    ob_start();  // Inicia o buffer de saída

    // Crie uma instância do Dompdf
    $dompdf = new Dompdf();

    // Conteúdo HTML para o PDF (carregando uma view)
    $html = view('pdf_template');

    // Carregar o HTML no Dompdf
    $dompdf->loadHtml($html);

    // Definir o tamanho e a orientação do papel
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar o HTML como PDF
    $dompdf->render();

    // Limpa o buffer de saída e envia o PDF para o navegador
    ob_end_clean();  // Limpa o buffer de saída antes de enviar o PDF

    // Envia o PDF para o navegador sem o cabeçalho "attachment"
    $dompdf->stream("meu_arquivo.pdf", ["Attachment" => false]);
}

}
