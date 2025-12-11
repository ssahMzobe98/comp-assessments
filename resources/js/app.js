import './bootstrap';
import { createApp } from 'vue'
import LearnerProgress from './components/LearnerProgress.vue'

const app = createApp({})
console.log('Learner Progress App')
app.component('learner-progress', LearnerProgress)
console.log('mounting #app')
app.mount('#app')
