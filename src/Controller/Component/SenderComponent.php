<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Componente de envio de e-mail e outras ferramentas de envio.
 * @package App\Controller\Component
 */
class SenderComponent extends Component
{
    /**
     * Envia um e-mail simples
     * @param string $name Nome do remetente
     * @param string $from Endereço de e-mail do remetente
     * @param string $to Endereço de e-mail do destinatário
     * @param string $subject Assunto da mensagem
     * @param string $message Mensagem
     * @return array Resultado do envio
     */
    public function sendEmail($name, $from, $to, $subject, $message)
    {
        $email = new Email('default');
        $email->from([$from => $name]);
        $email->to($to);
        $email->subject($subject);

        $headMail = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject
        ];

        $this->registrarLog($headMail);

        return $email->send($message);
    }

    /**
     * Envia um e-mail baseado num template
     * @param array $headMail Cabeçalho do e-mail
     * @param string $template Nome do template do envio do e-mail
     * @param array $params Parâmetros para serem usados no template do e-mail
     * @return array Resultado do envio de e-mail
     */
    public function sendEmailTemplate($headMail, $template, $params = NULL)
    {
        $email = new Email('default');
        $email->template($template);
        $email->emailFormat("html");
        $email->helpers(['Html', 'Url']);
        $email->from([$headMail["from"] => $headMail["name"]]);
        $email->replyTo($headMail["from"]);

        if(is_array($headMail["to"]))
        {
            $i = 0;
            $email->to($headMail["to"][$i]);

            $i = $i + 1;

            while($i < count($headMail["to"]))
            {
                $email->addTo($headMail["to"][$i]);
                $i = $i + 1;
            }
        }
        else
        {
            $email->to($headMail["to"]);
        }

        $email->subject($headMail["subject"]);
        $email->viewVars($params);

        $this->registrarLog($headMail);

        return $email->send();
    }

    /**
     * Envia um e-mail interno do sistema
     * @param string $from Rementente do sistema ou nulo, caso seja do sistema
     * @param string $to Destinatário(s) do sistema ou nulo, caso seja para todos
     * @param string $subject Assunto da mensagem
     * @param string $message Mensagem
     * @param int $relacionado Mensagem relacionada, usada para respostas ou encaminhamentos.
     */
    public function sendMessage($from, $to, $subject, $message, $relacionado = null)
    {
        $destinatarios = null;
        $table = TableRegistry::get('Mensagem');

        if(is_array($to))
        {
            $destinatarios = $to;
        }
        else
        {
            $destinatarios = [$to];
        }

        foreach ($destinatarios as $destinatario)
        {
            $mensagem = $table->newEntity();
            $mensagem->rementente = $from;
            $mensagem->destinatario = $destinatario;
            $mensagem->data = date("Y-m-d H:i:s");
            $mensagem->titulo = $subject;
            $mensagem->mensagem = $message;
            $mensagem->lido = false;
            $mensagem->relacionado = $relacionado;

            $table->save($mensagem);
        }
    }

    /**
     * Faz o registro de log de envio de e-mails
     * @param array $headMail Informações do cabeçalho do e-mail
     */
    private function registrarLog($headMail)
    {
        Log::notice('Date: ' . date('d/m/Y H:i:s') .  '; From: ' . $headMail["from"] . '; To: ' . json_encode($headMail["to"]) . '; IP:' . $_SERVER['REMOTE_ADDR'] . '; Assunto:' . $headMail["subject"], ['mail']);
    }
}
