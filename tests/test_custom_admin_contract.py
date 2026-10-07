import unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
class CustomAdminContract(unittest.TestCase):
    def test_live_ui_isolated_from_demo_actions(self):
        s=(ROOT/'platform/frontend/src/features/admin/components/AdminDashboard.vue').read_text()
        self.assertIn('<template v-if="live">',s)
        self.assertIn('<BackendCustomersPanel v-else-if="activeTab===\'customers\'"/>',s)
        self.assertIn('<BackendUsersPanel v-else-if="activeTab===\'users\'"/>',s)
        self.assertIn('<template v-else>',s)
        live=(ROOT/'platform/frontend/src/features/admin/components/BackendCustomersPanel.vue').read_text()
        self.assertNotIn('useCustomersStore',live)
        self.assertNotIn('localStorage',live)
        self.assertIn('updateAdminCustomer',live)
        self.assertIn('fetchAdminCustomers',live)
        self.assertNotIn('loginPassword',live)
        self.assertNotIn('impersonate',live)
    def test_routes_keep_real_permission_boundary(self):
        s=(ROOT/'platform/backend/routes/web.php').read_text()
        self.assertIn("Route::prefix('api/admin')->middleware(['active.admin', 'throttle:60,1'])",s)
        self.assertIn("Route::patch('/customers/{customer}'",s)
