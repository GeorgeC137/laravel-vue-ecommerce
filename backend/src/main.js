import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import store from './store'
import router from './router'
import currencyUSD from './filters/currency'
import CKEditor from '@ckeditor/ckeditor5-vue' 

const app = createApp(App)
app
    .use(store)
    .use(router)
    .use(CKEditor)
    .mount('#app')
;

app.config.globalProperties.$filters = {
    currencyUSD
}
    
