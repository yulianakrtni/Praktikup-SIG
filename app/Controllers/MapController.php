<?php

namespace App\Controllers;

use App\Models\LokasiModel;
use CodeIgniter\RESTful\ResourceController;

class MapController extends ResourceController
{
    public function getLocations()
    {
        $model = new LokasiModel();
        $data = $model->findAll();

        return $this->respond($data);
    }

    public function addLocation()
    {
        $model = new LokasiModel();

        $newData = [
            'nama' => $this->request->getVar('nama'),
            'latitude' => $this->request->getVar('latitude'),
            'longitude' => $this->request->getVar('longitude'),
            'keterangan' => $this->request->getVar('keterangan'),
        ];

        $model->insert($newData);

        return $this->respondCreated(['message' => 'Location added successfully']);
    }

    public function editLocation($id = null)
    {
        $model = new LokasiModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->failNotFound('Location not found');
        }

        $updatedData = [
            'nama' => $this->request->getVar('nama'),
            'latitude' => $this->request->getVar('latitude'),
            'longitude' => $this->request->getVar('longitude'),
            'keterangan' => $this->request->getVar('keterangan'),
        ];

        $model->update($id, $updatedData);

        return $this->respondUpdated(['message' => 'Location updated successfully']);
    }

    public function deleteLocation($id = null)
    {
        $model = new LokasiModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->failNotFound('Location not found');
        }

        $model->delete($id);

        return $this->respondDeleted(['message' => 'Location deleted successfully']);
    }
}
