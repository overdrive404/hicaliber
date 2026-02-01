import './bootstrap';
import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';
import PropertySearch from './components/PropertySearch.vue';

const app = createApp(PropertySearch);

app.use(ElementPlus);

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

app.mount('#app');
