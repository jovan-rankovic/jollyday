import Vue from 'vue'
import VCalendar from 'v-calendar'

// Use v-calendar & v-date-picker components
Vue.use(VCalendar, {
  locales: {
    'en-EN': {
      firstDatOfWeek: 1,
      masks: {
        L: 'DD-MM-YYYY'
      }
    }
  }
})
