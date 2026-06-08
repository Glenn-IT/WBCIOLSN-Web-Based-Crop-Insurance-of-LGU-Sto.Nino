<?php
// ============================================================
// Farm Controller
// POST   /api/farms
// GET    /api/farms
// GET    /api/farms/{id}
// PUT    /api/farms/{id}
// DELETE /api/farms/{id}
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/FarmModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class FarmController extends BaseController {
    private FarmModel $farms;

    public function __construct() {
        $this->farms = new FarmModel();
    }

    // GET /api/farms
    public function index(array $params): void {
        $auth = requireAuth();
        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();

        if (in_array($auth['role'], ['admin', 'agent'])) {
            $search = sanitize($_GET['search'] ?? '');
            $items  = $this->farms->getAllPaginated($offset, $perPage, $search);
            $total  = $this->farms->countAll($search);
        } else {
            $items = $this->farms->getByUser($auth['id']);
            $total = count($items);
        }

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/farms/{id}
    public function show(array $params): void {
        $auth = requireAuth();
        $id   = assertPositiveInt($params['id']);
        $farm = $this->farms->getWithDetails($id);
        if (!$farm) sendNotFound('Farm not found.');
        requireOwnerOrAdmin($auth, (int)$farm['user_id']);
        sendSuccess($farm);
    }

    // POST /api/farms
    public function store(array $params): void {
        $auth = requireAuth();
        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $this->validateOrFail($data, [
            'farm_name'      => 'required|max:150',
            'location'       => 'required|max:255',
            'area_hectares'  => 'required|numeric',
            'crop_type_id'   => 'required|numeric',
        ]);

        // ── Duplicate guard: same user, same location submitted within 60 seconds ──
        $recent = $this->farms->rawOne(
            "SELECT id FROM farms
             WHERE user_id = ? AND location = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 60 SECOND)
             LIMIT 1",
            [$auth['id'], sanitize($data['location'])]
        );
        if ($recent) {
            // Return the existing farm so the client can proceed to policy creation
            sendSuccess($this->farms->getWithDetails($recent['id']), 'Farm already registered.', 200);
            return;
        }

        $id = $this->farms->insert([
            'user_id'          => $auth['id'],
            'farm_name'        => sanitize($data['farm_name']),
            'application_type' => sanitize($data['application_type'] ?? ''),
            'farmer_category'  => sanitize($data['farmer_category']  ?? ''),
            'location'         => sanitize($data['location']),
            'province'         => sanitize($data['province']    ?? ''),
            'municipality'     => sanitize($data['municipality'] ?? ''),
            'barangay'         => sanitize($data['barangay']    ?? ''),
            'area_hectares'    => (float)$data['area_hectares'],
            'crop_type_id'     => (int)$data['crop_type_id'],
            'soil_type'        => sanitize($data['soil_type']   ?? ''),
            'irrigation'       => isset($data['irrigation']) ? (int)$data['irrigation'] : 0,
            'tenurial_status'  => sanitize($data['tenurial_status']  ?? ''),
            'planting_method'  => sanitize($data['planting_method']  ?? ''),
            'planting_date'    => sanitize($data['planting_date']    ?? ''),
            'harvest_date'     => sanitize($data['harvest_date']     ?? ''),
            'latitude'         => isset($data['latitude'])  && $data['latitude']  !== '' ? (float)$data['latitude']  : null,
            'longitude'        => isset($data['longitude']) && $data['longitude'] !== '' ? (float)$data['longitude'] : null,
        ]);

        $this->audit($auth['id'], 'create_farm', 'farms', "Created farm #$id");
        sendSuccess($this->farms->getWithDetails($id), 'Farm registered successfully.', 201);
    }

    // PUT /api/farms/{id}
    public function update(array $params): void {
        $auth = requireAuth();
        $id   = assertPositiveInt($params['id']);
        $farm = $this->farms->find($id);
        if (!$farm) sendNotFound('Farm not found.');
        requireOwnerOrAdmin($auth, (int)$farm['user_id']);

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, [
            'farm_name'     => 'required|max:150',
            'location'      => 'required|max:255',
            'area_hectares' => 'required|numeric',
            'crop_type_id'  => 'required|numeric',
        ]);

        $this->farms->update($id, [
            'farm_name'        => sanitize($data['farm_name']),
            'application_type' => sanitize($data['application_type'] ?? $farm['application_type']),
            'farmer_category'  => sanitize($data['farmer_category']  ?? $farm['farmer_category']),
            'location'         => sanitize($data['location']),
            'province'         => sanitize($data['province']    ?? $farm['province']),
            'municipality'     => sanitize($data['municipality'] ?? $farm['municipality']),
            'barangay'         => sanitize($data['barangay']    ?? $farm['barangay']),
            'area_hectares'    => (float)$data['area_hectares'],
            'crop_type_id'     => (int)$data['crop_type_id'],
            'soil_type'        => sanitize($data['soil_type']   ?? $farm['soil_type']),
            'irrigation'       => isset($data['irrigation']) ? (int)$data['irrigation'] : $farm['irrigation'],
            'tenurial_status'  => sanitize($data['tenurial_status']  ?? $farm['tenurial_status']),
            'planting_method'  => sanitize($data['planting_method']  ?? $farm['planting_method']),
            'planting_date'    => sanitize($data['planting_date']    ?? $farm['planting_date']),
            'harvest_date'     => sanitize($data['harvest_date']     ?? $farm['harvest_date']),
        ]);

        $this->audit($auth['id'], 'update_farm', 'farms', "Updated farm #$id");
        sendSuccess($this->farms->getWithDetails($id), 'Farm updated successfully.');
    }

    // DELETE /api/farms/{id}
    public function destroy(array $params): void {
        $auth = requireAuth();
        $id   = assertPositiveInt($params['id']);
        $farm = $this->farms->find($id);
        if (!$farm) sendNotFound('Farm not found.');
        requireOwnerOrAdmin($auth, (int)$farm['user_id']);

        $this->farms->delete($id);
        $this->audit($auth['id'], 'delete_farm', 'farms', "Deleted farm #$id");
        sendSuccess(null, 'Farm removed successfully.');
    }
}
