<?php
class team_view {
	public static function qr_html($data) {
		$data['layout'] = 'htmlPlain';
		$data['template'] = 'team/qr';
		core::showHTML($data);
	}
	
	public static function read_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'team/read';
		core::showHTML($data);	
	}
	
	public static function qr_png($data) {
		include(dirname(__FILE__) . '/../vendor/phpqrcode/qrlib.php');
		 QRcode::png($data['url']);
	}
	public static function error_html($data) {
		$data['layout'] = 'htmlLayout';
		$data['template'] = 'error';
		core::showHTML($data);
	}
}
