<template>
  <div v-if="!isLoading" class="container container-width">
    <admin-breadcrumb />

    <admin-title :classes="{ h2: true }">
      <span class="task-date">
        <b-icon-calendar /> {{ task.updated_at }}
      </span>
    </admin-title>

    <div v-if="!isHandlerLoading">
      <vue-mathjax :formula="task.description"
                   class="d-block formula"
                   disabled
      />

      <div class="math-list-bg px-4 py-5">
        <vue-mathjax :formula="handler.formula"
                     class="d-block formula equation"
                     disabled
        />
      </div>

      <hr>

      <div class="d-flex justify-content-center text-center">
        <b-form @submit.prevent="onSubmit">
          <div v-for="(field, idx) in handler.fields"
               :key="idx"
          >
            <b-form-group v-if="field.type === 'mixed-fraction'"
                          :label="field.label + ':'"
                          label-size="lg"
                          label-cols="2"
                          label-align="right"
                          label-class="pt-0 d-flex align-items-center"
                          class="fraction"
            >
              <b-row>
                <b-col md="6" class="d-flex justify-content-center align-items-center pr-0">
                  <b-form-input
                    type="text"
                    placeholder="Целая часть"
                    @input="handleFractionInput($event, idx, 2)"
                    class="mb-1 numerator"
                  />
                </b-col>
                <b-col md="6">
                  <b-form-input
                    type="text"
                    placeholder="Числитель"
                    @input="handleFractionInput($event, idx, 0)"
                    class="mb-1 numerator"
                    required
                  />

                  <hr class="fraction-line my-2 mx-0" />

                  <b-form-input
                    type="text"
                    placeholder="Знаменатель"
                    @input="handleFractionInput($event, idx, 1)"
                    class="denominator"
                    required
                  />
                </b-col>
              </b-row>
            </b-form-group>
            <b-form-group v-else-if="field.type === 'fraction'"
                          :label="field.label + ':'"
                          label-size="lg"
                          label-cols="2"
                          label-align="right"
                          label-class="pt-0 d-flex align-items-center"
                          class="fraction"
            >
              <b-row>
                <b-col md="6">
                  <b-form-input
                    type="text"
                    placeholder="Числитель"
                    @input="handleFractionInput($event, idx, 0)"
                    class="mb-1 numerator"
                    required
                  />

                  <hr class="fraction-line my-2 mx-0" />

                  <b-form-input
                    type="text"
                    placeholder="Знаменатель"
                    @input="handleFractionInput($event, idx, 1)"
                    class="denominator"
                    required
                  />
                </b-col>
              </b-row>
            </b-form-group>
            <b-form-group v-else-if="field.type === 'string'"
                          :label="field.label + ':'"
                          label-size="lg"
                          label-cols="2"
                          label-align="right"
                          label-class="pt-0 d-flex"
            >
              <b-form-input
                type="text"
                placeholder="Выражение"
                @input="handleStringInput($event, idx)"
                class="integer"
                required
              />
            </b-form-group>
            <b-form-group v-else
                          :label="field.label + ':'"
                          label-size="lg"
                          label-cols="2"
                          label-align="right"
                          label-class="pt-0 d-flex"
            >
              <b-form-input
                type="text"
                placeholder="Число"
                @input="handleIntegerInput($event, idx)"
                class="integer"
                required
              />
            </b-form-group>
          </div>

          <b-button type="submit" variant="primary" :disabled="isProcessing" class="mt-3">
            <b-spinner v-show="isProcessing" small />
            Отправить решение
          </b-button>
        </b-form>
      </div>
    </div>
    <Loader v-else />
  </div>
  <Loader v-else />
</template>

<script>
import axios from 'axios'
import AdminBreadcrumb from '~/components/admin/Breadcrumb'
import AdminTitle from '~/components/admin/Title'
import Loader from '../../components/Loader'

export default {
  components: {
    AdminBreadcrumb,
    AdminTitle,
    Loader
  },

  middleware: 'auth',

  data: () => ({
    endpoint: '/api/tasks/',
    isLoading: false,
    isHandlerLoading: false,
    isProcessing: false,
    task: {},
    handler: {},
    answers: {}
  }),

  computed: {
    taskHandler () {
      return Object.keys(this.task).length > 0 ? this.task.task_handler.handler : null
    }
  },

  async mounted () {
    this.isLoading = true

    await this.fetchData()

    this.isLoading = false
  },

  methods: {
    handleIntegerInput (value, idx) {
      this.$set(this.answers, idx, Number(value))
    },

    handleFractionInput (value, idx, key) {
      const fraction = this.answers[idx] ?? []

      console.log()

      if (value.length) fraction[key] = Number(value)
      else fraction.splice(key, 1)

      this.$set(this.answers, idx, fraction)
    },

    handleStringInput (value, idx) {
      this.$set(this.answers, idx, value)
    },

    async fetchData () {
      this.isHandlerLoading = true

      try {
        const { data } = await axios.get(this.endpoint + this.$route.params.id)
        const bradcrumb = data.data.task.breadcrumb

        this.task = data.data.task
        this.handler = data.data.handler

        console.log('Answers:', this.handler.answers)

        bradcrumb.unshift(
          { name: 'home', link: { name: 'home' }, icon: 'house-fill' },
          { name: 'sections', link: { name: 'sections' } }
        )

        this.$route.meta.breadcrumb = bradcrumb
      } catch (e) {
        // TODO: Обработать ошибку
      }

      this.isHandlerLoading = false
    },

    async onSubmit (event) {
      this.isProcessing = true

      try {
        const { data } = await axios.post(this.endpoint + this.$route.params.id, {
          answers: Object.values(this.answers)
        })

        if (data.data.isCorrect) {
          await this.$bvModal.msgBoxOk(this.$t('correct_and_next') + '', {
            title: this.$t('correct'),
            size: 'md',
            buttonSize: 'md',
            okVariant: 'success',
            okTitle: this.$t('go_on'),
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          })

          await this.fetchData()
        } else {
          await this.$bvModal.msgBoxOk(this.$t('try_more') + '', {
            title: this.$t('wrong'),
            size: 'md',
            buttonSize: 'md',
            okVariant: 'danger',
            okTitle: this.$t('go_on'),
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          })
        }
      } catch (e) {
        // TODO: Обработать ошибку
      }

      this.isProcessing = false
    }
  }
}
</script>
