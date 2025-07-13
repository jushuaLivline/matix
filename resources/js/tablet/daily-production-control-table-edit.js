import {createApp} from 'vue';

import DailyProductionEdit from '../components/DailyProductionEdit.vue';

const app_equipment_inspection = createApp(DailyProductionEdit).mount("#app-daily-production");
window.app_equipment_inspection = app_equipment_inspection;