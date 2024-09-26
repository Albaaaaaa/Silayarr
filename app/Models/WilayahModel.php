<?php

namespace App\Models;

class WilayahModel extends \App\Models\BaseModel
{
	private $fotoPath;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getPropinsi() {
		$sql = 'SELECT * FROM wilayah_propinsi';
		$query = $this->db->query($sql)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_wilayah_propinsi']] = $val['nama_propinsi'];
		}
		return $result;
	}
	
	public function getKabupatenByIdPropinsi($id_propinsi) {
		$sql = 'SELECT * FROM wilayah_kabupaten WHERE id_wilayah_propinsi = ?';
		$query = $this->db->query($sql, $id_propinsi)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_wilayah_kabupaten']] = $val['nama_kabupaten'];
		}
		return $result;
	}
	
	public function getKecamatanByIdKabupaten($id_kabupaten) {
		$sql = 'SELECT * FROM wilayah_kecamatan WHERE id_wilayah_kabupaten = ?';
		$query = $this->db->query($sql, $id_kabupaten)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_wilayah_kecamatan']] = $val['nama_kecamatan'];
		}
		return $result;
	}
	
	public function getKelurahanByIdKecamatan($id_kecamatan) {
		$sql = 'SELECT * FROM wilayah_kelurahan WHERE id_wilayah_kecamatan = ?';
		$query = $this->db->query($sql, $id_kecamatan)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_wilayah_kelurahan']] = $val['nama_kelurahan'];
		}
		return $result;
	}
	
	public function getKecamatanByIdKelurahan($id_kelurahan) {
		if (empty($id_kelurahan)) {
			
			$sql = 'SELECT COUNT(*) as jml FROM wilayah_kecamatan';
			$result = $this->db->query($sql)->getRowArray();
			
			$sql = 'SELECT * FROM wilayah_kecamatan LIMIT ' . ceil($result['jml']/2) . ',1';
			$result = $this->db->query($sql)->getRowArray();
			
		} else {
			$sql = 'SELECT * FROM wilayah_kecamatan 
						LEFT JOIN wilayah_kelurahan USING(id_wilayah_kecamatan) 
						WHERE id_wilayah_kelurahan = ?';
			$result = $this->db->query($sql, $id_kelurahan)->getRowArray();
		}
		return $result;
	}
	
	public function getKabupatenByIdKecamatan($id_kecamatan) {
		$sql = 'SELECT * FROM wilayah_kabupaten 
					LEFT JOIN wilayah_kecamatan USING(id_wilayah_kabupaten) 
					WHERE id_wilayah_kecamatan = ?';
		$result = $this->db->query($sql, $id_kecamatan)->getRowArray();
		return $result;
	}
	
	public function getPropinsiByIdKabupaten($id_kabupaten) {
		$sql = 'SELECT * FROM wilayah_propinsi 
					LEFT JOIN wilayah_kabupaten USING(id_wilayah_propinsi) 
					WHERE id_wilayah_kabupaten = ?';
		$result = $this->db->query($sql, $id_kabupaten)->getRowArray();
		return $result;
	}
}
?>