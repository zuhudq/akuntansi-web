<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;

class Coa extends BaseController
{
    public function index()
    {
        $coaModel = new CoaModel();

        $data = [
            'title' => 'Chart of Accounts',
            'page_title' => 'Daftar Akun (Chart of Accounts)',
            'accounts' => $coaModel->findAll()
        ];
        return view('coa/index', $data);
    }


    public function new()
    {
        return view('coa/new');
    }

    public function create()
    {
        $data = $this->request->getPost();
        $coaModel = new CoaModel();
        $coaModel->save($data);
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $coaModel = new CoaModel();
        $data = [
            'account' => $coaModel->find($id)
        ];

        return view('coa/edit', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $coaModel = new CoaModel();
        $coaModel->update($id, $data);
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil diubah!');
    }

    public function delete($id)
    {
        $coaModel = new CoaModel();
        $coaModel->delete($id);
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil dihapus!');
    }
}
