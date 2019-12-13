<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'index',
		'name'		=> 'Index',
		'send'		=> 'Index'
	];

	public function index($parametros){
		$this->view->assign('campeao', $parametros[0]);
		$this->view->render('', $this->modulo['modulo'] . '/view/index');
	}

	public function index_fernanda(){
		$folder = '/uploads/MR CONTADOR -- 12_-be27cf5e';
		// $url = 'http://teste.dv/monta_imagens_lado_a_lado_para_impressao/' . $folder . '/';

		$sem_margem = [0, 3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36, 39, 42, 45, 48, 51, 54, 57, 60, 63, 66, 69, 72, 75, 78, 81, 84, 87, 90, 93, 96, 99, 102, 105, 108, 111, 114, 117, 120, 123, 126, 129, 132, 135, 138, 141, 144, 147, 150, 153, 156, 159, 162, 165, 168, 171, 174, 177, 180, 183, 186, 189, 192, 195, 198, 201, 204, 207, 210, 213, 216, 219, 222, 225, 228, 231, 234, 237, 240, 243, 246, 249, 252, 255, 258, 261, 264, 267, 270, 273, 276, 279, 282, 285, 288, 291, 294, 297, 300, 303, 306, 309, 312, 315, 318, 321, 324, 327, 330, 333, 336, 339, 342, 345, 348, 351, 354, 357, 360, 363, 366, 369, 372, 375, 378, 381, 384, 387, 390, 393, 396, 399, 402, 405, 408, 411, 414, 417, 420, 423, 426, 429, 432, 435, 438, 441, 444, 447, 450, 453, 456, 459, 462, 465, 468, 471, 474, 477, 480, 483, 486, 489, 492, 495, 498, 501, 504, 507, 510, 513, 516, 519, 522, 525, 528, 531, 534, 537, 540, 543, 546, 549, 552, 555, 558, 561, 564, 567, 570, 573, 576, 579, 582, 585, 588, 591, 594];
		$imagens    = scandir($_SERVER['DOCUMENT_ROOT'] . $folder);

		$imagens = scandir($_SERVER['DOCUMENT_ROOT'] . $folder);

		foreach($imagens as $indice => $image){
			if($image == '.' || $image == '..' || $image == 'depois'){
				unset($imagens[$indice]);
				continue;
			}
		}

		$imagens = array_values($imagens);

		foreach($imagens as $indice => $image){
			unset($imagens[$indice]);
			$imagens[$indice] = [
				'imagem' => $folder . '/' . $image,
				'class'  => ((in_array($indice, $sem_margem)) ? ' nao ' : ' sim '),

				'p1'     => [
					'id' => 592089,
				],

				'p2'     => [
					'frame' => $indice + 1,
					'total' => count($imagens) + 1,
					// 'coisa' => 'MR (256x256) - AXIAL',
					// 'coisa' => 'MR (540x640) - AXIAL',
					// 'coisa' => 'MR (580x640) - AXIAL',
					// 'coisa' => 'MR (580x640) - CORONAL',
					// 'coisa' => 'MR (512x512) - AXIAL',
					// 'coisa' => 'MR (384x384) - SAGITAL',


					// 'coisa' => 'MR (768x768) - SAGITAL',
					// 'coisa' => 'MR (448x462) - SAGITAL',
					// 'coisa' => 'MR (768x792) - SAGITAL',




					// 'coisa' => 'MR (256x256) - OBLIQUE',
					// 'coisa' => 'MR (600x640) - OBLIQUE',
					// 'coisa' => 'MR (640x640) - OBLIQUE',
					// 'coisa' => 'MR (640x640) - SAGITAL',
					// 'coisa' => 'MR (640x640) - AXIAL',
					// 'coisa' => 'MR (640x580) - AXIAL',
					'coisa' => 'MR (1115x2168) - SAGITAL',



				],

				'p3'     => [
					'data'     => '19 de ago de 2019',
					'acnb'     => '228528',
					// 'parte' => 'OMBRO^ESQUERDO',
					// 'parte' => 'OMBRO^DIREITO',
					// 'parte' => 'JOELHO^ESQUERDO',
					// 'parte' => 'JOELHO^DIREITO',
					'parte'    => 'COLUNA^TOTAL',


					// 'id'    => 1,
					'id'    => 'MR20190819173633',

				],

				'p4'     => [
					// 'laterality' => 'R',
					// 'laterality' => 'R',

					// 'serie'  => 3,
					// 'serie'  => 4,
					// 'serie'  => 6,
					// 'serie'  => 6,
					// 'serie'  => 7,
					// 'serie'  => 8,
					// 'serie'  => 11,
					// 'serie'  => 12,
					// 'serie'  => 13,
					// 'serie'  => 14,
					// 'serie'  => 15,
					'serie'  => 100,








					// 'nome'   => 'AXIAL T2 fs blade',
					// 'nome'   => 'SAGITAL T1',
					// 'nome'   => 'SAGITAL T2 fs blade',
					// 'nome'   => 'CORONAL T2_',
					// 'nome'   => 'CORONAL T2  fs blade',
					// 'nome'   => 'CORONAL T1',
					// 'nome'   => 'CORONAL T2 FS',
					// 'nome'   => 'CC SAGITAL T2',
					// 'nome'   => 'CC SAGITAL T1',
					// 'nome'   => 'CC AXIAL T2',
					// 'nome'   => 'CT SAGITAL T2',
					// 'nome'   => 'CT SAGITAL T2 FS',
					// 'nome'   => 'CT SAGITAL T1',
					// 'nome'   => 'CL SAGITAL T2',
					// 'nome'   => 'CL SAGITAL T1',
					// 'nome'   => 'CT AXIAL T2 blocos',
					// 'nome'   => 'CT AXIAL T2 disco',
					'nome'   => 'CONTADOR',











					// 'nome'   => 'SAGITAL DP FS',
					// 'nome'      => 'AXIAL T2 FS',

					// 'thickness' => '3,5 mm'
					// 'thickness' => '3 mm'
					// 'thickness' => '3,3 mm'
					// 'thickness' => '4 mm'
					// 'thickness' => '4,5 mm'
					'thickness' => '3,63 mm'


				],
			];
		}

		$this->view->assign('imagens', $imagens);
		$this->view->render('', $this->modulo['modulo'] . '/view/index');
		exit;
	}
}