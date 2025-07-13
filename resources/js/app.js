import Dropzone from "dropzone";
import { createApp } from 'vue';

window.Dropzone = Dropzone

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

app.mount('#app');
