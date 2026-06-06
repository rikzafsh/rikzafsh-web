<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\PustakaKaryaModel;

class PustakaKaryaController extends BaseController
{
    protected $pustakaModel;

    public function __construct()
    {
        $this->pustakaModel = new PustakaKaryaModel();
    }

    public function simpan()
    {
        $fileCover = $this->request->getFile('cover_img');
        $namaCover = ($fileCover->isValid() && !$fileCover->hasMoved()) ? $fileCover->getRandomName() : null;
        if ($namaCover) $fileCover->move(FCPATH . 'uploads/covers', $namaCover);

        $this->pustakaModel->save([
            'codename'      => $this->request->getPost('codename'),
            'edisi'         => $this->request->getPost('edisi'),
            'tema'          => $this->request->getPost('tema'),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'warna'         => $this->request->getPost('warna'),
            'link'          => $this->request->getPost('link'),
            'cover_img'     => $namaCover
        ]);

        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil ditambahkan!');
    }

    public function update($id)
    {
        $majalahLama = $this->pustakaModel->find($id);
        $fileCover = $this->request->getFile('cover_img');
        $namaCover = $majalahLama['cover_img'];

        if ($fileCover->isValid() && !$fileCover->hasMoved()) {
            $namaCover = $fileCover->getRandomName();
            $fileCover->move(FCPATH . 'uploads/covers', $namaCover);
            if (!empty($majalahLama['cover_img']) && file_exists(FCPATH . 'uploads/covers/' . $majalahLama['cover_img'])) {
                unlink(FCPATH . 'uploads/covers/' . $majalahLama['cover_img']);
            }
        }

        $this->pustakaModel->update($id, [
            'codename'      => $this->request->getPost('codename'),
            'edisi'         => $this->request->getPost('edisi'),
            'tema'          => $this->request->getPost('tema'),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'warna'         => $this->request->getPost('warna'),
            'link'          => $this->request->getPost('link'),
            'cover_img'     => $namaCover
        ]);

        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil diperbarui!');
    }

    public function hapus($id)
    {
        $majalah = $this->pustakaModel->find($id);
        if ($majalah) {
            if (!empty($majalah['cover_img']) && file_exists(FCPATH . 'uploads/covers/' . $majalah['cover_img'])) {
                unlink(FCPATH . 'uploads/covers/' . $majalah['cover_img']);
            }
            $this->pustakaModel->delete($id);
        }
        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil dihapus!');
    }
}