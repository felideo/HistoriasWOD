<?php
namespace Model;

class Cron extends \Framework\Model{
	public function get_crons(){
		return $this->query->select('
				cron.*
			')
			->from('gerenciador_cron cron')
			->where('horario < "' . date("Y-m-d H:i:s") . '"')
			->andWhere('ativo = 1')
			->fetchArray();
	}
}
