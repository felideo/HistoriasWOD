<?php
namespace Libs;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
	private $from;
	private $replay_to;
	private $to = [];
	private $mensagem;
	private $assunto;
	private $cco;
	private $mail;
	private $sender;

	public function __construct(){

		// $this->get_sender();
	}

 	public function set_from($from){
		$this->from = $from;
		$this->sender['email'] = $from;
		return $this;
	}

	public function set_pass($pass){
		$this->sender['senha'] = $pass;
		return $this;
	}

	public function set_filter($filter){
		$this->filter = $filter;

		if(isset($this->to) && !empty($this->to)){
			foreach($this->to as $indice => $email){
				$email = explode('@', $email);
				$this->to[$indice] = $email[0] . '+' . $this->filter . '@' . $email[1];
			}
		}

		return $this;
	}

 	public function set_to($to){
		$this->to = explode(',', $to);
		return $this;
	}

 	public function set_mensagem($mensagem){
		$this->mensagem = $mensagem;
		return $this;
	}

 	public function set_assunto($assunto){
		$this->assunto = $assunto;
		return $this;
	}

 	public function send_mail(){
 		if(!isset($this->sender['email']) || empty($this->sender['email'])){
 			return false;
 		}

 		try {
			$this->mail = new PHPMailer(true);

 			// debug2($this->sender);
 			// debug2($this->to);

		    // $this->mail->SMTPDebug  = 2;
		    $this->mail->isSMTP();
		    $this->mail->Host       = 'smtp.gmail.com';
		    $this->mail->SMTPAuth   = true;
	    	$this->mail->Username   = $this->sender['email'];
		    $this->mail->Password   = $this->sender['senha'];
		    $this->mail->SMTPSecure = 'tls';
		    $this->mail->Port       = 587;
	    	$this->mail->setFrom($this->sender['email']);

 		    foreach($this->to as $indice => $email){
		    	$this->mail->addAddress($email);
 		    }

		    $this->mail->addReplyTo($this->sender['email']);
 		    $this->mail->isHTML(true);                                  // Set email format to HTML
		    $this->mail->Subject = $this->assunto;
		    $this->mail->Body    = $this->mensagem;



		    debug2($this->mail->send());
		} catch (Exception $e){
	        $error = [
	           'exception_msg' => $e->getMessage(),
	           'code'          => $e->getCode(),
	           'localizador'   => "Class => " . __CLASS__ . " - Function => " . __FUNCTION__ . "() - Line => " . __LINE__,
	           'line'          => $e->getLine(),
	           'file'          => $e->getFile(),
	           'backtrace'     => $e->getTraceAsString(),
	        ];

	        debug2($error);
	        exit;

            ob_start();

		    debug($error);

		    $error = ob_get_clean();

		    $error = str_replace('"', '\'', $error);

			$this->connect_query("UPDATE email_gratis SET bloqueado = 1 WHERE id = {$this->sender['id']}");
			$this->connect_query('UPDATE email_gratis SET info = "' . $error . '" WHERE id = ' . $this->sender["id"]);

			unset($this->sender);
			unset($this->mail);

			$this->get_sender();
			$this->send_mail();

			return true;
		}

		debug2('fim email');
	}

	public function connect_query($query, $fetch_type = 'FETCH_ASSOC'){
        $acessos_db =[
            'host'     => BD_SERVIDOR,
            'database' => BD_BASE,
            'user'     => BD_USUARIO,
            'pass'     => BD_SENHA
        ];

        $pdo = new PDO('mysql:dbname=' . $acessos_db['database'] . ";host=" . $acessos_db['host'], $acessos_db['user'], $acessos_db['pass']);
        $sql = $pdo->prepare($query);
        $sql->execute();
        return $sql->fetchAll(constant("\PDO::" . $fetch_type));
    }

    public function execute_query($query){
        $acessos_db =[
            'host'     => BD_SERVIDOR,
            'database' => BD_BASE,
            'user'     => BD_USUARIO,
            'pass'     => BD_SENHA
        ];

        $pdo = new PDO('mysql:dbname=' . $acessos_db['database'] . ";host=" . $acessos_db['host'], $acessos_db['user'], $acessos_db['pass']);
        $sql = $pdo->prepare($query);

        $retorno = [
            $sql->execute(),
            $sql->errorCode(),
            $sql->errorInfo()
        ];

        if(isset($retorno[2][2]) && !empty($retorno[2][2])){
            return [
                'error' => $retorno[2],
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            ];
        }

        return $retorno;
    }
}