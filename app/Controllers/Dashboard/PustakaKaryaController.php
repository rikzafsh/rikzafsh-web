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

    private function aturan(): array
    {
        return [
            'codename'      => 'required|max_length[50]',
            'edisi'         => 'required|is_natural_no_zero',
            'tema'          => 'required|max_length[200]',
            'tanggal_rilis' => 'required|valid_date[Y-m-d]',
            'warna'         => 'required|regex_match[/^#[0-9a-fA-F]{6}$/]',
            'link'          => 'permit_empty|valid_url_strict[https,http]|max_length[500]',
            'cover_img'     => 'max_size[cover_img,5120]|is_image[cover_img]|mime_in[cover_img,image/jpg,image/jpeg,image/png,image/webp]',
        ];
    }

    private function dataForm(?string $cover): array
    {
        return [
            'codename'      => trim((string) $this->request->getPost('codename')),
            'edisi'         => (int) $this->request->getPost('edisi'),
            'tema'          => trim((string) $this->request->getPost('tema')),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'warna'         => $this->request->getPost('warna'),
            'link'          => trim((string) $this->request->getPost('link')),
            'cover_img'     => $cover,
        ];
    }

    public function simpan()
    {
        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $fileCover = $this->request->getFile('cover_img');
        $namaCover = null;

        if ($fileCover && $fileCover->isValid() && ! $fileCover->hasMoved()) {
            $namaCover = $fileCover->getRandomName();
            $fileCover->move(FCPATH . 'uploads/covers', $namaCover);
        }

        $this->pustakaModel->save($this->dataForm($namaCover));

        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil ditambahkan!');
    }

    public function update($id)
    {
        $majalahLama = $this->pustakaModel->find($id);

        if (! $majalahLama) {
            return redirect()->to('dashboard/projects')->with('error', 'Edisi tidak ditemukan.');
        }

        if (! $this->validate($this->aturan())) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $fileCover = $this->request->getFile('cover_img');
        $namaCover = $majalahLama['cover_img'];

        if ($fileCover && $fileCover->isValid() && ! $fileCover->hasMoved()) {
            $namaCover = $fileCover->getRandomName();
            $fileCover->move(FCPATH . 'uploads/covers', $namaCover);

            if (! empty($majalahLama['cover_img']) && is_file(FCPATH . 'uploads/covers/' . $majalahLama['cover_img'])) {
                unlink(FCPATH . 'uploads/covers/' . $majalahLama['cover_img']);
            }
        }

        $this->pustakaModel->update($id, $this->dataForm($namaCover));

        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil diperbarui!');
    }

    public function hapus($id)
    {
        $majalah = $this->pustakaModel->find($id);
        if ($majalah) {
            if (!empty($majalah['cover_img']) && is_file(FCPATH . 'uploads/covers/' . $majalah['cover_img'])) {
                unlink(FCPATH . 'uploads/covers/' . $majalah['cover_img']);
            }
            $this->pustakaModel->delete($id);
        }
        return redirect()->to('dashboard/projects')->with('message', 'Edisi berhasil dihapus!');
    }
}