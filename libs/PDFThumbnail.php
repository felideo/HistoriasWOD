<?php
namespace Libs;

//https://github.com/spatie/pdf-to-image
class PDFThumbnail {
	public static function creatThumbnail($path_to_pdf){
		$thumb_name = \Libs\Hash::get_unic_hash();

		$thumb = new \Imagick();
		$thumb->readImage($path_to_pdf . "[0]");
		$thumb->setResolution(10, 10);
		$thumb->setimageformat("jpg");
		$thumb->setImageFormat('jpg');
		$thumb->writeimage('uploads/thumbnails/' . $thumb_name . '.jpg');
		$thumb->clear();
		$thumb->destroy();

		return 'uploads/thumbnails/' . $thumb_name . '.jpg';
	}
}

