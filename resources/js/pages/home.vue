<template>
  <div>
    <b-jumbotron>
      <template #lead>
        <div class="container">
          <b-row>
            <b-col class="d-flex justify-content-center align-items-center">
              <div>
                <h1 class="mb-3">
                  <b>Математика — это просто</b>
                </h1>

                <p>
                  Научитесь решать различные математические задачи с помощью нашего сервиса. Ознакомьтесь с теорией, а затем приступайте к решению примеров.
                </p>

                <p>
                  Задачи генерируются и проверяются автоматически.
                </p>

                <b-button variant="outline-primary" :to="{ name: 'sections' }">
                  Перейти к разделам
                </b-button>
                <b-button variant="success" :to="{ name: 'admin' }">
                  {{ $t('admin_panel') }}
                </b-button>
              </div>
            </b-col>

            <b-col class="text-center">
              <b-img style="max-height: 300px;" src="/main_bg.png" fluid />
            </b-col>
          </b-row>
        </div>
      </template>
    </b-jumbotron>

    <div class="container my-3">
      <div>
        <h2 class="mb-4">
          <b>Содержание</b>
        </h2>

        <p>
          Здесь вы можете научиться решать математические задачи, начиная с простых и заканчивая более продвинутыми примерами. <br />
          Мы сосредоточимся на самом умении решать, изредка добавляя теоретические заметки и вставки.
        </p>

        <div>
          <div v-for="(section, sectionIndex) in items" :key="section.id">
            <div class="mb-3 textbook-section-title">
              <router-link :to="{ name: 'sections.detail', params: { id: section.id } }">
                {{ section.title }}
              </router-link>
            </div>

            <ul class="textbook-list numbered mb-3 list-unstyled">
              <li v-for="(task, taskIndex) in section.tasks"
                  :key="task.id"
              >
                <span class="incrementor">{{ sectionIndex + 1 }}.{{ taskIndex + 1 }}</span>
                <router-link :to="{ name: 'tasks.detail', params: { id: task.id } }">
                  {{ task.title }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container my-3 mt-4">
      <div>
        <h2 class="mb-4">
          <b>Интересное</b>
        </h2>

        <p></p>
      </div>

      <b-card-group deck>
        <b-card bg-variant="primary" text-variant="white" header="Приведенные квадратные уравнения" header-class="font-weight-bold">
          <b-card-text>Уровень: Простой</b-card-text>

          <b-button to="/tasks/1" variant="outline-light">Перейти</b-button>
        </b-card>

        <b-card bg-variant="secondary" text-variant="white" header="Системы линейных уравнений" header-class="font-weight-bold">
          <b-card-text>Уровень: Простой</b-card-text>

          <b-button to="/tasks/6" variant="outline-light">Перейти</b-button>
        </b-card>

        <b-card bg-variant="success" text-variant="white" header="Пределы" header-class="font-weight-bold">
          <b-card-text>Уровень: Простой</b-card-text>

          <b-button to="/tasks/8" variant="outline-light">Перейти</b-button>
        </b-card>
      </b-card-group>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  middleware: 'auth',

  data: () => ({
    isLoading: false,
    items: []
  }),

  metaInfo () {
    return { title: this.$t('home') }
  },

  async mounted () {
    this.isLoading = true

    try {
      const { data } = await axios.get('/api/sections/tasks')

      this.items = data.data
    } catch (e) {
      // TODO: Обработать ошибку
    }

    this.isLoading = false
  }
}
</script>
