import {createApp} from 'vue';

import DailyProductionReference from '../components/DailyProductionReference.vue';

const app_equipment_inspection = createApp(DailyProductionReference).mount("#app-daily-production");
window.app_equipment_inspection = app_equipment_inspection;