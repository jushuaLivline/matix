import {createApp} from 'vue';

import EquipmentInspectionEdit from '../components/EquipmentInspectionEdit.vue';

const app_equipment_inspection = createApp(EquipmentInspectionEdit, {
    userId: document.getElementById('app-equipment-inspection').getAttribute('data-user-id'),
    currentUrl: document.getElementById('app-equipment-inspection').getAttribute('data-current-url'),
    fileID: document.getElementById('app-equipment-inspection').getAttribute('data-file-id'),
}).mount("#app-equipment-inspection");
window.app_equipment_inspection = app_equipment_inspection;