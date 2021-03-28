<template>
  <div>
    <v-date-picker
      v-model="range"
      is-range
      locale="en-EN"
      :columns="layout.columns"
      :rows="layout.rows"
      :is-expanded="layout.isExpanded"
      :disabled-dates="disabledDates"
      :max-date="maxDate"
    />

    <v-button native-type="button" large="" block="" @click.native="submitDays()">
      Submit
    </v-button>

    <span class="text-center error">{{ error }}</span>

    <div class="card text-center mt-4">
      <div class="card-header">
        Holiday days remaining
      </div>
      <div class="card-body">
        <h1>{{ daysRemaining }}</h1>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import VButton from '../components/Button'

export default {
  components: { VButton },
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data () {
    return {
      error: '',
      daysRemaining: 0,
      maxDate: new Date(new Date().getFullYear(), 11, 31),
      disabledDates: [{ weekdays: [1, 7] }],
      range: {
        start: new Date(),
        end: new Date()
      }
    }
  },

  computed: {
    layout () {
      return this.$screens(
        {
          // Default layout for mobile
          default: {
            columns: 1,
            rows: 1,
            isExpanded: true
          }
          // Override for large screens
          // lg: {
          //   columns: 2,
          //   rows: 2,
          //   isExpanded: false
          // }
        }
      )
    }
  },

  mounted () {
    this.getRemainingDays()
    this.getHolidayDates()
  },

  methods: {
    getRemainingDays () {
      axios
        .get('/api/remaining_days')
        .then(response => {
          this.daysRemaining = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },

    getHolidayDates () {
      axios
        .get('/api/holiday_dates')
        .then(response => {
          response.data.forEach(element => this.disabledDates.push(element))
        })
        .catch(error => {
          console.log(error)
        })
    },

    // setMaxDate () {
    //   this.maxDate = addDays(this.range.start, this.daysRemaining)
    // },

    submitDays () {
      const daysSelected = dateDiffInDays(this.range.start, this.range.end)

      if (daysSelected > this.daysRemaining) {
        this.error = 'Not enough days remaining.'
        return
      }

      axios
        .post('/api/submit_days', {
          start_date: this.range.start,
          end_date: this.range.end
        })
        .then(() => {
          this.error = ''
          this.getRemainingDays()
          this.getHolidayDates()
        })
        .catch(error => {
          console.log(error)
        })
    }
  }
}

const _MS_PER_DAY = 1000 * 60 * 60 * 24

function dateDiffInDays (a, b) {
  // Discard the time and time-zone information.
  const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate())
  const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate())

  return Math.floor((utc2 - utc1) / _MS_PER_DAY)
}

// function addDays (date, days) {
//   const result = new Date(date)
//   result.setDate(result.getDate() + days)
//   return result
// }
</script>

<style>
  .error {
    color: red;
  }
</style>
