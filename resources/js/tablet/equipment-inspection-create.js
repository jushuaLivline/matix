import {createApp} from 'vue';

import EquipmentInspectionCreate from '../components/EquipmentInspectionCreate.vue';

const app_equipment_inspection = createApp(EquipmentInspectionCreate, {
    userId: document.getElementById('app-equipment-inspection').getAttribute('data-user-id'),
    currentUrl: document.getElementById('app-equipment-inspection').getAttribute('data-current-url'),
}).mount("#app-equipment-inspection");
window.app_equipment_inspection = app_equipment_inspection;