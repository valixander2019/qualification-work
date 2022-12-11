<template>
  <admin-form endpoint="/api/admin/tasks"
              route-name="admin.tasks"
              :form="defaultForm"
              :is-creating="isCreating"
  >
    <template #default="{ form }">
      <b-form-group :label="$t('is_active')"
                    label-for="is_active"
      >
        <b-form-checkbox id="is_active"
                         v-model="form.is_active"
                         size="lg"
                         switch
        />
      </b-form-group>
      <b-form-group :label="'Приоритет'"
                    label-for="order"
      >
        <b-form-input id="order"
                      v-model="form.order"
                      type="number"
        />
      </b-form-group>
      <b-form-group :label="'Раздел'"
                    label-for="section_id"
      >
        <b-form-select
          id="section_id"
          v-model="form.section_id"
          :options="sections"
          required
        >
          <template #first>
            <b-form-select-option :value="null" disabled>
              -- Выберите раздел --
            </b-form-select-option>
          </template>
        </b-form-select>
      </b-form-group>
      <b-form-group :label="'Заголовок'"
                    label-for="title"
      >
        <b-form-input id="title"
                      v-model="form.title"
                      :placeholder="'Заголовок'"
                      required
        />
      </b-form-group>
      <b-form-group :label="'Обработчик'"
                    label-for="code"
      >
        <b-form-textarea id="code"
                         v-model="form.code"
                         :placeholder="'Обработчик'"
                         style="height: 500px !important;"
                         required
        />
      </b-form-group>
      <b-form-group :label="'Описание'"
                    label-for="description"
      >
        <b-form-textarea id="description"
                         v-model="form.description"
                         :placeholder="'Описание'"
        />
      </b-form-group>
      <b-form-group>
        <vue-mathjax :formula="form.description" class="d-block formula" />
      </b-form-group>
    </template>
  </admin-form>
</template>

<script>
import AdminForm from '~/components/admin/Form'
import axios from 'axios'

export default {
  components: {
    AdminForm
  },

  props: {
    isCreating: {
      type: Boolean,
      default: false
    }
  },

  data: () => ({
    defaultForm: {
      is_active: true,
      order: 500,
      section_id: null,
      code: ''
    },
    sections: []
  }),

  async mounted () {
    try {
      const { data } = await axios.get('/api/admin/sections/tree')

      this.sections = data.data
    } catch (e) {
      // TODO: Обработать ошибку
    }
  }
}
</script>
