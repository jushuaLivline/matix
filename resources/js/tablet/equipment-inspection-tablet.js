import {createApp} from 'vue';

import EquipmentInspectionTablet from '../components/EquipmentInspectionTablet.vue';

const app_equipment_inspection = createApp(EquipmentInspectionTablet).mount("#app-equipment-inspection");
window.app_equipment_inspection = app_equipment_inspection;