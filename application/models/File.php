<?php
class Model_File extends Core_Model
{
	public function createFiles ($urls) 
	{
		if($urls) {
			$curlModel = new Model_Curl();
			$data = array();

			foreach($urls as $id => $url) {
				$fileName = substr(strrchr($urls[$id], '/'), 1);
				$tmpfname = tempnam(sys_get_temp_dir(), $fileName);
				$handle = fopen($tmpfname, "w");
				fwrite($handle, $curlModel->curlRequestForFiles($urls[$id]));
				$data[$fileName] = file($tmpfname);
				fclose($handle);
				unlink($tmpfname);
			}

			return $data;
		}

		return null;
	}
}